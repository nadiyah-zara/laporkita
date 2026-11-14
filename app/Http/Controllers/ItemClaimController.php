<?php

namespace App\Http\Controllers;

use App\Models\ItemClaim;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemClaimController extends Controller
{
    /**
     * Foto 1: Tampilkan Form Pengajuan Klaim Temuan
     */
    public function create($reportId)
    {
        $report = Report::barang()->findOrFail($reportId);
        abort_if($report->user_id === Auth::id(), 403, 'Anda tidak bisa menanggapi laporan sendiri.');

        $existingClaim = ItemClaim::where('report_id', $report->id)
            ->where('user_id', Auth::id())->first();

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
        abort_if($report->user_id === Auth::id(), 403, 'Anda tidak bisa menanggapi laporan sendiri.');
        $hilang = $report->item_kind === 'kehilangan';

        $validated = $request->validate([
            'secret_unique_feature' => 'required|string',
            'secret_inner_color'    => 'required|string',
            'secret_full_detail'    => 'required|string',
            'proof_photo'           => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'claimant_phone'        => $hilang ? 'required|string|max:25' : 'nullable',
        ], [
            'secret_unique_feature.required' => 'Identitas unik barang wajib diisi.',
            'secret_inner_color.required'    => 'Warna furing/lapisan bagian dalam wajib diisi.',
            'secret_full_detail.required'    => 'Rincian penjelasan wajib diisi.',
            'proof_photo.required'           => 'Foto/dokumen wajib diunggah.',
            'claimant_phone.required'        => 'Nomor WhatsApp aktif wajib diisi.',
        ]);

        $proofPath = $request->file('proof_photo')->store('claims/proofs', 'public');
        $claimCode = ($hilang ? '#BRGH' : '#BRGT') . date('ymd') . rand(100, 999);

        $claim = ItemClaim::create([
            'report_id'             => $report->id,
            'user_id'               => Auth::id(),
            'claim_code'            => $claimCode,
            'secret_unique_feature' => $validated['secret_unique_feature'],
            'secret_inner_color'    => $validated['secret_inner_color'],
            'secret_full_detail'    => $validated['secret_full_detail'],
            'proof_photo'           => $proofPath,
            'current_step'          => 2,
            'status'                => 'pending',
        ]);

        if ($hilang) {
            $claim->forceFill(['claimant_phone' => $validated['claimant_phone']])->save();
        }

        return redirect()->route('barang.klaim.aktivitas', $claim->id)
            ->with('success', $hilang
                ? 'Berkas Anda terkirim! Mohon tunggu pemilik mengirim berkas kepemilikannya.'
                : 'Berkas verifikasi klaim Anda berhasil dikirim! Mohon tunggu konfirmasi penemu.');
    }


    /**
     * Foto 2, 3, 4, 5: Halaman Aktivitas & Tracking Stepper Klaim
     */
    public function aktivitas($claimId)
    {
        $claim = ItemClaim::with(['report', 'user'])->findOrFail($claimId);

        if ($claim->user_id !== Auth::id() && $claim->report->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($claim->isKehilangan()) {
            // Pemilik laporan "Dicari" memakai halaman Tanggapan
            if ($claim->report->user_id === Auth::id()) {
                return redirect()->route('laporan.barang.tanggapan', [$claim->report_id, $claim->id]);
            }

            if (!$claim->claimant_seen_at) {
                $claim->forceFill(['claimant_seen_at' => now()])->save();
            }
            return view('barang.klaim-aktivitas-kehilangan', compact('claim'));
        }

        return view('barang.klaim-aktivitas', compact('claim'));
    }


    /**
     * Foto 4: Simpan Kesepakatan Jadwal & Tempat Pengambilan Fisik
     */
    public function simpanKesepakatan(Request $request, $claimId)
    {
        $claim = ItemClaim::with('report')->findOrFail($claimId);

        abort_unless(in_array(Auth::id(), [$claim->user_id, $claim->report->user_id]), 403);
        abort_if($claim->rejected_at || $claim->current_step < 3, 422);

        $validated = $request->validate([
            'pickup_location' => 'required|string|max:255',
            'pickup_date'     => 'required|date',
            'pickup_time'     => 'required|string',
        ]);

        $claim->update([
            'pickup_location' => $validated['pickup_location'],
            'pickup_date'     => $validated['pickup_date'],
            'pickup_time'     => $validated['pickup_time'],
            'current_step'    => 5,
            'status'          => 'ready_pickup',
        ]);

        return back()->with('success', 'Kesepakatan berhasil dikonfirmasi! QR tiket telah terbit.');
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
    public function konfirmasi(ItemClaim $claim)
    {
        $claim->load('report');
        $isFinder = $claim->finderId() === (int) auth()->id();

        // Hanya penemu yang boleh menyelesaikan. Mode local: dua pihak boleh (untuk demo).
        abort_unless($isFinder || app()->environment('local'), 403);

        if ($claim->current_step == 5 && !$claim->rejected_at) {
            DB::transaction(function () use ($claim) {
                $claim->forceFill(['current_step' => 6, 'status' => 'completed'])->save();
                $claim->report->forceFill(['status' => 'resolved'])->save();

                ItemClaim::where('report_id', $claim->report_id)
                    ->where('id', '!=', $claim->id)
                    ->whereNull('rejected_at')
                    ->update(['rejected_at' => now(), 'rejection_reason' => 'Barang sudah dikembalikan ke pemilik.']);
            });
        }

        $pageFinder = $claim->isKehilangan()
            ? route('barang.klaim.aktivitas', $claim->id)
            : route('laporan.barang.tanggapan', [$claim->report_id, $claim->id]);
        $pageOwner = $claim->isKehilangan()
            ? route('laporan.barang.tanggapan', [$claim->report_id, $claim->id])
            : route('barang.klaim.aktivitas', $claim->id);

        return redirect($isFinder ? $pageFinder : $pageOwner)->with('success', 'Serah terima selesai!');
    }

    /** KEHILANGAN: penemu memverifikasi berkas milik pelapor. */
    public function verifikasiBerkas(ItemClaim $claim)
    {
        $claim->load('report');
        abort_unless($claim->isKehilangan() && $claim->user_id === Auth::id(), 403);
        abort_unless($claim->current_step == 2 && $claim->owner_submitted_at && !$claim->rejected_at, 422);

        $claim->forceFill(['finder_verified_at' => now()])->save();
        $claim->advanceIfBothVerified();

        return back()->with('success', 'Berkas pelapor diverifikasi.');
    }

    /** KEHILANGAN: penemu menolak berkas milik pelapor. */
    public function tolakBerkas(Request $request, ItemClaim $claim)
    {
        $claim->load('report');
        abort_unless($claim->isKehilangan() && $claim->user_id === Auth::id(), 403);
        abort_unless($claim->current_step == 2 && !$claim->rejected_at, 422);

        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:255']]);

        $claim->forceFill([
            'rejected_at'      => now(),
            'rejection_reason' => $data['rejection_reason'] ?? null,
        ])->save();

        return back()->with('success', 'Berkas pelapor ditolak.');
    }
}
