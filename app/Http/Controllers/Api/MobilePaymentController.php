<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Tenant;

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

        return response()->json([
            'status' => 'success',
            'message' => 'Bukti pembayaran berhasil diupload',
            'invoice' => $payment->invoice_number
        ]);
    }
}