<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('room')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        $availableRooms = Room::where('status', 'available')->get();
        return view('tenants.create', compact('availableRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|unique:tenants',
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'emergency_contact' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant = Tenant::create($validated);
        
        // Update room status
        Room::find($validated['room_id'])->update(['status' => 'occupied']);

        return redirect()->route('tenants.index')
                        ->with('success', 'Penghuni berhasil ditambahkan');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['room', 'payments', 'complaints']);
        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $rooms = Room::where('status', 'available')
                    ->orWhere('id', $tenant->room_id)
                    ->get();
        
        return view('tenants.edit', compact('tenant', 'rooms'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'id_card' => 'required|string|unique:tenants,id_card,' . $tenant->id,
            'address' => 'required|string',
            'entry_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'emergency_contact' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $oldRoomId = $tenant->room_id;

        if ($request->hasFile('photo')) {
            if ($tenant->photo) {
                Storage::disk('public')->delete($tenant->photo);
            }
            $validated['photo'] = $request->file('photo')->store('tenants', 'public');
        }

        $tenant->update($validated);

        if ($tenant->status == 'inactive') {
            Room::where('id', $tenant->room_id)->update(['status' => 'available']);
        }
        else {
            if ($oldRoomId != $validated['room_id']) {
                Room::where('id', $oldRoomId)->update(['status' => 'available']);
            }
            Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);
        }

        $tenant->update($validated);

        if ($tenant->status == 'inactive') {
            Room::where('id', $tenant->room_id)->update(['status' => 'available']);
        } else {
            if ($oldRoomId != $validated['room_id']) {
                Room::where('id', $oldRoomId)->update(['status' => 'available']);
            }
            Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);
        }

        return redirect()->route('tenants.index')
                ->with('success', 'Data penghuni berhasil diupdate');
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->photo) {
            Storage::disk('public')->delete($tenant->photo);
        }

        Room::find($tenant->room_id)->update(['status' => 'available']);
        
        $tenant->delete();

        return redirect()->route('tenants.index')
                        ->with('success', 'Penghuni berhasil dihapus');
    }

    public function updateStatus(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
            'exit_date' => 'required_if:status,inactive|nullable|date'
        ]);

        $tenant->update($validated);

        if ($validated['status'] == 'inactive') {
            Room::find($tenant->room_id)->update(['status' => 'available']);
        }

        return redirect()->back()
                        ->with('success', 'Status penghuni berhasil diupdate');
    }
}