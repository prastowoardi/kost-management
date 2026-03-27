<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Tenant;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MobilePaymentController extends Controller
{
    public function getHistory(Request $request)
    {
        $user = $request->user();
        
        $tenant = Tenant::where('user_id', $user->id)->first();

        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tenant tidak ditemukan'
            ], 404);
        }

        $payments = Payment::with('room')
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }

    public function uploadProof(Request $request)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();
        $tenant = Tenant::where('user_id', $user->id)->first();

        if (!$tenant) {
            return response()->json(['message' => 'Data tenant tidak ditemukan'], 404);
        }

        $path = $request->file('proof_image')->store('receipts', 'public');

        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'payment_date' => now(),
            'period_month' => now()->startOfMonth(),
            'amount' => $tenant->room->price ?? 0,
            'total' => $tenant->room->price ?? 0,
            'status' => 'pending',
            'payment_method' => 'transfer',
            'receipt_file' => $path,
            'notes' => 'Pembayaran via Mobile'
        ]);

        LogHelper::log(
            'UPLOAD_RECEIPT', 
            "Tenant {$user->name} mengunggah bukti bayar untuk tagihan bulan " . now()->format('F'),
            $payment
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Bukti pembayaran berhasil diupload',
            'invoice' => $payment->invoice_number ?? $payment->id
        ]);
    }

    public function verifyPayment(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $payment = Payment::with(['tenant.user', 'room'])->findOrFail($id);

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
                "Admin {$request->user()->name} {$logAction} pembayaran #{$payment->invoice_number}",
                $payment,
                ['amount' => $payment->total, 'status' => $newStatus]
            );

            $tenantUser = $payment->tenant->user ?? null;
            
            Log::info("Mengecek Token untuk User ID: " . ($tenantUser->id ?? 'Kosong'));
            Log::info("Token: " . ($tenantUser->expo_push_token ?? 'NULL'));

            if ($tenantUser && $tenantUser->expo_push_token) {
                $isPaid = ($newStatus === 'paid');

                $response = Http::post('https://exp.host/--/api/v2/push/send', [
                    'to' => $tenantUser->expo_push_token,
                    'title' => $isPaid ? 'Pembayaran Diterima! ✅' : 'Pembayaran Ditolak! ❌',
                    'body' => $isPaid 
                        ? "Pembayaran sebesar Rp " . number_format($payment->total, 0, ',', '.') . " telah diverifikasi."
                        : "Maaf, pembayaran Rp " . number_format($payment->total, 0, ',', '.') . " ditolak Admin. Silakan hubungi pengelola.",
                    'data' => [
                        'type' => $isPaid ? 'payment_verified' : 'payment_rejected', 
                        'id' => (int)$payment->id
                    ],
                    'sound' => 'default',
                ]);
                
                Log::info("Respon Expo: " . $response->body());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated to ' . $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error("Verify Error: " . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $payment = Payment::with(['tenant.user', 'room'])
            ->where('id', $id)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan.'], 404);
        }

        if ($request->user()->role !== 'admin' && $payment->tenant->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($payment);
    }
}