<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    private const SELESAI = "SUM(CASE WHEN status IN ('done','resolved') THEN 1 ELSE 0 END)";

    public function index(Request $request)
    {
        $tahun = $this->tahun($request);

        $perBulan = Report::whereNull('hidden_at')->whereYear('created_at', $tahun)
            ->selectRaw('MONTH(created_at) as bulan, type, COUNT(*) as total, ' . self::SELESAI . ' as selesai')
            ->groupBy('bulan', 'type')->get();

        $bulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulan[$i] = ['infra_masuk' => 0, 'infra_selesai' => 0, 'barang_masuk' => 0, 'barang_selesai' => 0];
        }
        foreach ($perBulan as $r) {
            $p = $r->type === 'infrastruktur' ? 'infra' : 'barang';
            $bulan[(int) $r->bulan][$p . '_masuk'] = (int) $r->total;
            $bulan[(int) $r->bulan][$p . '_selesai'] = (int) $r->selesai;
        }

        $total = collect($bulan)->reduce(fn ($c, $b) => [
            'infra_masuk'    => $c['infra_masuk'] + $b['infra_masuk'],
            'infra_selesai'  => $c['infra_selesai'] + $b['infra_selesai'],
            'barang_masuk'   => $c['barang_masuk'] + $b['barang_masuk'],
            'barang_selesai' => $c['barang_selesai'] + $b['barang_selesai'],
        ], ['infra_masuk' => 0, 'infra_selesai' => 0, 'barang_masuk' => 0, 'barang_selesai' => 0]);

        $rataHari = Report::whereNull('hidden_at')->whereYear('created_at', $tahun)
            ->infrastruktur()->whereIn('status', ['done', 'resolved'])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as rata')->value('rata');

        $perKategori = Report::query()
            ->join('categories', 'categories.id', '=', 'reports.category_id')
            ->whereNull('reports.hidden_at')->whereYear('reports.created_at', $tahun)
            ->selectRaw("categories.name as nama, reports.type as tipe, COUNT(*) as total,
                SUM(CASE WHEN reports.status IN ('done','resolved') THEN 1 ELSE 0 END) as selesai")
            ->groupBy('categories.name', 'reports.type')
            ->orderByDesc('total')->get();

        $awal = optional(Report::min('created_at'))
            ? \Carbon\Carbon::parse(Report::min('created_at'))->year : now()->year;
        $tahunList = range(now()->year, min($awal, now()->year));

        return view('admin.rekap.index', compact('tahun', 'tahunList', 'bulan', 'total', 'rataHari', 'perKategori'));
    }

    public function ekspor(Request $request)
    {
        $tahun = $this->tahun($request);

        return response()->streamDownload(function () use ($tahun) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // agar Excel membaca UTF-8
            fputcsv($out, ['Kode Tiket', 'Jenis', 'Judul', 'Kategori', 'Status', 'Tanggal Masuk', 'Terakhir Diperbarui', 'Alamat', 'Petugas'], ',');

            Report::with('category')->whereNull('hidden_at')->whereYear('created_at', $tahun)
                ->orderBy('created_at')->lazy()
                ->each(function ($r) use ($out) {
                    fputcsv($out, [
                        $r->ticket_code,
                        $r->type === 'infrastruktur' ? 'Infrastruktur' : ($r->item_kind === 'penemuan' ? 'Barang Ditemukan' : 'Barang Dicari'),
                        $this->aman($r->title),
                        $this->aman($r->category?->name),
                        match ($r->status) {
                            'pending' => 'Menunggu Verifikasi',
                            'verified' => $r->type === 'infrastruktur' ? 'Terverifikasi' : 'Aktif',
                            'in_progress' => 'Dikerjakan',
                            'done', 'resolved' => 'Selesai',
                            'rejected' => 'Ditolak',
                            default => $r->status,
                        },
                        $r->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i'),
                        $r->updated_at->timezone('Asia/Jakarta')->format('Y-m-d H:i'),
                        $this->aman($r->address),
                        $this->aman($r->supervisor_name),
                    ], ';');
                });

            fclose($out);
        }, "rekap-laporan-{$tahun}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function tahun(Request $request): int
    {
        $t = (int) $request->query('tahun', now()->year);

        return ($t >= 2000 && $t <= 2100) ? $t : now()->year;
    }

    /** Cegah teks yang diawali = + - @ dibaca Excel sebagai rumus. */
    private function aman(?string $v): string
    {
        $v = (string) $v;

        return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
    }
}