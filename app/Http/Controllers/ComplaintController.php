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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $tenant = Tenant::find($validated['tenant_id']);
        $validated['room_id'] = $tenant->room_id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('complaints', 'public');
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $tenant = Tenant::find($validated['tenant_id']);
        $validated['room_id'] = $tenant->room_id;

        if ($request->hasFile('image')) {
            if ($complaint->image) {
                Storage::disk('public')->delete($complaint->image);
            }
            $validated['image'] = $request->file('image')->store('complaints', 'public');
        }

        $complaint->update($validated);

        return redirect()->route('complaints.index')
                        ->with('success', 'Keluhan berhasil diupdate');
    }

    public function destroy(Complaint $complaint)
    {
        if ($complaint->image) {
            Storage::disk('public')->delete($complaint->image);
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