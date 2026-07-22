<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class AdminComplaintController extends Controller
{
    public function __construct(
        private PushNotificationService $pushNotification,
    ) {}

    public function index()
    {
        $complaints = Complaint::with(['tenant.user', 'room', 'images'])->latest()->get();

        return response()->json(['success' => true, 'data' => $complaints]);
    }

    public function show($id)
    {
        $complaint = Complaint::with(['tenant', 'room', 'images'])->find($id);

        if (! $complaint) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $complaint]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'response' => 'nullable|string',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->update([
            'status' => $request->status,
            'response' => $request->response,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui',
            'data' => $complaint,
        ]);
    }

    public function respond(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'response' => 'nullable|string',
        ]);

        $complaint = Complaint::with('tenant.user')->findOrFail($id);
        $complaint->update([
            'status' => $request->status,
            'response' => $request->response,
        ]);

        $user = $complaint->tenant->user;

        if ($user && $user->expo_push_token) {
            $statusLabel = [
                'in_progress' => 'sedang diproses',
                'resolved' => 'telah selesai',
            ][$request->status] ?? 'diperbarui';

            $this->pushNotification->send(
                $user->expo_push_token,
                'Update Laporan: '.$complaint->title,
                "Laporan kamu $statusLabel. ".($request->response ? 'Pesan: '.$request->response : '')
            );
        }

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }
}
