<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class PetugasLapangController extends Controller
{
    /**
     * Validasi Token Khusus Petugas Lapangan (Magic Link)
     */
    private function validateToken(string $ticket, string $token): Report
    {
        // Cari baik jika pakai '#' maupun tanpa '#'
        $report = Report::infrastruktur()
            ->where(function ($q) use ($ticket) {
                $q->where('ticket_code', $ticket)
                  ->orWhere('ticket_code', '#' . $ticket)
                  ->orWhere('ticket_code', str_replace('#', '', $ticket))
                  ->orWhere('id', $ticket);
            })
            ->firstOrFail();

        // Validasi token MD5
        $validToken = md5($report->id . 'laporkita');
        if ($token !== $validToken) {
            abort(403, 'Tautan tugas tidak valid atau sudah kedaluwarsa.');
        }

        return $report;
    }

    /**
     * Tampilan Kamera di Browser HP Petugas
     */
    public function showCamera(string $ticket, string $token)
    {
        $report = $this->validateToken($ticket, $token);
        return view('petugas.kamera', compact('report'));
    }

    /**
     * Simpan Update Lapangan (Survey / Pengerjaan) dari HP Petugas
     */
    public function storeUpdate(Request $request, string $ticket, string $token)
    {
        $report = $this->validateToken($ticket, $token);

        $request->validate([
            'foto_lapangan' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'keterangan'    => 'required|string|max:1000',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'captured_at'   => 'nullable|string',
        ]);

        // 1. Simpan file foto pengerjaan ke storage
        $path = $request->file('foto_lapangan')->store('reports/worklogs', 'public');

        // 2. Tentukan tahapan otomatis:
        // Jika status masih pending/awal -> Tahap 2: Survey Tim
        // Jika sudah pengerjaan -> Tahap 5: Pengerjaan
        $isSurvey = ($report->status === 'pending' || (int) ($report->current_step ?? 1) < 3);
        $step = $isSurvey ? 2 : 5;
        $judulCatatan = $isSurvey ? 'Survey Tim Lapangan' : 'Update Pengerjaan Lapangan';

        // 3. Update HANYA kolom yang memang ada di tabel reports
        $report->forceFill([
            'supervisor_notes' => $request->keterangan,
            'current_step'     => max((int) ($report->current_step ?? 1), $step),
        ])->save();

        // 4. Catat riwayat foto & catatan ke tabel logs (menggunakan method catat() bawaan Anda)
        $gpsInfo = ($request->latitude && $request->longitude) 
            ? " (GPS: {$request->latitude}, {$request->longitude})" 
            : "";

        // Method bawaan model Report Anda: catat($judul, $deskripsi, $foto)
        $report->catat($judulCatatan, $request->keterangan . $gpsInfo, $path);

        return back()->with('success', 'Update lapangan berhasil dikirim dan tersinkronisasi ke sistem.');
    }
}