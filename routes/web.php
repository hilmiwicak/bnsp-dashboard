<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenyewaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::resource('penyewa', PenyewaController::class)->except('show');

    // Modul standar kantor virtual selain Data Penyewa. Struktur halaman sudah
    // disiapkan (judul, deskripsi, kolom tabel) dan datanya menyusul.
    $modules = [
        'ruang-fasilitas' => [
            'heading' => 'Data Ruang & Fasilitas',
            'description' => 'Daftar ruang meeting, private office, dan fasilitas pendukung yang dapat dipesan penyewa.',
            'columns' => ['Kode Ruang', 'Nama Ruang', 'Kapasitas', 'Lokasi', 'Status'],
        ],
        'pemesanan' => [
            'heading' => 'Pemesanan',
            'description' => 'Catat pemesanan ruang dan layanan yang diajukan oleh penyewa kantor virtual.',
            'columns' => ['Kode Pemesanan', 'Penyewa', 'Ruang / Layanan', 'Tanggal', 'Durasi', 'Status'],
        ],
        'kontrak-sewa' => [
            'heading' => 'Kontrak Sewa',
            'description' => 'Kelola dokumen kontrak sewa aktif, termasuk periode dan status kontrak yang disepakati.',
            'columns' => ['Nomor Kontrak', 'Penyewa', 'Mulai', 'Berakhir', 'Durasi', 'Status'],
        ],
        'jadwal-ruang' => [
            'heading' => 'Jadwal Penggunaan Ruang',
            'description' => 'Pantau jadwal pemakaian ruang meeting agar tidak terjadi bentrok antar penyewa.',
            'columns' => ['Ruang', 'Penyewa', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Status'],
        ],
        'tagihan-pembayaran' => [
            'heading' => 'Tagihan & Pembayaran',
            'description' => 'Terbitkan tagihan bulanan penyewa dan catat pembayaran yang sudah diterima.',
            'columns' => ['No. Tagihan', 'Penyewa', 'Periode', 'Jumlah', 'Jatuh Tempo', 'Status'],
        ],
        'laporan' => [
            'heading' => 'Laporan',
            'description' => 'Rekap data penyewa, pemesanan, dan pendapatan kantor virtual per periode.',
            'columns' => ['Periode', 'Total Penyewa', 'Total Pemesanan', 'Total Pendapatan', 'Status'],
        ],
    ];

    foreach ($modules as $slug => $module) {
        Route::view($slug, 'modules.placeholder', $module)->name($slug.'.index');
    }

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
