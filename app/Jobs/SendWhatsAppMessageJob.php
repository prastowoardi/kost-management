<?php

namespace App\Jobs;

use App\Models\Broadcast;
use App\Models\BroadcastLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Kirim pesan teks WhatsApp secara asinkron agar request HTTP tidak
 * menunggu respons gateway (yang bisa lambat/down).
 */
class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $phone,
        public string $message,
        public ?int $broadcastId = null,
        public ?string $tenantName = null,
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $sent = $whatsapp->sendMessage($this->phone, $this->message);

        if ($this->broadcastId !== null) {
            BroadcastLog::create([
                'broadcast_id' => $this->broadcastId,
                'tenant_name' => $this->tenantName ?? '',
                'phone' => $this->phone,
                'status' => $sent ? 'success' : 'failed',
                'error_message' => $sent ? null : 'Gagal kirim pesan',
            ]);

            // Update counter broadcast secara atomik
            Broadcast::where('id', $this->broadcastId)->increment(
                $sent ? 'total_success' : 'total_failed'
            );
        }
    }
}
