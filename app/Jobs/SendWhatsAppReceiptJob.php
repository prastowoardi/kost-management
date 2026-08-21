<?php

namespace App\Jobs;

use App\Models\BroadcastLog;
use App\Models\Payment;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Render kwitansi pembayaran menjadi gambar (via gateway + Puppeteer)
 * lalu kirim ke penghuni. Dijalankan asinkron karena proses render
 * bisa memakan waktu puluhan detik.
 */
class SendWhatsAppReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $paymentId,
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $payment = Payment::with(['tenant', 'room'])->find($this->paymentId);

        if (! $payment || ! $payment->tenant?->phone) {
            return;
        }

        $tenant = $payment->tenant;
        $htmlContent = view('payments.receipt', compact('payment'))->render();
        $caption = $whatsapp->getPaymentReceiptCaption($tenant->name);

        $sent = $whatsapp->sendImage($tenant->phone, $htmlContent, $caption);

        BroadcastLog::create([
            'broadcast_id' => null,
            'tenant_name' => $tenant->name,
            'phone' => $tenant->phone,
            'status' => $sent ? 'success' : 'failed',
            'error_message' => $sent ? null : 'Gagal kirim gambar WA',
        ]);
    }
}
