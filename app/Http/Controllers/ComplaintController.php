<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Complaint;
use App\Models\Tenant;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ComplaintController extends Controller
{
    public function __construct(
        private PushNotificationService $pushNotification,
    ) {}

    public function index()
    {
        $complaints = Complaint::with(['tenant', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        $tenants = Tenant::where('status', 'active')->with('room')->get();

        return view('complaints.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'required|in:facility,cleanliness,security,other',
                'priority' => 'required|in:low,medium,high',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $tenant = Tenant::find($validated['tenant_id']);
            $validated['room_id'] = $tenant->room_id;

            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('complaints', 'public');
                }
                $validated['images'] = $imagePaths;
            }

            Complaint::create($validated);

            return redirect()->route('complaints.index')
                ->with('success', 'Keluhan berhasil ditambahkan');
        } catch (Throwable $e) {
            LogHelper::logError(
                'CREATE_COMPLAINT_FAILED',
                'Gagal menambah keluhan dari web',
                $e,
                ['title' => $request->title, 'tenant_id' => $request->tenant_id]
            );

            return back()->with('error', 'Gagal menambah keluhan')->withInput();
        }
    }

    public function show($id)
    {
        $complaint = Complaint::with(['images', 'tenant', 'room'])->where('uuid', $id)->firstOrFail();

        return view('complaints.show', compact('complaint'));
    }

    public function edit(Complaint $complaint)
    {
        $tenants = Tenant::where('status', 'active')->with('room')->get();

        return view('complaints.edit', compact('complaint', 'tenants'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        try {
            $validated = $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'required|in:facility,cleanliness,security,other',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:open,in_progress,resolved,closed',
                'response' => 'nullable|string',
                'resolved_date' => 'nullable|date',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
                'remove_images' => 'nullable|array',
            ]);

            $tenant = Tenant::find($validated['tenant_id']);
            $validated['room_id'] = $tenant->room_id;
            $newStatus = $validated['status'];

            if ($request->has('remove_images')) {
                $currentImages = $complaint->images ?? [];
                foreach ($request->remove_images as $imageToRemove) {
                    if (($key = array_search($imageToRemove, $currentImages)) !== false) {
                        Storage::disk('public')->delete($imageToRemove);
                        unset($currentImages[$key]);
                    }
                }
                $validated['images'] = array_values($currentImages);
            }

            if ($request->hasFile('images')) {
                $currentImages = $validated['images'] ?? $complaint->images ?? [];

                if (count($currentImages) + count($request->file('images')) > 5) {
                    return back()->with('error', 'Maksimal 5 foto!');
                }

                foreach ($request->file('images') as $image) {
                    $currentImages[] = $image->store('complaints', 'public');
                }
                $validated['images'] = $currentImages;
            }

            if ($newStatus == 'resolved' || $newStatus == 'closed') {
                $validated['resolved_date'] = now();
            } else {
                $validated['resolved_date'] = null;
            }

            $complaint->update($validated);

            return redirect()->route('complaints.index')
                ->with('success', 'Keluhan berhasil diupdate');
        } catch (Throwable $e) {
            LogHelper::logError(
                'UPDATE_COMPLAINT_FAILED',
                "Gagal update keluhan #{$complaint->id}",
                $e
            );

            return back()->with('error', 'Gagal mengupdate keluhan')->withInput();
        }
    }

    public function destroy(Complaint $complaint)
    {
        try {
            if ($complaint->images) {
                foreach ($complaint->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $complaint->delete();

            return redirect()->route('complaints.index')
                ->with('success', 'Keluhan berhasil dihapus');
        } catch (Throwable $e) {
            LogHelper::logError(
                'DELETE_COMPLAINT_FAILED',
                "Gagal hapus keluhan #{$complaint->id}",
                $e
            );

            return back()->with('error', 'Gagal menghapus keluhan');
        }
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:open,in_progress,resolved,closed',
                'response' => 'nullable|string',
            ]);

            if ($validated['status'] == 'resolved' || $validated['status'] == 'closed') {
                $validated['resolved_date'] = now();
            }

            $complaint->update($validated);

            $tenant = $complaint->tenant;

            if ($tenant && $tenant->expo_push_token) {
                $this->pushNotification->sendComplaintUpdate(
                    $tenant->expo_push_token,
                    $complaint->title,
                    $validated['status'],
                    $validated['response'] ?? null,
                    $complaint->id
                );
            }

            LogHelper::log(
                'RESPOND_COMPLAINT',
                "Admin merespon keluhan #{$complaint->id}: {$complaint->title}",
                $complaint,
                ['status_baru' => $request->status, 'respon' => $request->response]
            );

            return redirect()->back()
                ->with('success', 'Status keluhan berhasil diupdate & Notifikasi dikirim');
        } catch (Throwable $e) {
            LogHelper::logError(
                'RESPOND_COMPLAINT_FAILED',
                "Gagal update status keluhan #{$complaint->id}",
                $e
            );

            return back()->with('error', 'Gagal mengupdate status keluhan');
        }
    }
}
