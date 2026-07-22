<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Throwable;

class AdminRoomController extends Controller
{
    public function index()
    {
        return Room::with(['activeTenant', 'facilities'])
            ->orderBy('room_number')
            ->get();
    }

    public function show($uuid)
    {
        $room = Room::where('uuid', $uuid)
            ->with(['activeTenant.user', 'facilities', 'payments' => function ($q) {
                $q->latest()->limit(5);
            }])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $room,
        ]);
    }

    public function update(Request $request, $uuid)
    {
        try {
            $room = Room::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'room_number' => 'required|unique:rooms,room_number,' . $room->id,
                'type' => 'required|in:singlenoac,singleac,shared',
                'price' => 'required|numeric|min:0',
                'capacity' => 'required|integer|min:1',
                'status' => 'required|in:available,occupied,maintenance',
                'size' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
            ]);

            $room->update($validated);

            LogHelper::log(
                'UPDATE_ROOM_API',
                "Admin mengupdate kamar {$room->room_number}"
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Kamar berhasil diupdate!',
                'data' => $room->fresh()->load(['activeTenant', 'facilities']),
            ]);
        } catch (Throwable $e) {
            LogHelper::logError('UPDATE_ROOM_API_FAILED', 'Gagal update kamar', $e);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate kamar',
            ], 500);
        }
    }

    public function updateStatus(Request $request, $uuid)
    {
        try {
            $room = Room::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'status' => 'required|in:available,occupied,maintenance',
            ]);

            $room->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Status kamar berhasil diupdate!',
                'data' => $room->fresh(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate status kamar',
            ], 500);
        }
    }
}
