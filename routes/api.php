<?php

use App\Http\Controllers\Api\Admin\AdminComplaintController;
use App\Http\Controllers\Api\Admin\AdminPaymentController;
use App\Http\Controllers\Api\Admin\AdminRoomController;
use App\Http\Controllers\Api\Admin\AdminTenantController;
use App\Http\Controllers\Api\Admin\FinanceController;
use App\Http\Controllers\Api\Admin\StatsController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileComplaintController;
use App\Http\Controllers\Api\MobilePaymentController;
use App\Http\Controllers\Api\MobileTenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [MobileAuthController::class, 'login']);
Route::post('/register', [MobileAuthController::class, 'register']);
Route::get('health', function () {
    return response()->json(['status' => 'OK']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [MobileTenantController::class, 'getProfile']);

    Route::get('/tenant/payments', [MobilePaymentController::class, 'getHistory']);
    Route::post('/payments/upload', [MobilePaymentController::class, 'uploadProof']);
    Route::get('/tenant/payments/{id}', [MobilePaymentController::class, 'show']);

    Route::get('/complaints', [MobileComplaintController::class, 'index']);
    Route::post('/complaints', [MobileComplaintController::class, 'store']);
    Route::get('/complaints/{id}', [MobileComplaintController::class, 'show']);

    Route::post('/change-password', [MobileAuthController::class, 'changePassword']);
    Route::post('/logout', [MobileAuthController::class, 'logout']);

    Route::post('/update-push-token', function (Request $request) {
        $request->validate(['token' => 'required']);
        $request->user()->update(['expo_push_token' => $request->token]);
        \App\Helpers\LogHelper::log('UPDATE_PUSH_TOKEN', 'User '.$request->user()->name.' memperbarui Push Token device');

        return response()->json(['message' => 'Token updated']);
    });
});

Route::middleware(['auth:sanctum'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/stats', [StatsController::class, 'index']);

        Route::get('/payments/pending', function () {
            return \App\Models\Payment::with(['tenant.user', 'room'])
                ->where('status', 'pending')->get();
        });

        Route::post('/payments/store', [AdminPaymentController::class, 'store']);
        Route::get('/payments/{uuid}', [AdminPaymentController::class, 'show']);
        Route::post('/payments/{id}/verify', [MobilePaymentController::class, 'verifyPayment']);

        Route::get('/complaints', [AdminComplaintController::class, 'index']);
        Route::get('/complaints/{id}', [AdminComplaintController::class, 'show']);
        Route::patch('/complaints/{id}/status', [AdminComplaintController::class, 'updateStatus']);
        Route::post('/complaints/{id}/respond', [AdminComplaintController::class, 'respond']);

        Route::get('/tenants', [AdminTenantController::class, 'index']);
        Route::get('/tenants/active', [AdminTenantController::class, 'activeTenants']);
        Route::get('/rooms', [AdminRoomController::class, 'index']);
        Route::get('/rooms/available', [AdminTenantController::class, 'availableRooms']);
        Route::get('/rooms/{uuid}', [AdminRoomController::class, 'show']);
        Route::match(['put', 'patch', 'post'], '/rooms/{uuid}', [AdminRoomController::class, 'update']);
        Route::put('/rooms/{uuid}/status', [AdminRoomController::class, 'updateStatus']);
        Route::post('/tenants/store', [AdminTenantController::class, 'store']);
        Route::match(['put', 'patch', 'post'], '/tenants/update/{uuid}', [AdminTenantController::class, 'update']);
        Route::post('/tenants/delete/{uuid}', [AdminTenantController::class, 'destroy']);
        Route::get('/tenants/{uuid}/payments', [AdminTenantController::class, 'payments']);

        Route::get('/categories', [FinanceController::class, 'getApiCategories']);
        Route::apiResource('/finances', FinanceController::class);
    });
