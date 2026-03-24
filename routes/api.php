<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileTenantController;
use App\Http\Controllers\Api\MobileComplaintController;
use App\Http\Controllers\Api\MobilePaymentController;

// --- PUBLIC ROUTES ---
Route::post('/login', [MobileAuthController::class, 'login']);
Route::post('/register', [MobileAuthController::class, 'register']);

// --- TENANT ROUTES (Sisi Penghuni) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Profile & Info Dashboard
    Route::get('/me', [MobileTenantController::class, 'getProfile']);
    
    // Tagihan & Riwayat Bayar
    Route::get('/payments/history', [MobilePaymentController::class, 'getHistory']); // Gunakan Controller agar rapi
    Route::post('/payments/upload', [MobilePaymentController::class, 'uploadProof']);
    
    // Komplain
    Route::get('/complaints', [MobileComplaintController::class, 'index']);
    Route::post('/complaints', [MobileComplaintController::class, 'store']);
    
    Route::post('/logout', [MobileAuthController::class, 'logout']);
});

// --- ADMIN ROUTES (Sisi Pengelola) ---
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    
    // Stats Dashboard Admin
    Route::get('/stats', function () {
        return [
            'total_rooms' => \App\Models\Room::count(),
            'occupied_rooms' => \App\Models\Tenant::where('status', 'active')->count(),
            'vacant_rooms' => \App\Models\Room::where('status', 'available')->count(),
            'monthly_income' => \App\Models\Payment::where('status', 'success')->whereMonth('created_at', now()->month)->sum('total'),
            'latest_complaints' => \App\Models\Complaint::with(['tenant.user'])->where('status', 'pending')->take(5)->get()
        ];
    });

    // Payments Verification
    Route::get('/payments/pending', function () {
        return \App\Models\Payment::with(['tenant.user', 'room'])
                ->where('status', 'pending')->get();
    });
    Route::post('/payments/{id}/verify', function (Request $request, $id) {
        $payment = \App\Models\Payment::findOrFail($id);
        $payment->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated']);
    });

    // Complaints Management
    Route::get('/complaints', function () {
        return \App\Models\Complaint::with(['tenant.user', 'tenant.room'])
                ->orderByRaw("FIELD(status, 'pending', 'process', 'resolved')")
                ->orderBy('created_at', 'desc')->get();
    });
    Route::post('/complaints/{id}/respond', function (Request $request, $id) {
        $complaint = \App\Models\Complaint::findOrFail($id);
        $complaint->update([
            'status' => $request->status,
            'response' => $request->response
        ]);
        return response()->json(['message' => 'Laporan diperbarui']);
    });

    // Tenants Management
    Route::get('/tenants', function () {
        return \App\Models\Tenant::with(['user', 'room'])->get();
    });
    Route::get('/rooms/available', function () {
        return \App\Models\Room::where('status', 'available')->get();
    });
    Route::post('/tenants/store', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'room_id' => 'required|exists:rooms,id',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        return \DB::transaction(function () use ($request) {
            try {
                $user = \App\Models\User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password ?? 'password123'),
                    'role' => 'tenant'
                ]);

                $tenant = new \App\Models\Tenant();
                $tenant->user_id = $user->id;
                $tenant->room_id = $request->room_id;
                $tenant->name = $request->name;
                $tenant->email = $request->email;
                $tenant->phone = $request->phone;
                $tenant->id_card = $request->id_card;
                $tenant->address = $request->address;
                $tenant->entry_date = $request->entry_date;
                $tenant->status = 'active';
                $tenant->save();

                $room = \App\Models\Room::find($request->room_id);
                $room->update(['status' => 'occupied']);

                return response()->json(['message' => 'Tenant berhasil didaftarkan!']);
                
            } catch (\Exception $e) {
                return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
            }
        });
    });
});