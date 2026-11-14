<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        // 1. Ambil MAKSIMAL 5 laporan terbaru yang AKTIF (bukan resolved/done)
        //    supaya laporan yang sudah selesai tidak tampil di feed beranda
        $laporanTerbaru = Report::with('category')
            ->whereNotIn('status', ['resolved', 'done'])
            ->latest()
            ->take(5)
            ->get();

        // 2. Ambil 1 laporan AKTIF terbaru untuk Floating Map Hero
        //    hanya yang masih aktif (bukan selesai) agar peta tidak menampilkan
        //    kasus yang sudah tuntas
        $laporanPetaHero = Report::with('category')
            ->whereNotNull('address')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotIn('status', ['resolved', 'done'])
            ->latest()
            ->first();

        // 3. Hitung statistik riil dari database
        $totalLaporan    = Report::count();
        $mingguIni       = Report::where('created_at', '>=', now()->subDays(7))->count();
        $barangKembali   = Report::barang()->whereIn('status', ['done', 'resolved'])->count();
        $infraSelesai    = Report::infrastruktur()->whereIn('status', ['in_progress', 'done', 'resolved'])->count();

        $totalSelesai    = Report::whereIn('status', ['done', 'resolved'])->count();
        $persenTuntas    = $totalLaporan > 0 ? round(($totalSelesai / $totalLaporan) * 100, 1) : 0;

        $stats = [
            'total'          => $totalLaporan,
            'minggu_ini'     => $mingguIni,
            'barang_kembali' => $barangKembali,
            'infra_selesai'  => $infraSelesai,
            'persen'         => $persenTuntas,
        ];

        return view('beranda', compact('laporanTerbaru', 'laporanPetaHero', 'stats'));
    }
}