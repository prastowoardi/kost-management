<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Facility;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
        try {
            $validated = $request->validate([
                'room_number' => 'required|unique:rooms',
                'type' => 'required|in:singlenoac,singleac,shared',
                'price' => 'required|numeric|min:0',
                'capacity' => 'required|integer|min:1',
                'size' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'facilities' => 'nullable|array',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('rooms', 'public');
                }
            }

            $validated['images'] = json_encode($imagePaths);

            $room = Room::create($validated);

            if ($request->has('facilities')) {
                $room->facilities()->attach($request->facilities);
            }

            LogHelper::log('CREATE_ROOM', "Menambah kamar {$room->room_number}", $room);

            return redirect()->route('rooms.index')
                ->with('success', 'Kamar berhasil ditambahkan');
        } catch (Throwable $e) {
            LogHelper::logError(
                'CREATE_ROOM_FAILED',
                'Gagal menambah kamar',
                $e,
                ['room_number' => $request->room_number]
            );

            return back()->with('error', 'Gagal menambah kamar')->withInput();
        }
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
            'new_images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
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

        $before = $room->toArray();
        $room->update($validated);
        $after = $room->fresh()->toArray();

        // Sync facilities
        if ($request->has('facilities')) {
            $room->facilities()->sync($request->facilities);
        } else {
            $room->facilities()->detach();
        }

        LogHelper::log('UPDATE_ROOM', "Mengubah kamar {$room->room_number}", $room, [
            'before' => $before,
            'after' => $after,
        ]);

        return redirect()->route('rooms.show', $room)
            ->with('success', 'Kamar berhasil diupdate!');
    }

    public function destroy(Room $room)
    {
        if ($room->activeTenant) {
            LogHelper::logError(
                'DELETE_ROOM_FAILED',
                "Gagal hapus kamar {$room->room_number}: masih ditempati"
            );

            return redirect()->route('rooms.index')
                ->with('error', 'Tidak dapat menghapus kamar yang masih ditempati');
        }

        try {
            $images = is_string($room->images)
                ? json_decode($room->images, true)
                : ($room->images ?? []);

            if (! empty($images)) {
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $deletedData = $room->toArray();
            $room->delete();

            LogHelper::log('DELETE_ROOM', "Menghapus kamar {$deletedData['room_number']}", null, [
                'deleted' => $deletedData,
            ]);

            return redirect()->route('rooms.index')
                ->with('success', 'Kamar berhasil dihapus');
        } catch (Throwable $e) {
            LogHelper::logError(
                'DELETE_ROOM_FAILED',
                "Gagal hapus kamar {$room->room_number}",
                $e
            );

            return back()->with('error', 'Gagal menghapus kamar');
        }
    }

    public function updateStatus(Request $request, Room $room)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $before = $room->toArray();
        $room->update($validated);
        $after = $room->fresh()->toArray();

        LogHelper::log('UPDATE_ROOM_STATUS', "Mengubah status kamar {$room->room_number} dari {$before['status']} ke {$after['status']}", $room, [
            'before' => $before,
            'after' => $after,
        ]);

        return redirect()->back()
            ->with('success', 'Status kamar berhasil diupdate');
    }
}
