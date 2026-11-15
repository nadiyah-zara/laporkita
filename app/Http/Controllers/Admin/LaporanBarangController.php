<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanBarangController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'semua');
        $aktif = fn() => Report::barang()->whereNull('hidden_at');
        $belumSelesai = ['resolved', 'done'];

        $counts = [
            'semua'         => $aktif()->count(),
            'dicari'        => $aktif()->where('item_kind', 'kehilangan')->whereNotIn('status', $belumSelesai)->count(),
            'ditemukan'     => $aktif()->where('item_kind', 'penemuan')->whereNotIn('status', $belumSelesai)->count(),
            'selesai'       => $aktif()->whereIn('status', $belumSelesai)->count(),
            'disembunyikan' => Report::barang()->whereNotNull('hidden_at')->count(),
        ];

        $query = Report::barang()->with(['category', 'user'])->withCount('claims');

        match ($tab) {
            'dicari'        => $query->whereNull('hidden_at')->where('item_kind', 'kehilangan')->whereNotIn('status', $belumSelesai),
            'ditemukan'     => $query->whereNull('hidden_at')->where('item_kind', 'penemuan')->whereNotIn('status', $belumSelesai),
            'selesai'       => $query->whereNull('hidden_at')->whereIn('status', $belumSelesai),
            'disembunyikan' => $query->whereNotNull('hidden_at'),
            default         => $query->whereNull('hidden_at'),
        };

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('admin.barang.index', compact('reports', 'counts', 'tab'));
    }

    public function show(Report $report)
    {
        $this->pastikanBarang($report);
        $report->load(['category', 'user']);
        $claims = $report->claims()->with('user')->latest()->get();

        return view('admin.barang.show', compact('report', 'claims'));
    }

    public function sembunyikan(Request $request, Report $report)
    {
        $this->pastikanBarang($report);
        abort_if($report->hidden_at, 422);

        $data = $request->validate(
            ['hidden_reason' => 'required|string|max:500'],
            ['hidden_reason.required' => 'Alasan penurunan laporan wajib diisi.']
        );

        DB::transaction(function () use ($report, $data) {
            $report->forceFill([
                'hidden_at'      => now(),
                'hidden_reason'  => $data['hidden_reason'],
                'hidden_seen_at' => null,
            ])->save();

            // Klaim yang belum selesai ikut ditutup
            $report->claims()
                ->whereNull('rejected_at')
                ->where('current_step', '<', 6)
                ->update([
                    'rejected_at'      => now(),
                    'rejection_reason' => 'Laporan diturunkan oleh admin.',
                ]);
        });

        return back()->with('success', 'Laporan disembunyikan dari publik.');
    }

    public function pulihkan(Report $report)
    {
        $this->pastikanBarang($report);
        abort_unless($report->hidden_at, 422);

        $report->forceFill(['hidden_at' => null, 'hidden_reason' => null])->save();

        return back()->with('success', 'Laporan dipulihkan dan tampil kembali di publik.');
    }

    private function pastikanBarang(Report $report): void
    {
        abort_unless($report->type === 'barang_hilang', 404);
    }
}
