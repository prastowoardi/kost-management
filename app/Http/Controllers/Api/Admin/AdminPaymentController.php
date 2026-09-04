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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
            'receipt_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $validated = $validator->validated();
            $tenant = Tenant::with('room')->where('uuid', $validated['tenant_id'])->firstOrFail();

            $receiptPath = null;
            $proofPath = null;
            if ($request->hasFile('receipt_file')) {
                $receiptPath = $request->file('receipt_file')->store('receipts');
            }
            if ($request->hasFile('proof_of_payment')) {
                $proofPath = $request->file('proof_of_payment')->store('proofs');
            }

            unset($validated['receipt_file'], $validated['proof_of_payment']);

            $payment = $this->paymentService->createInstallmentPayment($tenant, $validated);

            $updates = [];
            if ($receiptPath) {
                $updates['receipt_file'] = $receiptPath;
            }
            if ($proofPath) {
                $updates['proof_of_payment'] = $proofPath;
            }
            if (! empty($updates)) {
                $payment->update($updates);
            }

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
                \Illuminate\Support\Facades\Log::info('NOTIF: send result', ['success' => $sent ? 'yes' : 'no']);
            } else {
                \Illuminate\Support\Facades\Log::info('NOTIF: skipped - no user or no push token');
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
        } catch (ValidationException $e) {
            LogHelper::logError(
                'CREATE_PAYMENT_API_FAILED',
                'Validasi gagal saat catat pembayaran: '.collect($e->errors())->flatten()->first(),
                $e
            );

            return response()->json([
                'status' => 'error',
                'message' => collect($e->errors())->flatten()->first() ?? 'Validasi gagal.',
                'errors' => $e->errors(),
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
