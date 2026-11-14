<?php

namespace App\Http\Controllers;

use App\Models\ItemClaim;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoriController extends Controller
{
    public function index(Request $request)
    {
        $uid = auth()->id();

        $jenis = in_array($request->query('jenis'), ['infrastruktur', 'barang']) ? $request->query('jenis') : 'semua';
        $status = in_array($request->query('status'), ['selesai', 'ditolak']) ? $request->query('status') : 'semua';

        // 1. Laporan milikku yang sudah berakhir (selesai / ditolak)
        $milik = Report::with('category')
            ->where('user_id', $uid)
            ->whereIn('status', ['done', 'resolved', 'rejected'])
            ->get()
            ->map(function ($r) {
                $infra = $r->type === 'infrastruktur';

                return [
                    'jenis'    => $infra ? 'infrastruktur' : 'barang',
                    'peran'    => 'milik',
                    'kind'     => $infra ? null : $r->item_kind,
                    'kode'     => $r->ticket_code,
                    'judul'    => $r->title,
                    'kategori' => $r->category?->name,
                    'status'   => $r->status === 'rejected' ? 'ditolak' : 'selesai',
                    'waktu'    => $r->updated_at,
                    'url'      => $infra
                        ? route('laporan.infrastruktur.detail', $r->id)
                        : route('laporan.barang.saya.show', $r),
                ];
            });

        // 2. Tanggapan/klaim yang pernah kukirim ke laporan orang lain (selesai / ditolak)
        $menanggapi = ItemClaim::with('report.category')
            ->where('user_id', $uid)
            ->where(fn ($q) => $q->where('current_step', '>=', 6)->orWhereNotNull('rejected_at'))
            ->get()
            ->filter(fn ($c) => $c->report)
            ->map(fn ($c) => [
                'jenis'    => 'barang',
                'peran'    => 'menanggapi',
                'kind'     => $c->report->item_kind,
                'kode'     => $c->report->ticket_code,
                'judul'    => $c->report->title,
                'kategori' => $c->report->category?->name,
                'status'   => $c->rejected_at ? 'ditolak' : 'selesai',
                'waktu'    => $c->updated_at,
                'url'      => route('barang.klaim.aktivitas', $c->id),
            ]);

        $semua = $milik->concat($menanggapi);

        // Ringkasan (dari seluruh data, tidak terpengaruh filter)
        $ringkasan = [
            'selesai'       => $semua->where('status', 'selesai')->count(),
            'infrastruktur' => $semua->where('jenis', 'infrastruktur')->where('status', 'selesai')->count(),
            'barang'        => $semua->where('jenis', 'barang')->where('status', 'selesai')->count(),
            'ditolak'       => $semua->where('status', 'ditolak')->count(),
        ];

        $counts = [
            'semua'         => $semua->count(),
            'infrastruktur' => $semua->where('jenis', 'infrastruktur')->count(),
            'barang'        => $semua->where('jenis', 'barang')->count(),
        ];

        // Filter
        $items = $semua;
        if ($jenis !== 'semua') {
            $items = $items->where('jenis', $jenis);
        }
        if ($status !== 'semua') {
            $items = $items->where('status', $status);
        }
        if ($search = trim((string) $request->query('search'))) {
            $items = $items->filter(fn ($i) => stripos($i['judul'] . ' ' . $i['kode'], $search) !== false);
        }

        $items = $items->sortByDesc('waktu')->values();

        // Paginasi manual (data berasal dari 2 sumber)
        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('histori.index', [
            'items'     => $paginator,
            'jenis'     => $jenis,
            'status'    => $status,
            'counts'    => $counts,
            'ringkasan' => $ringkasan,
        ]);
    }
}