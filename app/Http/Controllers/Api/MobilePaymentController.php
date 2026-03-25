<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Tenant;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Http;

class MobilePaymentController extends Controller
{
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
            return response()->json(['message' => 'Unauthorized. Hanya Admin yang bisa verifikasi.'], 403);
        }

        $payment = Payment::with('tenant.user')->findOrFail($id);

        if ($payment->status === 'verified') {
            return response()->json(['message' => 'Pembayaran ini sudah diverifikasi sebelumnya.'], 400);
        }

        $payment->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        LogHelper::log(
            'VERIFY_PAYMENT', 
            "Admin {$request->user()->name} memverifikasi pembayaran #{$payment->invoice_number} dari {$payment->tenant->name}",
            $payment,
            ['amount' => $payment->total, 'status' => 'verified']
        );

        $tenantUser = $payment->tenant->user;
        if ($tenantUser && $tenantUser->expo_push_token) {
            Http::post('https://exp.host/--/api/v2/push/send', [
                'to' => $tenantUser->expo_push_token,
                'title' => 'Pembayaran Diterima! ✅',
                'body' => "Pembayaran sebesar Rp " . number_format($payment->total, 0, ',', '.') . " telah diverifikasi. Terima kasih!",
                'data' => ['type' => 'payment_verified', 'id' => $payment->id],
                'sound' => 'default',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran berhasil diverifikasi dan log dicatat.'
        ]);
    }
}