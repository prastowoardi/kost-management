<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\TenantRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminTenantController extends Controller
{
    public function __construct(
        private TenantRegistrationService $registration,
    ) {}

    public function index()
    {
        return \App\Models\Tenant::with(['user', 'room'])->get();
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
}
