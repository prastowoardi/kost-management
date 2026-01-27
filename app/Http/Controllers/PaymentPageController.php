<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PaymentPageController extends Controller
{
    public function show($hash)
    {
        // Decode ID atau cari berdasarkan kolom unik (misal id yang di-encrypt)
        $payment = Payment::with(['tenant.room'])->where('id', decrypt($hash))->firstOrFail();
        
        if ($payment->status === 'paid') {
            return view('payments.success', compact('payment'));
        }

        return view('payments.public_checkout', compact('payment', 'hash'));
    }

    public function upload(Request $request, $hash)
    {
        $payment = Payment::findOrFail(decrypt($hash));
        
        $request->validate([
            'proof' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('public/proofs');
            
            $payment->update([
                'proof_of_payment' => basename($path),
                'status' => 'pending'
            ]);

            // Kirim Notif WA ke Admin (Gateway Node.js kamu)
            $this->notifyAdmin($payment);

            return back()->with('success', 'Bukti berhasil diunggah!');
        }
    }

    private function notifyAdmin($payment) {
        Http::post('http://localhost:3000/send-message', [
            'number' => '628xxx', // No HP kamu
            'message' => "📩 *Bukti Bayar Masuk!*\n\nDari: {$payment->tenant->name}\nKamar: {$payment->tenant->room->room_number}\nTotal: Rp " . number_format($payment->total)
        ]);
    }
}