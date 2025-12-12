<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
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
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:facility,cleanliness,security,other',
            'priority' => 'required|in:low,medium,high',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $tenant = Tenant::find($validated['tenant_id']);
        $validated['room_id'] = $tenant->room_id;

        // Handle multiple images
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
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['tenant', 'room']);
        return view('complaints.show', compact('complaint'));
    }

    public function edit(Complaint $complaint)
    {
        $tenants = Tenant::where('status', 'active')->with('room')->get();
        return view('complaints.edit', compact('complaint', 'tenants'));
    }

    public function update(Request $request, Complaint $complaint)
    {
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
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'remove_images' => 'nullable|array' // IDs gambar yang akan dihapus
        ]);

        $tenant = Tenant::find($validated['tenant_id']);
        $validated['room_id'] = $tenant->room_id;

        // Handle remove images
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

        // Handle new images
        if ($request->hasFile('images')) {
            $currentImages = $validated['images'] ?? $complaint->images ?? [];
            
            // Check total images limit
            if (count($currentImages) + count($request->file('images')) > 5) {
                return back()->with('error', 'Maksimal 5 foto!');
            }
            
            foreach ($request->file('images') as $image) {
                $currentImages[] = $image->store('complaints', 'public');
            }
            $validated['images'] = $currentImages;
        }

        $complaint->update($validated);

        return redirect()->route('complaints.index')
                        ->with('success', 'Keluhan berhasil diupdate');
    }

    public function destroy(Complaint $complaint)
    {
        // Delete all images
        if ($complaint->images) {
            foreach ($complaint->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $complaint->delete();

        return redirect()->route('complaints.index')
                        ->with('success', 'Keluhan berhasil dihapus');
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'response' => 'nullable|string'
        ]);

        if ($validated['status'] == 'resolved' || $validated['status'] == 'closed') {
            $validated['resolved_date'] = now();
        }

        $complaint->update($validated);

        return redirect()->back()
                        ->with('success', 'Status keluhan berhasil diupdate');
    }
}