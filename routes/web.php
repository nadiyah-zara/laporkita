<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PetaLayananController;
use App\Http\Controllers\ItemClaimController;
use App\Http\Controllers\LaporanSayaController;

// ==========================================================
// 1. RUTE PUBLIK (BISA DILIHAT OLEH UMUM / TANPA LOGIN)
// ==========================================================
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/peta-layanan', [PetaLayananController::class, 'index'])->name('peta.layanan');

// Foto 1: Halaman Utama Infrastruktur (Publik)
Route::get('/lapor-infrastruktur', [ReportController::class, 'indexInfrastruktur'])
    ->name('laporan.infrastruktur.index');
Route::get('/laporan', [ReportController::class, 'indexInfrastruktur'])
    ->name('laporan.index'); // Alias

// Foto 3: Detail Laporan Publik (Bisa dibaca siapa saja)
Route::get('/lapor-infrastruktur/detail/{id}', [ReportController::class, 'showInfrastruktur'])
    ->name('laporan.infrastruktur.detail');
Route::get('/laporan/detail/{id}', [ReportController::class, 'showInfrastruktur'])
    ->name('laporan.show'); // Alias

// Foto 1: Halaman Publik Barang Hilang (Bisa dibuka siapa saja)
Route::get('/lapor-barang-hilang', [ReportController::class, 'indexBarang'])
    ->name('laporan.barang.index');

Route::get('/dashboard', function () {
    return redirect()->route('beranda');
})->middleware(['auth'])->name('dashboard');

// ==========================================================
// 2. RUTE PRIBADI (WAJIB LOGIN)
// ==========================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Foto 2: Form Buat Laporan Baru
    Route::get('/lapor-infrastruktur/buat', [ReportController::class, 'createInfrastruktur'])
        ->name('laporan.infrastruktur');
    Route::get('/laporan/buat', [ReportController::class, 'createInfrastruktur'])
        ->name('laporan.create'); // Alias
    Route::post('/lapor-infrastruktur/buat', [ReportController::class, 'storeInfrastruktur'])
        ->name('laporan.infrastruktur.store');
    Route::post('/laporan/buat', [ReportController::class, 'storeInfrastruktur'])
        ->name('laporan.store'); // Alias

    // Foto 4: Halaman "Laporan Saya" (Monitoring Aduan Akun Sendiri)
    Route::get('/lapor-infrastruktur/saya', [ReportController::class, 'sayaInfrastruktur'])
        ->name('laporan.infrastruktur.saya');
    Route::get('/laporan-saya', [ReportController::class, 'sayaInfrastruktur'])
        ->name('laporan.saya'); // Alias

    // Foto 2: Pilihan Jenis Laporan
    Route::get('/lapor-barang-hilang/pilih', [ReportController::class, 'pilihJenisBarang'])
        ->name('laporan.barang.pilih');
    // Foto 3: Form Kehilangan
    Route::get('/lapor-barang-hilang/kehilangan', [ReportController::class, 'createKehilangan'])
        ->name('laporan.barang.kehilangan');
    // Foto 4: Form Penemuan
    Route::get('/lapor-barang-hilang/penemuan', [ReportController::class, 'createPenemuan'])
        ->name('laporan.barang.penemuan');
    // Proses Simpan Laporan
    Route::post('/lapor-barang-hilang/simpan', [ReportController::class, 'storeBarang'])
        ->name('laporan.barang.store');




    // ==========================================
    // RUTE KLAIM BARANG TEMUAN
    // ==========================================
    // Foto 1: Form Pengajuan Klaim
    Route::get('/lapor-barang-hilang/klaim/{report_id}', [ItemClaimController::class, 'create'])
        ->name('barang.klaim.create');
    Route::post('/lapor-barang-hilang/klaim/{report_id}', [ItemClaimController::class, 'store'])
        ->name('barang.klaim.store');
    // Foto 2, 3, 4, 5: Aktivitas & Stepper Klaim
    Route::get('/lapor-barang-hilang/aktivitas-klaim/{claim_id}', [ItemClaimController::class, 'aktivitas'])
        ->name('barang.klaim.aktivitas');
    // Foto 4 Action: Simpan Kesepakatan COD Pengambilan Barang
    Route::post('/lapor-barang-hilang/aktivitas-klaim/{claim_id}/kesepakatan', [ItemClaimController::class, 'simpanKesepakatan'])
        ->name('barang.klaim.kesepakatan');
    // Helper Khusus Demo Dosen Hari Senin (Bisa klik untuk simulasi penemu menerima)
    Route::get('/lapor-barang-hilang/aktivitas-klaim/{claim_id}/simulasi', [ItemClaimController::class, 'simulasiNextStep'])
        ->name('barang.klaim.simulasi');
    // Action Verifikasi Scan QR Serah Terima Selesai (Step 5 -> Step 6)
    Route::get('/lapor-barang-hilang/aktivitas-klaim/{claim}/konfirmasi-serah-terima', [ItemClaimController::class, 'konfirmasi'])
        ->name('barang.klaim.konfirmasi');
});

Route::middleware('auth')->prefix('laporan-barang/saya')->group(function () {
    Route::get('/', [LaporanSayaController::class, 'index'])->name('laporan.barang.saya');
    Route::get('/{report}', [LaporanSayaController::class, 'show'])->name('laporan.barang.saya.show');
    Route::delete('/{report}', [LaporanSayaController::class, 'destroy'])->name('laporan.barang.saya.destroy');
    Route::get('/{report}/tanggapan/{claim}', [LaporanSayaController::class, 'tanggapan'])->name('laporan.barang.tanggapan');
    Route::post('/{report}/tanggapan/{claim}/verifikasi', [LaporanSayaController::class, 'verifikasi'])->name('laporan.barang.tanggapan.verifikasi');
    Route::post('/{report}/tanggapan/{claim}/tolak', [LaporanSayaController::class, 'tolak'])->name('laporan.barang.tanggapan.tolak');
    Route::post('/notifikasi/{claim}/baca', [LaporanSayaController::class, 'baca'])->name('notifikasi.baca');
});

require __DIR__ . '/auth.php';
