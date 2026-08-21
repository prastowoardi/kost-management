<?php

namespace App\Services;

use App\Models\Finance;
use App\Models\Payment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Catat pembayaran (mendukung cicilan) untuk satu periode sewa.
     *
     * Aturan:
     * - Periode yang sudah lunas tidak bisa dibayar lagi.
     * - Total pembayaran (amount + late_fee) tidak boleh melebihi sisa tagihan.
     * - Jika notes kosong, dibuat otomatis: "Cicilan ke-N dari pembayaran
     *   bertahap — sisa Rp X." atau "— lunas." jika pelunasan.
     *
     * Melempar ValidationException (key 'period_month' / 'amount') bila melanggar.
     */
    public function createInstallmentPayment(Tenant $tenant, array $data): Payment
    {
        $period = Carbon::parse($data['period_month'])->startOfMonth()->toDateString();

        return DB::transaction(function () use ($tenant, $data, $period) {
            // Kunci baris periode ini agar perhitungan sisa tidak balapan
            // dengan pembayaran lain yang masuk bersamaan.
            $paidTotal = (float) Payment::where('tenant_id', $tenant->id)
                ->whereDate('period_month', $period)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->sum('total');

            $price = (float) ($tenant->room->price ?? 0);
            $remainingBefore = $price - $paidTotal;

            if ($remainingBefore <= 0) {
                throw ValidationException::withMessages([
                    'period_month' => 'Periode ini sudah lunas.',
                ]);
            }

            $amount = (float) $data['amount'];
            $lateFee = (float) ($data['late_fee'] ?? 0);
            $newTotal = $amount + $lateFee;

            // Pokok sewa tidak boleh melebihi sisa tagihan; denda bersifat
            // tambahan (penalti) sehingga boleh membuat total melampaui harga.
            if ($amount > $remainingBefore) {
                throw ValidationException::withMessages([
                    'amount' => 'Pembayaran melebihi sisa tagihan periode ini (sisa Rp '
                        .number_format($remainingBefore, 0, ',', '.').').',
                ]);
            }

            $installmentCount = Payment::where('tenant_id', $tenant->id)
                ->whereDate('period_month', $period)
                ->whereNull('deleted_at')
                ->count();

            $notes = $data['notes'] ?? null;
            if (empty($notes)) {
                $remainingAfter = max(0, $remainingBefore - $newTotal);
                $notes = $remainingAfter <= 0
                    ? 'Cicilan ke-'.($installmentCount + 1).' dari pembayaran bertahap — lunas.'
                    : 'Cicilan ke-'.($installmentCount + 1).' dari pembayaran bertahap — sisa Rp '
                        .number_format($remainingAfter, 0, ',', '.').'.';
            }

            return Payment::create([
                'tenant_id' => $tenant->id,
                'room_id' => $tenant->room_id,
                'payment_date' => $data['payment_date'],
                'period_month' => $period,
                'amount' => $amount,
                'late_fee' => $lateFee,
                'total' => $newTotal,
                'status' => 'paid',
                'payment_method' => $data['payment_method'],
                'notes' => $notes,
            ]);
        });
    }

    public function createFinanceRecord(Payment $payment): Finance
    {
        $payment->loadMissing(['room', 'tenant']);

        return Finance::create([
            'type' => 'income',
            'category' => 'Pembayaran Sewa',
            'transaction_date' => $payment->payment_date,
            'amount' => $payment->total,
            'description' => 'Pembayaran Sewa '.
                Carbon::parse($payment->period_month)->translatedFormat('F Y').
                ' - Kamar '.$payment->room->room_number.
                ' ('.$payment->tenant->name.')',
            'notes' => 'Dicatat otomatis dari pembayaran',
            'payment_id' => $payment->id,
        ]);
    }

    public function syncFinanceRecord(Payment $payment): ?Finance
    {
        $payment->loadMissing(['room', 'tenant']);
        $finance = Finance::where('payment_id', $payment->id)->first();

        $description = 'Pembayaran Sewa '.
            Carbon::parse($payment->period_month)->translatedFormat('F Y').
            ' - Kamar '.$payment->room->room_number.
            ' ('.$payment->tenant->name.')';

        if ($payment->status === 'paid') {
            if ($finance) {
                $finance->update([
                    'transaction_date' => $payment->payment_date,
                    'amount' => $payment->total,
                    'description' => $description,
                ]);

                return $finance;
            }

            return Finance::create([
                'type' => 'income',
                'category' => 'Pembayaran Sewa',
                'transaction_date' => $payment->payment_date,
                'amount' => $payment->total,
                'description' => $description,
                'notes' => 'Dicatat otomatis dari update pembayaran',
                'payment_id' => $payment->id,
            ]);
        }

        if ($finance) {
            $finance->delete();
        }

        return null;
    }

    public function deleteFinanceRecord(Payment $payment): void
    {
        Finance::where('payment_id', $payment->id)->delete();
    }

    public function updateRoomStatus(Payment $payment): void
    {
        // Depends on business logic - room status usually managed by tenant status
    }
}
