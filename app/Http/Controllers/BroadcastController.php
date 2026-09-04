<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Broadcast;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Throwable;

class BroadcastController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsapp,
    ) {}

    public function index()
    {
        return view('broadcast.index');
    }

    public function send(Request $request)
    {
        try {
            $request->validate(['message' => 'required']);

            $tenants = Tenant::whereNotNull('phone')
                ->where('status', 'active')
                ->get();

            if ($tenants->isEmpty()) {
                return back()->withErrors(['msg' => 'Tidak ada Penghuni aktif yang ditemukan.']);
            }

            $broadcast = Broadcast::create([
                'message' => $request->message,
            ]);

            // Kirim massal via queue: satu job per penghuni, request HTTP
            // langsung selesai tanpa menunggu gateway satu per satu.
            foreach ($tenants as $tenant) {
                \App\Jobs\SendWhatsAppMessageJob::dispatch(
                    $tenant->phone,
                    $request->message,
                    $broadcast->id,
                    $tenant->name,
                );
            }

            LogHelper::log('SEND_BROADCAST', "Broadcast mengantre dikirim ke {$tenants->count()} penghuni", $broadcast, [
                'total_tenant' => $tenants->count(),
            ]);

            return back()->with('status', 'Broadcast mengantre dikirim ke '.$tenants->count().' penghuni!');
        } catch (Throwable $e) {
            LogHelper::logError(
                'BROADCAST_FAILED',
                'Gagal mengirim broadcast',
                $e
            );

            return back()->withErrors(['msg' => 'Gagal mengirim broadcast']);
        }
    }

    public function history()
    {
        $history = Broadcast::with('logs')->latest()->paginate(10);

        return view('broadcast.history', compact('history'));
    }

    public function showChat($id)
    {
        $tenant = Tenant::with('room')->where('uuid', $id)->firstOrFail();
        $chats = [];
        $error = null;

        $chats = $this->whatsapp->getChats($tenant->phone);
        if (empty($chats)) {
            $error = 'Gateway Offline atau tidak ada chat';
        }

        return view('broadcast.chat-view', compact('tenant', 'chats', 'error'));
    }

    public function sendPersonal(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required',
                'phone' => 'required',
            ]);

            $sent = $this->whatsapp->sendMessage($request->phone, $request->message);

            if ($sent) {
                LogHelper::log('SEND_PERSONAL_CHAT', "Mengirim pesan personal ke {$request->phone}");

                return back()->with('status', 'Pesan terkirim!');
            }

            LogHelper::logError('SEND_PERSONAL_CHAT_FAILED', "Gagal kirim pesan personal ke {$request->phone}");

            return back()->withErrors(['msg' => 'Gagal terhubung ke Gateway WA.']);
        } catch (Throwable $e) {
            LogHelper::logError('SEND_PERSONAL_CHAT_FAILED', 'Gagal kirim pesan personal', $e);

            return back()->withErrors(['msg' => 'Gagal mengirim pesan.']);
        }
    }
}
