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
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\PaymentPageController;
use App\Http\Controllers\PublicRegistrationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Models\Tenant;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('welcome');
});

// Halaman Pembayaran Publik (Tanpa Login)
Route::controller(PaymentPageController::class)->group(function () {
    Route::get('/pay/{hash}', 'show')->name('public.pay');
    Route::post('/pay/{hash}/upload', 'upload')->name('public.pay.upload');
    Route::get('/pay/{hash}/success', 'success')->name('public.pay.success');
});

// Form register
Route::get('/join', [PublicRegistrationController::class, 'index'])->name('public.register');
Route::post('/join', [PublicRegistrationController::class, 'store'])->name('public.register.store');
Route::get('/join/success', [PublicRegistrationController::class, 'success'])->name('public.register.success');

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

Route::get('/test-wa/{id}', function ($id) {
    $tenant = Tenant::with('room')->findOrFail($id);
    
    // Panggil fungsi yang sama dengan yang ada di controller
    // Karena kita tidak bisa memanggil fungsi private di sini, kita tulis manual logic-nya
    $message = "Halo {$tenant->name}! Selamat datang di Serrata Kost! 👋✨\n\n" .
                    "Terimakasih sudah memilih Serrata Kost. Semoga betah dan nyaman ya tinggal di sini! 😊\n\n" .
                    "*Biar lebih asyik, yuk intip 'Rules of the House' kita:* 📝\n\n" .
                    "1. 🕒 *Jam Malam & Tamu:* Tamu berkunjung maksimal sampai jam 23.00 WIB ya. Demi privasi penghuni lain, mohon tidak membawa tamu lawan jenis ke dalam kamar.\n" .
                    "2. 🛏️ *Info Menginap:* Kalau ada keluarga atau teman yang mau menginap, wajib lapor dan konfirmasi ke admin terlebih dahulu ya.\n" .
                    "3. 🚪 *Keamanan Gerbang:* Mohon selalu tutup kembali dan kunci gerbang setiap kali kamu keluar atau masuk area kost. Keamanan kita tanggung jawab bersama! 🔐\n" .
                    "4. 🚿 *Hemat Air & Listrik:* Matikan lampu, AC, alat elektronik, dan keran air kalau lagi nggak dipakai atau saat keluar kamar ya.\n" .
                    "5. 🤫 *Keep it Quiet:* Di atas jam 21.30, tolong kecilkan volume musik atau suara ngobrol biar teman sebelah bisa istirahat tenang.\n" .
                    "6. 🧼 *Kebersihan:* Kamar adalah istanamu, jadi mohon dijaga kebersihannya. Sampah tolong dibuang ke tempat yang sudah disediakan ya.\n" .
                    "7. 🅿️ *Parkir Rapih:* Parkir kendaraan di slot yang sudah ditentukan agar tidak menghalangi jalan keluar-masuk teman lainnya.\n" .
                    "8. 🚭 *Area Merokok:* Mohon tidak merokok di dalam kamar. Gunakan area terbuka yang sudah tersedia ya.\n" .
                    "9. 🍳 *Dapur Bersama:* Habis masak, jangan lupa langsung dicuci alat masaknya dan bersihkan kembali meja dapurnya.\n" .
                    "10. 🧺 *Jemuran:* Kalau sudah kering segera diambil ya, biar bisa gantian sama penghuni lain dan menghindari barang tertukar/hilang.\n" .
                    "11. 🚫 *Barang Terlarang:* *Dilarang keras membawa narkoba, miras, senjata tajam, atau hewan peliharaan.*\n" .
                    "12. 🆘 *Lapor Kendala:* Ada keran bocor, lampu mati, atau kendala lain? Langsung kabari admin lewat chat nomor ini ya!\n\n" .
                    "Sekali lagi, selamat bergabung! Selamat istirahat dan semoga betah di Serrata Kost! 🏠🙌";

    try {
        $response = Http::timeout(10)->post('http://localhost:3000/send-message', [
            'number'  => $tenant->phone,
            'message' => $message
        ]);

        return $response->json();
    } catch (\Exception $e) {
        return "Gagal: " . $e->getMessage();
    }
});

require __DIR__.'/auth.php';