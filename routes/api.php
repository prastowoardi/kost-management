<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileTenantController;
use App\Http\Controllers\Api\MobileComplaintController;
use App\Http\Controllers\Api\MobilePaymentController;
use App\Http\Controllers\Api\Admin\FinanceController;
use App\Http\Controllers\Api\Admin\AdminTenantController;
use App\Http\Controllers\Api\Admin\StatsController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Admin\AdminComplaintController;
use App\Helpers\LogHelper;

// --- PUBLIC ROUTES ---
Route::post('/login', [MobileAuthController::class, 'login']);
Route::post('/register', [MobileAuthController::class, 'register']);

// --- TENANT ROUTES (Sisi Penghuni) ---
Route::middleware('auth:sanctum')->group(function () {

    // Profile & Info Dashboard
    Route::get('/me', [MobileTenantController::class, 'getProfile']);

    // Tagihan & Riwayat Bayar
    Route::get('/tenant/payments', [MobilePaymentController::class, 'getHistory']);
    Route::post('/payments/upload', [MobilePaymentController::class, 'uploadProof']);
    Route::get('/tenant/payments/{id}', [MobilePaymentController::class, 'show']);

    // Komplain
    Route::get('/complaints', [MobileComplaintController::class, 'index']);
    Route::post('/complaints', [MobileComplaintController::class, 'store']);
    Route::get('/complaints/{id}', [MobileComplaintController::class, 'show']);

    Route::post('/change-password', [MobileAuthController::class, 'changePassword']);
    Route::post('/logout', [MobileAuthController::class, 'logout']);
});

// --- ADMIN ROUTES (Sisi Pengelola) ---
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/stats', [StatsController::class, 'index']);
    // Payments Verification
    Route::get('/payments/pending', function () {
        return \App\Models\Payment::with(['tenant.user', 'room'])
                ->where('status', 'pending')->get();
    });
    Route::post('/payments/{id}/verify', [MobilePaymentController::class, 'verifyPayment'])
            ->middleware('auth:sanctum');

    // Complaints Management
    Route::get('/complaints', [AdminComplaintController::class, 'index']);
    Route::get('/complaints/{id}', [AdminComplaintController::class, 'show']);
    Route::patch('/complaints/{id}/status', [AdminComplaintController::class, 'updateStatus']);
    Route::post('/complaints/{id}/respond', [AdminComplaintController::class, 'respond']);

    // Tenants Management
    Route::get('/tenants', function () {
        return \App\Models\Tenant::with(['user', 'room'])->get();
    });
    Route::get('/rooms/available', function () {
        return \App\Models\Room::where('status', 'available')->get();
    });
    Route::post('/tenants/store', [AdminTenantController::class, 'store']);

    Route::apiResource('/finances', FinanceController::class);
});

Route::middleware('auth:sanctum')->post('/update-push-token', function (Request $request) {
    $request->validate(['token' => 'required']);
    $request->user()->update(['expo_push_token' => $request->token]);
    \App\Helpers\LogHelper::log('UPDATE_PUSH_TOKEN', "User " . $request->user()->name . " memperbarui Push Token device");
    return response()->json(['message' => 'Token updated']);
});
