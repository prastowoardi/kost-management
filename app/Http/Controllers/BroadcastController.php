<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Broadcast;
use App\Models\BroadcastLog;
use Carbon\Carbon;

class BroadcastController extends Controller
{
    public function index()
    {
        return view('broadcast.index');
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required']);

        $broadcast = Broadcast::create([
            'message' => $request->message,
        ]);

        $tenants = Tenant::whereNotNull('phone')->get();
        $success = 0; $failed = 0;

        foreach ($tenants as $tenant) {
            $errorMsg = null;
            try {
                $response = Http::timeout(10)->post('http://127.0.0.1:3000/send-message', [
                    'number' => $tenant->phone,
                    'message' => $request->message,
                ]);

                if ($response->successful()) {
                    $status = 'success';
                    $success++;
                } else {
                    throw new \Exception("Response Gagal");
                }
            } catch (\Exception $e) {
                $status = 'failed';
                $failed++;
                $errorMsg = $e->getMessage();
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

        return back()->with('status', 'Broadcast di kirim dan disimpan di history!');
    }

    public function history()
    {
        $history = Broadcast::with('logs')->latest()->paginate(10);
        return view('broadcast.history', compact('history'));
    }
}