<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\BroadcastLog;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

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

        $success = 0;
        $failed = 0;

        foreach ($tenants as $tenant) {
            $errorMsg = null;
            $sent = $this->whatsapp->sendMessage($tenant->phone, $request->message);

            if ($sent) {
                $status = 'success';
                $success++;
            } else {
                $status = 'failed';
                $failed++;
                $errorMsg = 'Gagal kirim pesan';
            }

            BroadcastLog::create([
                'broadcast_id' => $broadcast->id,
                'tenant_name' => $tenant->name,
                'phone' => $tenant->phone,
                'status' => $status,
                'error_message' => $errorMsg,
            ]);
        }

        $broadcast->update([
            'total_success' => $success,
            'total_failed' => $failed,
        ]);

        return back()->with('status', 'Broadcast terkirim ke '.$success.' penghuni!');
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
        $request->validate([
            'message' => 'required',
            'phone' => 'required',
        ]);

        $sent = $this->whatsapp->sendMessage($request->phone, $request->message);

        if ($sent) {
            return back()->with('status', 'Pesan terkirim!');
        }

        return back()->withErrors(['msg' => 'Gagal terhubung ke Gateway WA.']);
    }
}
