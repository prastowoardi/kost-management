<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentPageController extends Controller
{
    public function show($hash)
    {
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
            $path = $request->file('proof')->store('proofs');

            $payment->update([
                'proof_of_payment' => basename($path),
                'status' => 'pending',
            ]);

            $this->notifyAdmin($payment);

            return back()->with('success', 'Bukti berhasil diunggah!');
        }
    }

    private function notifyAdmin($payment)
    {
        $adminPhone = config('services.whatsapp.admin_phone', '');
        if (! $adminPhone) {
            return;
        }

        \App\Jobs\SendWhatsAppMessageJob::dispatch(
            $adminPhone,
            "📩 *Bukti Bayar Masuk!*\n\n".
            "Dari: {$payment->tenant->name}\n".
            "Kamar: {$payment->tenant->room->room_number}\n".
            'Total: Rp '.number_format($payment->total, 0, ',', '.')
        );
    }
}
