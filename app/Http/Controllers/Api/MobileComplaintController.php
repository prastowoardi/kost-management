<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Throwable;

class MobileComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::where('tenant_id', $request->user()->tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $complaints,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $complaint = new Complaint;
            $complaint->tenant_id = $request->user()->tenant->id;
            $complaint->room_id = $request->user()->tenant->room_id;
            $complaint->title = $request->title;
            $complaint->description = $request->description;
            $complaint->category = $request->category;
            $complaint->priority = $request->priority;
            $complaint->status = 'open';
            $complaint->save();

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('complaints', 'public');

                    $img = new \App\Models\ComplaintImage;
                    $img->complaint_id = $complaint->id;
                    $img->image_path = $path;
                    $img->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'uuid' => $complaint->uuid,
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'CREATE_COMPLAINT_FAILED',
                "User {$request->user()->email} gagal membuat laporan",
                $e,
                ['title' => $request->title]
            );

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan',
            ], 500);
        }
    }

    public function show($id)
    {
        $complaint = Complaint::with('images')->where('uuid', $id)->first();

        if (! $complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $complaint,
        ]);
    }
}
