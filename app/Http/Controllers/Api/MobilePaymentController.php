<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MobilePaymentController extends Controller
{
    public function __construct(
        private PushNotificationService $pushNotification,
    ) {}

    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return response()->json([], 200);
        }

        $payments = Payment::where('tenant_id', $tenant->id)
            ->whereIn('status', ['paid', 'overdue'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }

    public function getHistory(Request $request)
    {
        $user = $request->user();

        $tenant = Tenant::where('user_id', $user->id)->first();

        if (! $tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tenant tidak ditemukan',
            ], 404);
        }

        $payments = Payment::where('tenant_id', $tenant->id)
            ->with(['room'])
            ->whereIn('status', ['paid', 'overdue'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments,
        ]);
    }

    public function uploadProof(Request $request)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();
        $tenant = Tenant::where('user_id', $user->id)->first();

        if (! $tenant) {
            LogHelper::logError(
                'UPLOAD_RECEIPT_FAILED',
                "Gagal upload bukti bayar: tenant tidak ditemukan"
            );

            return response()->json(['message' => 'Data tenant tidak ditemukan'], 404);
        }

        try {
            $path = $request->file('proof_image')->store('receipts', 'public');

            $payment = DB::transaction(function () use ($tenant, $path) {
                return Payment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $tenant->room_id,
                    'payment_date' => now(),
                    'period_month' => now()->startOfMonth(),
                    'amount' => $tenant->room->price ?? 0,
                    'total' => $tenant->room->price ?? 0,
                    'status' => 'pending',
                    'payment_method' => 'transfer',
                    'receipt_file' => $path,
                    'notes' => 'Pembayaran via Mobile',
                ]);
            });

            LogHelper::log(
                'UPLOAD_RECEIPT',
                "Mengunggah bukti bayar untuk tagihan bulan ".now()->format('F'),
                $payment
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload',
                'invoice' => $payment->invoice_number ?? $payment->id,
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'UPLOAD_RECEIPT_FAILED',
                "Gagal upload bukti bayar",
                $e
            );

            return response()->json(['message' => 'Gagal mengupload bukti bayar'], 500);
        }
    }

    public function verifyPayment(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $payment = Payment::with(['tenant.user', 'room'])->where('uuid', $id)->firstOrFail();

        $newStatus = ($request->status === 'rejected') ? 'overdue' : 'paid';
        $logAction = ($newStatus === 'paid') ? 'MENERIMA' : 'MENOLAK';

        if ($payment->status === 'paid' || $payment->status === 'overdue') {
            return response()->json(['message' => 'Sudah diverifikasi sebelumnya.'], 400);
        }

        try {
            $payment->update([
                'status' => $newStatus,
                'verified_at' => now(),
            ]);

            LogHelper::log(
                'VERIFY_PAYMENT',
                "{$logAction} pembayaran #{$payment->invoice_number}",
                $payment,
                ['amount' => $payment->total, 'status' => $newStatus]
            );

            $tenantUser = $payment->tenant->user ?? null;

            if ($tenantUser && $tenantUser->expo_push_token) {
                if ($newStatus === 'paid') {
                    $this->pushNotification->sendPaymentVerified(
                        $tenantUser->expo_push_token,
                        $tenantUser->name,
                        $payment->total,
                        $payment->id
                    );
                } else {
                    $this->pushNotification->sendPaymentRejected(
                        $tenantUser->expo_push_token,
                        $tenantUser->name,
                        $payment->total,
                        $payment->id
                    );
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to '.$newStatus,
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'VERIFY_PAYMENT_FAILED',
                "Gagal verifikasi pembayaran #{$payment->invoice_number}",
                $e,
                ['payment_id' => $payment->id, 'request_status' => $request->status]
            );

            return response()->json(['message' => 'Gagal memverifikasi pembayaran'], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $payment = Payment::with(['tenant.user', 'room'])
            ->where('uuid', $id)
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan.'], 404);
        }

        if ($request->user()->role !== 'admin' && $payment->tenant->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($payment);
    }
}
