<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\PaymentPageController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('welcome');
});

// Halaman Pembayaran Publik (Tanpa Login)
Route::controller(PaymentPageController::class)->group(function () {
    Route::get('/pay/{hash}', 'show')->name('public.pay');
    Route::post('/pay/{hash}/upload', 'upload')->name('public.pay.upload');
    Route::get('/pay/{hash}/success', 'success')->name('public.pay.success');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/send-reminder', [App\Http\Controllers\DashboardController::class, 'sendReminder'])->name('send.reminder');
    
    // Profile (Semua User)
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    /* --- Role: ADMIN & STAFF --- */
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Management Resources
        Route::resource('rooms', RoomController::class);
        Route::put('rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.updateStatus');
        
        Route::resource('tenants', TenantController::class);
        
        Route::resource('payments', PaymentController::class);
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::put('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
        Route::post('payments/{payment}/send-wa', [PaymentController::class, 'sendGatewayWA'])->name('payments.sendWa');

        Route::resource('facilities', FacilityController::class);
        Route::resource('complaints', ComplaintController::class);
        Route::put('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');

        // Finances (Clean Version)
        Route::prefix('finances')->name('finances.')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
            Route::get('/finances/{finance}/edit', [FinanceController::class, 'edit'])->name('finances.edit');
            Route::put('/finances/{finance}', [FinanceController::class, 'update'])->name('finances.update');
            Route::get('/finances/report', [FinanceController::class, 'report'])->name('finances.report');
            Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
            Route::get('/report', [FinanceController::class, 'report'])->name('report');
            Route::get('/create', [FinanceController::class, 'create'])->name('create');
            Route::post('/', [FinanceController::class, 'store'])->name('store');
            Route::get('/{finance}', [FinanceController::class, 'show'])->name('show');
            Route::get('/{finance}/edit', [FinanceController::class, 'edit'])->name('edit');
            Route::put('/{finance}', [FinanceController::class, 'update'])->name('update');
            Route::delete('/{finance}', [FinanceController::class, 'destroy'])->name('destroy');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
            Route::get('/finances', [ReportController::class, 'finances'])->name('finances');
            Route::get('/rooms', [ReportController::class, 'rooms'])->name('rooms');
            Route::get('/tenants', [ReportController::class, 'tenants'])->name('tenants');
        });

        // Broadcast & Chat
        Route::prefix('broadcast')->name('broadcast.')->group(function () {
            Route::get('/', [BroadcastController::class, 'index'])->name('index');
            Route::post('/send', [BroadcastController::class, 'send'])->name('send');
            Route::get('/history', [BroadcastController::class, 'history'])->name('history');
            Route::get('/chat/{id}', [BroadcastController::class, 'showChat'])->name('chat');
            Route::post('/send-personal', [BroadcastController::class, 'sendPersonal'])->name('send-personal');
        });
    });

    /* --- Role: ADMIN ONLY --- */
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::put('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    });

    /* --- Role: TENANT ONLY --- */
    Route::middleware('role:tenant')->prefix('tenant')->name('tenant.')->group(function () {
        Route::get('/dashboard', [TenantController::class, 'tenantDashboard'])->name('dashboard');
        Route::get('/payments', [TenantController::class, 'tenantPayments'])->name('payments');
        Route::get('/complaints', [ComplaintController::class, 'tenantComplaints'])->name('complaints');
        Route::post('/complaints', [ComplaintController::class, 'storeByTenant'])->name('complaints.store');
    });
});

require __DIR__.'/auth.php';