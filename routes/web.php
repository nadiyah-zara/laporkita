<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PetaLayananController;
use App\Http\Controllers\ItemClaimController;
use App\Http\Controllers\LaporanSayaController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\HistoriController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LaporanInfrastrukturController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Admin\LaporanBarangController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Auth\AdminLoginController;

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

Route::get('/dokumentasi-penanganan', [DokumentasiController::class, 'index'])
    ->name('dokumentasi.index');

Route::get('/petugas/kamera/{ticket}/{token}', [App\Http\Controllers\PetugasLapangController::class, 'showCamera'])
    ->name('petugas.kamera.show');
Route::post('/petugas/kamera/{ticket}/{token}', [App\Http\Controllers\PetugasLapangController::class, 'storeUpdate'])
    ->name('petugas.kamera.store');

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

    Route::get('/histori-laporan', [HistoriController::class, 'index'])->name('histori.index');


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

    // Action Verifikasi Scan QR Serah Terima Selesai (Step 5 -> Step 6)
    Route::get('/lapor-barang-hilang/aktivitas-klaim/{claim}/konfirmasi-serah-terima', [ItemClaimController::class, 'konfirmasi'])
        ->name('barang.klaim.konfirmasi');
    Route::post('/lapor-barang-hilang/aktivitas-klaim/{claim}/verifikasi-berkas', [ItemClaimController::class, 'verifikasiBerkas'])
        ->name('barang.klaim.verifikasi');
    Route::post('/lapor-barang-hilang/aktivitas-klaim/{claim}/tolak-berkas', [ItemClaimController::class, 'tolakBerkas'])
        ->name('barang.klaim.tolak');

    Route::post('/lapor-barang-hilang/aktivitas-klaim/{claim}/qr', [ItemClaimController::class, 'tampilkanQr'])
        ->name('barang.klaim.qr');
    Route::post('/lapor-barang-hilang/aktivitas-klaim/{claim}/qr-reset', [ItemClaimController::class, 'resetQr'])
        ->name('barang.klaim.qr.reset');

    // =========NOTIFIKASI (BISA DIBACA OLEH PEMILIK LAPORAN & PEMILIK KLAIM)==========
    Route::post('/notifikasi/log/{log}/baca', [NotifikasiController::class, 'logBaca'])->name('notifikasi.log.baca');
    Route::post('/notifikasi/laporan/{report}/baca', [NotifikasiController::class, 'laporanBaca'])->name('notifikasi.laporan.baca');
});

Route::middleware('auth')->prefix('laporan-barang/saya')->group(function () {
    Route::get('/', [LaporanSayaController::class, 'index'])->name('laporan.barang.saya');
    Route::get('/{report}', [LaporanSayaController::class, 'show'])->name('laporan.barang.saya.show');
    Route::delete('/{report}', [LaporanSayaController::class, 'destroy'])->name('laporan.barang.saya.destroy');
    Route::get('/{report}/tanggapan/{claim}', [LaporanSayaController::class, 'tanggapan'])->name('laporan.barang.tanggapan');
    Route::post('/{report}/tanggapan/{claim}/verifikasi', [LaporanSayaController::class, 'verifikasi'])->name('laporan.barang.tanggapan.verifikasi');
    Route::post('/{report}/tanggapan/{claim}/tolak', [LaporanSayaController::class, 'tolak'])->name('laporan.barang.tanggapan.tolak');
    Route::post('/notifikasi/{claim}/baca', [LaporanSayaController::class, 'baca'])->name('notifikasi.baca');
    Route::post('/{report}/tanggapan/{claim}/berkas', [LaporanSayaController::class, 'kirimBerkas'])
        ->name('laporan.barang.tanggapan.berkas');
});

Route::prefix('panel-x7k2')->name('admin.')->group(function () {
    // halaman login admin (publik, tapi di path rahasia)
    Route::get('/masuk', [AdminLoginController::class, 'show'])->name('login');
    Route::post('/masuk', [AdminLoginController::class, 'store'])->name('login.store');

    // halaman admin (dijaga)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Laporan infrastruktur
        Route::prefix('laporan-infrastruktur')->name('infrastruktur.')->group(function () {
            Route::get('/', [LaporanInfrastrukturController::class, 'index'])->name('index');
            Route::get('/{report}', [LaporanInfrastrukturController::class, 'show'])->name('show');
            Route::post('/{report}/verifikasi', [LaporanInfrastrukturController::class, 'verifikasi'])->name('verifikasi');
            Route::post('/{report}/tolak', [LaporanInfrastrukturController::class, 'tolak'])->name('tolak');
            Route::post('/{report}/tugaskan', [LaporanInfrastrukturController::class, 'tugaskan'])->name('tugaskan');
            Route::post('/{report}/progres', [LaporanInfrastrukturController::class, 'progres'])->name('progres');
            Route::post('/{report}/selesai', [LaporanInfrastrukturController::class, 'selesai'])->name('selesai');
        });

        // Barang hilang (moderasi)
        Route::prefix('barang-hilang')->name('barang.')->group(function () {
            Route::get('/', [LaporanBarangController::class, 'index'])->name('index');
            Route::get('/{report}', [LaporanBarangController::class, 'show'])->name('show');
            Route::post('/{report}/sembunyikan', [LaporanBarangController::class, 'sembunyikan'])->name('sembunyikan');
            Route::post('/{report}/pulihkan', [LaporanBarangController::class, 'pulihkan'])->name('pulihkan');
        });

        // Kategori
        Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy']);

        // Pengguna
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna/{user}/blokir', [PenggunaController::class, 'blokir'])->name('pengguna.blokir');
        Route::post('/pengguna/{user}/buka', [PenggunaController::class, 'buka'])->name('pengguna.buka');

        // Rekap
        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/rekap/ekspor', [RekapController::class, 'ekspor'])->name('rekap.ekspor');

        // Profil admin
        Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
        Route::patch('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'password'])->name('profil.password');

        //Petugas
        Route::get('/petugas/kamera/{ticket}/{token}', [App\Http\Controllers\PetugasLapangController::class, 'showCamera'])->name('petugas.kamera.show');
        Route::post('/petugas/kamera/{ticket}/{token}', [App\Http\Controllers\PetugasLapangController::class, 'storeUpdate'])->name('petugas.kamera.store');
    });
});
require __DIR__ . '/auth.php';
