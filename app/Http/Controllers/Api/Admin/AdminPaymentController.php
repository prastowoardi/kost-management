<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Services\PaymentService;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AdminPaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private PushNotificationService $pushNotification,
    ) {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|string',
            'payment_date' => 'required|date',
            'period_month' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,e-wallet',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $tenant = Tenant::with('room')->where('uuid', $request->tenant_id)->firstOrFail();

            $request->merge([
                'period_month' => Payment::normalizePeriodMonth($request->period_month),
            ]);

            $payment = DB::transaction(function () use ($request, $tenant) {
                [$periodStart, $periodEnd] = Payment::periodRange($request->period_month);

                $agg = Payment::lockForUpdate()
                    ->where('tenant_id', $tenant->id)
                    ->whereBetween('period_month', [$periodStart, $periodEnd])
                    ->where('status', 'paid')
                    ->whereNull('deleted_at')
                    ->selectRaw('COALESCE(SUM(total), 0) AS total_paid, COUNT(*) AS cnt')
                    ->first();

                $paidSoFar = (float) $agg->total_paid;
                $paidCount = (int) $agg->cnt;

                $price = (float) $tenant->room->price;
                $remaining = $price - $paidSoFar;

                if ($remaining <= 0) {
                    throw new \InvalidArgumentException(
                        "Periode ini sudah lunas (total bayar Rp ".number_format($paidSoFar, 0, ',', '.').")."
                    );
                }

                $newTotal = (float) $request->amount + (float) ($request->late_fee ?? 0);

                if ($newTotal > $remaining) {
                    throw new \InvalidArgumentException(
                        "Jumlah melebihi sisa tagihan (sisa Rp ".number_format($remaining, 0, ',', '.').")."
                    );
                }

                $notes = filled($request->notes)
                    ? $request->notes
                    : Payment::splitPaymentNote($paidCount + 1, $remaining - $newTotal);

                return Payment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $tenant->room_id,
                    'payment_date' => $request->payment_date,
                    'period_month' => $request->period_month,
                    'amount' => $request->amount,
                    'late_fee' => $request->late_fee ?? 0,
                    'total' => $newTotal,
                    'payment_method' => $request->payment_method,
                    'status' => 'paid',
                    'notes' => $notes,
                ]);
            });

            $payment->load(['tenant.user', 'room']);

            $this->paymentService->createFinanceRecord($payment);

            $tenantUser = $tenant->user;
            if ($tenantUser && $tenantUser->expo_push_token) {
                $sent = $this->pushNotification->sendPaymentReceipt(
                    $tenantUser->expo_push_token,
                    $tenant->name,
                    $payment->invoice_number,
                    $payment->total,
                );
            }

            LogHelper::log(
                'CREATE_PAYMENT_API',
                "Mencatat pembayaran {$payment->invoice_number} untuk {$tenant->name}",
                $payment
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dicatat!',
                'data' => [
                    'payment' => $payment,
                    'invoice' => $payment->invoice_number,
                    'total' => $payment->total,
                    'tenant_name' => $tenant->name,
                    'room_number' => $tenant->room?->room_number,
                    'period' => Carbon::parse($payment->period_month)->translatedFormat('F Y'),
                    'payment_date' => $payment->payment_date,
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            LogHelper::logError('CREATE_PAYMENT_API_FAILED', 'Gagal catat pembayaran', $e);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencatat pembayaran.',
            ], 500);
        }
    }

    public function show($uuid)
    {
        $payment = Payment::where('uuid', $uuid)
            ->with(['tenant.user', 'room'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $payment,
        ]);
    }
}
