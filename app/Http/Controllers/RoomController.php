<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Facility;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['activeTenant', 'facilities'])
                        ->orderBy('room_number')
                        ->paginate(10);
        
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        $facilities = Facility::where('type', 'room')->get();
        return view('rooms.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms',
            'type' => 'required|in:single,double,shared',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array'
        ]);

        $room = Room::create($validated);
        
        if ($request->has('facilities')) {
            $room->facilities()->attach($request->facilities);
        }

        return redirect()->route('rooms.index')
                        ->with('success', 'Kamar berhasil ditambahkan');
    }

    public function show(Room $room)
    {
        $room->load(['activeTenant', 'facilities', 'payments', 'complaints']);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $facilities = Facility::where('type', 'room')->get();
        $selectedFacilities = $room->facilities->pluck('id')->toArray();
        
        return view('rooms.edit', compact('room', 'facilities', 'selectedFacilities'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $room->id,
            'type' => 'required|in:single,double,shared',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array'
        ]);

        $room->update($validated);
        
        if ($request->has('facilities')) {
            $room->facilities()->sync($request->facilities);
        } else {
            $room->facilities()->detach();
        }

        return redirect()->route('rooms.index')
                        ->with('success', 'Kamar berhasil diupdate');
    }

    public function destroy(Room $room)
    {
        if ($room->activeTenant) {
            return redirect()->route('rooms.index')
                            ->with('error', 'Tidak dapat menghapus kamar yang masih ditempati');
        }

        $room->delete();

        return redirect()->route('rooms.index')
                        ->with('success', 'Kamar berhasil dihapus');
    }

    public function updateStatus(Request $request, Room $room)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        $room->update($validated);

        return redirect()->back()
                        ->with('success', 'Status kamar berhasil diupdate');
    }
}