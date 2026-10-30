<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintImage;
use Illuminate\Http\Request;
use Throwable;

class MobileComplaintController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $complaints = Complaint::where('tenant_id', $tenant->id)
            ->with('images')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $complaints,
        ]);
    }

    public function store(Request $request)
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Data profil tenant tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|in:facility,cleanliness,security,other',
            'priority' => 'nullable|in:low,medium,high',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            $complaint = new Complaint;
            $complaint->tenant_id = $tenant->id;
            $complaint->room_id = $tenant->room_id;
            $complaint->title = $validated['title'];
            $complaint->description = $validated['description'];
            $complaint->category = $validated['category'] ?? 'other';
            $complaint->priority = $validated['priority'] ?? 'medium';
            $complaint->status = 'open';
            $complaint->save();

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('complaints');

                    $img = new ComplaintImage;
                    $img->complaint_id = $complaint->id;
                    $img->image_path = $path;
                    $img->path = $path;
                    $img->save();
                }
            }

            LogHelper::log('CREATE_COMPLAINT_MOBILE', "Membuat laporan: {$complaint->title}", $complaint);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'uuid' => $complaint->uuid,
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'CREATE_COMPLAINT_FAILED',
                'Gagal membuat laporan',
                $e,
                ['title' => $request->title]
            );

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan',
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $complaint = Complaint::with('images')->where('uuid', $id)->first();

        if (! $complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
            ], 404);
        }

        $user = $request->user();
        $isOwner = $user->tenant && ($user->tenant->id === $complaint->tenant_id);
        $isAdminOrStaff = $user->isAdminOrStaff();

        if (! $isOwner && ! $isAdminOrStaff) {
            LogHelper::logError('FORBIDDEN_COMPLAINT_ACCESS', 'Percobaan akses laporan keluhan bukan miliknya.', null, [
                'user_id' => $user->id,
                'complaint_uuid' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Anda tidak memiliki akses ke laporan ini.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $complaint,
        ]);
    }
}
