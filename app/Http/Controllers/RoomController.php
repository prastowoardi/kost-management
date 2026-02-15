<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'type' => 'required|in:singlenoac,singleac,shared',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        // Handle multiple images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('rooms', 'public');
            }
        }
        
        // Store as JSON
        $validated['images'] = json_encode($imagePaths);

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
            'room_number' => 'required|unique:rooms,room_number,'.$room->id,
            'type' => 'required|in:singlenoac,singleac,shared',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'size' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array',
            'keep_images' => 'nullable|array',
            'new_images' => 'nullable|array|max:5',
            'new_images.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        // Get old images data
        $oldImages = is_string($room->images) 
            ? json_decode($room->images, true) 
            : ($room->images ?? []);
        
        // Images to keep from old ones
        $keepImages = $request->input('keep_images', []);
        
        // Calculate deleted images
        $deletedImages = array_diff($oldImages, $keepImages);

        // Delete removed images from storage
        foreach ($deletedImages as $deletedImage) {
            if (Storage::disk('public')->exists($deletedImage)) {
                Storage::disk('public')->delete($deletedImage);
            }
        }

        // Start with kept images
        $finalImages = $keepImages;
        
        // Upload and add new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('rooms', 'public');
                $finalImages[] = $path;
            }
        }
        
        // Update validated data with final images
        $validated['images'] = json_encode($finalImages);
        
        // Remove non-database fields
        unset($validated['keep_images']);
        unset($validated['new_images']);

        $room->update($validated);
        
        // Sync facilities
        if ($request->has('facilities')) {
            $room->facilities()->sync($request->facilities);
        } else {
            $room->facilities()->detach();
        }
        
        return redirect()->route('rooms.show', $room)
            ->with('success', 'Kamar berhasil diupdate!');
    }

    public function destroy(Room $room)
    {
        if ($room->activeTenant) {
            return redirect()->route('rooms.index')
                            ->with('error', 'Tidak dapat menghapus kamar yang masih ditempati');
        }

        // Delete all images
        $images = is_string($room->images) 
            ? json_decode($room->images, true) 
            : ($room->images ?? []);
            
        if (!empty($images)) {
            foreach ($images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
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