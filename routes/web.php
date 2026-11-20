<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TenantApplicationController;
use App\Http\Controllers\TenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Pengunjung diarahkan sesuai status login-nya.
Route::get('/', fn (Request $request) => redirect(
    $request->user()?->homePath() ?? route('login'),
));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    // Area pengelola kantor virtual.
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('tenants', TenantController::class)->except('show');

        Route::resource('ruang-fasilitas', RoomController::class)
            ->parameters(['ruang-fasilitas' => 'room'])
            ->except('show');

        Route::resource('pemesanan', BookingController::class)
            ->parameters(['pemesanan' => 'booking'])
            ->except('show');

        Route::patch('pemesanan/{booking}/status', [BookingController::class, 'updateStatus'])
            ->name('pemesanan.status');

        // Jadwal penggunaan ruang diturunkan dari pemesanan yang disetujui.
        Route::get('jadwal-ruang', [ScheduleController::class, 'index'])->name('jadwal-ruang.index');

        Route::resource('kontrak-sewa', ContractController::class)
            ->parameters(['kontrak-sewa' => 'contract'])
            ->except('show');

        Route::resource('tagihan-pembayaran', InvoiceController::class)
            ->parameters(['tagihan-pembayaran' => 'invoice'])
            ->except('show');

        Route::patch('tagihan-pembayaran/{invoice}/lunas', [InvoiceController::class, 'markPaid'])
            ->name('tagihan-pembayaran.lunas');

        Route::get('laporan', [ReportController::class, 'index'])->name('laporan.index');
    });

    // Portal untuk tenant dan customer.
    Route::middleware('role:tenant,customer')->group(function () {
        Route::get('/portal', [PortalController::class, 'index'])->name('portal');

        // Pengajuan sewa mandiri bagi customer yang belum punya data tenant.
        Route::get('/portal/pengajuan', [TenantApplicationController::class, 'create'])->name('portal.apply');
        Route::post('/portal/pengajuan', [TenantApplicationController::class, 'store'])->name('portal.apply.store');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
