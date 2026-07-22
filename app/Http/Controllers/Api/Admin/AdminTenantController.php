<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\TenantRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AdminTenantController extends Controller
{
    public function __construct(
        private TenantRegistrationService $registration,
    ) {}

    public function index()
    {
        return \App\Models\Tenant::with(['user', 'room'])->get();
    }

    public function allRooms()
    {
        return Room::all();
    }

    public function availableRooms()
    {
        return Room::where('status', 'available')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'room_id' => 'required|exists:rooms,id',
            'phone' => 'required',
            'id_card' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        return DB::transaction(function () use ($request) {
            try {
                $room = Room::findOrFail($request->room_id);

                $tenant = $this->registration->registerWithUser([
                    'room_id' => $room->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'id_card' => $request->id_card,
                    'address' => $request->address,
                    'entry_date' => $request->entry_date ?? now(),
                    'emergency_contact_name' => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                    'status' => 'active',
                ], $request->password ?? 'serratajos');

                LogHelper::log(
                    'CREATE_TENANT',
                    'Admin '.$request->user()->name." mendaftarkan tenant: {$request->name} di Kamar ".$room->room_number,
                    $tenant->user
                );

                return response()->json([
                    'status' => 'success',
                    'message' => 'Tenant berhasil didaftarkan!',
                    'data' => $tenant,
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal simpan: '.$e->getMessage(),
                ], 500);
            }
        });
    }

    public function destroy($uuid)
    {
        try {
            $tenant = Tenant::where('uuid', $uuid)->firstOrFail();
            $name = $tenant->name;

            $tenant->update(['status' => 'inactive']);
            $tenant->room?->update(['status' => 'available']);

            if ($tenant->user) {
                $tenant->user->tokens()->delete();
                $tenant->user->delete();
            }

            $tenant->delete();

            LogHelper::log(
                'DELETE_TENANT',
                "Admin menghapus tenant: {$name}"
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Tenant berhasil dihapus!',
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'DELETE_TENANT_FAILED',
                'Gagal hapus tenant: '.$uuid,
                $e
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus tenant',
            ], 500);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $tenant = Tenant::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$tenant->user_id,
                'room_id' => 'required|exists:rooms,id',
                'phone' => 'required',
                'id_card' => 'required',
                'address' => 'nullable|string',
                'entry_date' => 'nullable|date',
                'emergency_contact_name' => 'nullable|string',
                'emergency_contact_phone' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
            ]);

            $tenant->update([
                'room_id' => $validated['room_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'id_card' => $validated['id_card'],
                'address' => $validated['address'] ?? $tenant->address,
                'entry_date' => $validated['entry_date'] ?? $tenant->entry_date,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? $tenant->emergency_contact_name,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? $tenant->emergency_contact_phone,
                'status' => $validated['status'] ?? $tenant->status,
            ]);

            if ($tenant->user && $tenant->user->email !== $validated['email']) {
                $tenant->user->update(['email' => $validated['email']]);
            }

            LogHelper::log(
                'UPDATE_TENANT',
                'Admin '.$request->user()->name." mengupdate tenant: {$validated['name']}",
                $tenant->user
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Tenant berhasil diupdate!',
                'data' => $tenant->fresh()->load(['user', 'room']),
            ]);
        } catch (Throwable $e) {
            LogHelper::logError(
                'UPDATE_TENANT_FAILED',
                'Gagal update tenant: '.($request->name ?? $uuid),
                $e
            );

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate tenant',
            ], 500);
        }
    }
}
