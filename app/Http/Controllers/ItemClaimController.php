<?php

namespace App\Http\Controllers;

use App\Models\ItemClaim;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemClaimController extends Controller
{
    /**
     * Foto 1: Tampilkan Form Pengajuan Klaim Temuan
     */
    public function create($reportId)
    {
        $report = Report::barang()->findOrFail($reportId);

        // Jika user ini sudah pernah mengajukan klaim untuk barang ini, langsung arahkan ke halaman aktivitasnya
        $existingClaim = ItemClaim::where('report_id', $report->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingClaim) {
            return redirect()->route('barang.klaim.aktivitas', $existingClaim->id);
        }

        return view('barang.klaim-form', compact('report'));
    }

    /**
     * Foto 1 (Action): Simpan Data Form Klaim
     */
    public function store(Request $request, $reportId)
    {
        $report = Report::barang()->findOrFail($reportId);

        $validated = $request->validate([
            'secret_unique_feature' => 'required|string',
            'secret_inner_color'     => 'required|string',
            'secret_full_detail'     => 'required|string',
            'proof_photo'           => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ], [
            'secret_unique_feature.required' => 'Identitas unik barang wajib diisi.',
            'secret_inner_color.required'     => 'Warna furing/lapisan bagian dalam wajib diisi.',
            'secret_full_detail.required'     => 'Rincian penjelasan kepemilikan wajib diisi.',
            'proof_photo.required'           => 'Foto/dokumen bukti kepemilikan lama wajib diunggah.',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPath = $request->file('proof_photo')->store('claims/proofs', 'public');
        }

        // Generate Kode Klaim sesuai pola gambar: #BRGT261211001
        $claimCode = '#BRGT' . date('ymd') . rand(100, 999);

        $claim = ItemClaim::create([
            'report_id'              => $report->id,
            'user_id'                => Auth::id(),
            'claim_code'             => $claimCode,
            'secret_unique_feature' => $validated['secret_unique_feature'],
            'secret_inner_color'     => $validated['secret_inner_color'],
            'secret_full_detail'     => $validated['secret_full_detail'],
            'proof_photo'            => $proofPath,
            'current_step'           => 2, // Masuk ke Step 2: Terkirim & Belum Diverifikasi
            'status'                 => 'pending',
        ]);

        return redirect()->route('barang.klaim.aktivitas', $claim->id)
            ->with('success', 'Berkas verifikasi klaim Anda berhasil dikirim! Mohon tunggu konfirmasi penemu.');
    }

    /**
     * Foto 2, 3, 4, 5: Halaman Aktivitas & Tracking Stepper Klaim
     */
    public function aktivitas($claimId)
    {
        $claim = ItemClaim::with(['report', 'user'])->findOrFail($claimId);

        // Pastikan hanya pemilik klaim atau pembuat laporan yang bisa melihat halaman ini
        if ($claim->user_id !== Auth::id() && $claim->report->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return view('barang.klaim-aktivitas', compact('claim'));
    }

    /**
     * Foto 4: Simpan Kesepakatan Jadwal & Tempat Pengambilan Fisik
     */
    public function simpanKesepakatan(Request $request, $claimId)
    {
        $claim = ItemClaim::findOrFail($claimId);

        $validated = $request->validate([
            'pickup_location' => 'required|string|max:255',
            'pickup_date'     => 'required|date',
            'pickup_time'     => 'required|string',
        ]);

        $claim->update([
            'pickup_location' => $validated['pickup_location'],
            'pickup_date'     => $validated['pickup_date'],
            'pickup_time'     => $validated['pickup_time'],
            'current_step'    => 5, // Lanjut ke Step 5: QR Tiket Pengambilan (Foto 5)
            'status'          => 'ready_pickup',
        ]);

        return back()->with('success', 'Kesepakatan berhasil dikonfirmasi! QR Tiket Anda telah terbit.');
    }

    /**
     * Helper Simulasi Demo Dosen: Majukan Step Stepper Secara Instan
     */
    public function simulasiNextStep($claimId)
    {
        $claim = ItemClaim::with('report')->findOrFail($claimId);

        if ($claim->current_step < 6) {
            $next = $claim->current_step + 1;
            $claim->update([
                'current_step' => $next,
                'status' => match ($next) {
                    3 => 'verified',
                    4 => 'agreement',
                    5 => 'ready_pickup',
                    6 => 'completed',
                    default => 'pending'
                }
            ]);

            if ($next === 6) {
                $claim->report->update(['status' => 'resolved']);
            }
        }

        return back()->with('success', "Simulasi berhasil! Alur klaim kini di Step {$claim->current_step}.");
    }

    /**
     * Verifikasi Serah Terima Selesai (Hasil Scan QR Penemu) -> Step 6: Selesai
     */
    public function konfirmasiSerahTerima($claimId)
    {
        $claim = ItemClaim::with('report')->findOrFail($claimId);

        $claim->update([
            'current_step' => 6,
            'status'       => 'completed',
        ]);

        $claim->report->update([
            'status' => 'resolved',
        ]);

        return redirect()->route('barang.klaim.aktivitas', $claim->id)
            ->with('success', 'Luar biasa! Serah terima barang berhasil diverifikasi. Laporan kini resmi SELESAI!');
    }

    /**
     * TANGGAPAN DETAIL: Halaman review klaim dari sisi PELAPOR (orang yang menemukan/kehilangan)
     * Pelapor bisa melihat detail klaim dan memverifikasi/menolak
     */
    public function tanggapanDetail($claimId)
    {
        $claim = ItemClaim::with(['report', 'user'])->findOrFail($claimId);

        // Pastikan yang melihat adalah pemilik laporan ATAU pemilik klaim
        if ($claim->report->user_id !== Auth::id() && $claim->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return view('barang.tanggapan-detail', compact('claim'));
    }

    /**
     * VERIFIKASI TANGGAPAN: Pelapor menerima klaim -> step maju ke 3 (verified)
     */
    public function verifikasiTanggapan($claimId)
    {
        $claim = ItemClaim::with('report')->findOrFail($claimId);

        // Hanya pemilik laporan yang bisa verifikasi
        if ($claim->report->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $claim->update([
            'current_step' => 3,
            'status'       => 'verified',
        ]);

        return redirect()->route('laporan.barang.saya.detail', $claim->report_id)
            ->with('success', 'Tanggapan berhasil diverifikasi! Pengklaim bisa lanjut ke tahap kesepakatan.');
    }

    /**
     * TOLAK TANGGAPAN: Pelapor menolak klaim
     */
    public function tolakTanggapan($claimId)
    {
        $claim = ItemClaim::with('report')->findOrFail($claimId);

        // Hanya pemilik laporan yang bisa menolak
        if ($claim->report->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $claim->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('laporan.barang.saya.detail', $claim->report_id)
            ->with('success', 'Tanggapan telah ditolak.');
    }
}
