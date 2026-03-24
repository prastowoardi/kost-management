<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class AdminComplaintController extends Controller
{
    public function index()
    {
        
        $complaints = Complaint::with(['tenant.user', 'room', 'images'])->latest()->get();
        return response()->json(['success' => true, 'data' => $complaints]);
    }

    public function show($id)
    {
        $complaint = Complaint::with(['tenant', 'room', 'images'])->find($id);

        if (!$complaint) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $complaint]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'response' => 'nullable|string'
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->update([
            'status' => $request->status,
            'response' => $request->response
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diperbarui',
            'data' => $complaint
        ]);
    }
}