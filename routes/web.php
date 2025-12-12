<?php

// routes/web.php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Middleware auth untuk semua route yang memerlukan login
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
        
    // Rooms Management
    Route::resource('rooms', RoomController::class);
    Route::patch('rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.status');
    
    // Tenants Management
    Route::resource('tenants', TenantController::class);
    Route::patch('tenants/{tenant}/status', [TenantController::class, 'updateStatus'])->name('tenants.status');
    
    // Payments Management
    Route::get('/payments/report', [PaymentController::class, 'report'])->name('payments.report');
    Route::get('reports/payments', [PaymentController::class, 'report'])->name('reports.payments');
    Route::resource('payments', PaymentController::class);
    Route::patch('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])->name('payments.receipt');
    
    // Facilities Management
    Route::resource('facilities', FacilityController::class);
    
    // Complaints Management
    Route::resource('complaints', ComplaintController::class);
    Route::patch('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.status');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include authentication routes
require __DIR__.'/auth.php';