<?php

namespace App\Services;

use App\Models\Finance;
use App\Models\Payment;
use App\Models\Tenant;
use Carbon\Carbon;

class PaymentService
{
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
