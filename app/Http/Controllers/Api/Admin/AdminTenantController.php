<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Room;
use App\Helpers\LogHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminTenantController extends Controller
{
    /**
     * Mengambil semua data tenant
     */
    public function index()
    {
        return Tenant::with(['user', 'room'])->get();
    }

    /**
     * Mengambil kamar yang masih kosong
     */
    public function availableRooms()
    {
        return Room::where('status', 'available')->get();
    }

    /**
     * Menyimpan data tenant baru (dan buat akun user)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'room_id'   => 'required|exists:rooms,id',
            'phone'     => 'required',
            'id_card'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        return DB::transaction(function () use ($request) {
            try {
                $room = Room::findOrFail($request->room_id);

                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password ?? 'serratajos'),
                    'role'     => 'tenant'
                ]);

                $tenant = Tenant::create([
                    'user_id'                 => $user->id,
                    'room_id'                 => $room->id,
                    'name'                    => $request->name,
                    'email'                   => $request->email,
                    'phone'                   => $request->phone,
                    'id_card'                 => $request->id_card,
                    'address'                 => $request->address,
                    'entry_date'              => $request->entry_date ?? now(),
                    'emergency_contact_name'  => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                    'status'                  => 'active',
                ]);

                $room->update(['status' => 'occupied']);

                LogHelper::log(
                    'CREATE_TENANT', 
                    "Admin " . $request->user()->name . " mendaftarkan tenant: {$request->name} di Kamar " . $room->room_number,
                    $user
                );

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Tenant berhasil didaftarkan!',
                    'data'    => $tenant
                ]);
                
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal simpan: ' . $e->getMessage()
                ], 500);
            }
        });
    }
}