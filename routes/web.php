<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes for Admin & Staff only
Route::middleware(['auth', 'verified', 'role:admin,staff'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rooms Management
    Route::resource('rooms', RoomController::class);
    Route::put('rooms/{room}/status', [RoomController::class, 'updateStatus'])->name('rooms.updateStatus');
    
    // Tenants Management
    Route::resource('tenants', TenantController::class);
    
    // Payments Management
    Route::resource('payments', PaymentController::class);
    Route::put('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
    
    // Finance Management
    Route::prefix('finances')->name('finances.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/report', [FinanceController::class, 'report'])->name('report');
        Route::get('/create', [FinanceController::class, 'create'])->name('create');
        Route::post('/', [FinanceController::class, 'store'])->name('store');
        Route::get('/{finance}', [FinanceController::class, 'show'])->name('show');
        Route::get('/{finance}/edit', [FinanceController::class, 'edit'])->name('edit');
        Route::put('/{finance}', [FinanceController::class, 'update'])->name('update');
        Route::delete('/{finance}', [FinanceController::class, 'destroy'])->name('destroy');
    });
    
    // Facilities Management
    Route::resource('facilities', FacilityController::class);
    
    // Complaints Management
    Route::resource('complaints', ComplaintController::class);
    Route::put('complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.updateStatus');
});

// Routes for Admin only
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // User Management
    Route::resource('users', UserController::class);
    Route::put('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
});

// Routes for Tenants
Route::middleware(['auth', 'verified', 'role:tenant'])->group(function () {
    Route::get('/tenant/dashboard', [TenantController::class, 'tenantDashboard'])->name('tenant.dashboard');
    Route::get('/tenant/payments', [TenantController::class, 'tenantPayments'])->name('tenant.payments');
    Route::get('/tenant/complaints', [ComplaintController::class, 'tenantComplaints'])->name('tenant.complaints');
    Route::post('/tenant/complaints', [ComplaintController::class, 'storeByTenant'])->name('tenant.complaints.store');
});

// Profile (All authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';