<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ItemClaim;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaporanSayaController extends Controller
{
    public function index(Request $request)
    {
        $uid = auth()->id();
        $tab = $request->query('tab', 'semua');

        $milik = Report::barang()->where('user_id', $uid)
            ->withCount(['claims as klaim_baru' => fn($q) => $q->whereNull('reporter_seen_at')->whereNull('rejected_at')])
            ->get()
            ->map(fn($r) => [
                'jenis' => 'milik',
                'kode' => $r->ticket_code,
                'judul' => $r->title,
                'kind' => $r->item_kind,
                'category_id' => $r->category_id,
                'waktu' => $r->created_at,
                'baru' => $r->klaim_baru,
                'hidden' => (bool) $r->hidden_at,          // <- baris baru
                'url' => route('laporan.barang.saya.show', $r),
            ]);
        $menanggapi = ItemClaim::with('report')->where('user_id', $uid)->get()
            ->filter(fn($c) => $c->report)
            ->map(fn($c) => [
                'jenis' => 'menanggapi',
                'kode' => $c->report->ticket_code,
                'judul' => $c->report->title,
                'kind' => $c->report->item_kind,
                'category_id' => $c->report->category_id,
                'waktu' => $c->created_at,
                'baru' => 0,
                'url' => route('barang.klaim.aktivitas', $c), // <-- CEK nama route
            ]);

        $items = $milik->concat($menanggapi);

        if ($search = trim((string) $request->query('search'))) {
            $items = $items->filter(fn($i) => stripos($i['judul'] . ' ' . $i['kode'], $search) !== false);
        }
        if ($cat = $request->query('category_id')) {
            $items = $items->filter(fn($i) => (string) $i['category_id'] === (string) $cat);
        }

        $counts = [
            'semua' => $items->count(),
            'milik' => $items->where('jenis', 'milik')->count(),
            'menanggapi' => $items->where('jenis', 'menanggapi')->count(),
        ];

        if (in_array($tab, ['milik', 'menanggapi'])) {
            $items = $items->where('jenis', $tab);
        }

        $items = $request->query('sort') === 'terlama'
            ? $items->sortBy('waktu')
            : $items->sortByDesc('waktu');

        return view('barang.saya', [
            'items' => $items->values(),
            'counts' => $counts,
            'tab' => $tab,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Report $report)
    {
        $this->authorizeOwner($report);

        $claims = $report->claims()->with('user')->oldest()->get();
        $claims->each(fn($c, $i) => $c->nomor = $i + 1);

        return view('barang.saya-detail', [
            'report' => $report,
            'claims' => $claims->reverse()->values(),
        ]);
    }

    public function tanggapan(Report $report, ItemClaim $claim)
    {
        $this->authorizeOwner($report);
        abort_unless($claim->report_id === $report->id, 404);

        if (!$claim->reporter_seen_at) {
            $claim->forceFill(['reporter_seen_at' => now()])->save();
        }

        $nomor = $report->claims()->where('id', '<=', $claim->id)->count();
        $claim->load('user');

        $view = $report->item_kind === 'kehilangan' ? 'barang.tanggapan-kehilangan' : 'barang.tanggapan';
        return view($view, compact('report', 'claim', 'nomor'));
    }

    public function verifikasi(Report $report, ItemClaim $claim)
    {
        $this->authorizeOwner($report);
        abort_unless($claim->report_id === $report->id, 404);
        abort_unless($claim->current_step == 2 && !$claim->rejected_at, 422);

        if ($report->item_kind === 'kehilangan') {
            abort_unless($claim->owner_submitted_at, 422);
            $claim->forceFill(['owner_verified_at' => now()])->save();
            $claim->load('report');
            $claim->advanceIfBothVerified();

            return back()->with('success', 'Tanggapan diverifikasi.');
        }

        $claim->forceFill(['current_step' => 3])->save();

        return back()->with('success', 'Tanggapan diverifikasi. Pengklaim akan menghubungi Anda via WhatsApp untuk membuat kesepakatan.');
    }

    public function tolak(Request $request, Report $report, ItemClaim $claim)
    {
        $this->authorizeOwner($report);
        abort_unless($claim->report_id === $report->id, 404);
        abort_unless($claim->current_step <= 2 && !$claim->rejected_at, 422);

        $data = $request->validate(['rejection_reason' => ['nullable', 'string', 'max:255']]);

        $claim->forceFill([
            'rejected_at' => now(),
            'rejection_reason' => $data['rejection_reason'] ?? null,
        ])->save();

        return back()->with('success', 'Tanggapan ditolak.');
    }

    public function destroy(Report $report)
    {
        $this->authorizeOwner($report);

        if ($report->claims()->where('current_step', '>=', 5)->whereNull('rejected_at')->exists()) {
            return back()->with('error', 'Laporan tidak bisa dihapus karena sudah ada jadwal pengambilan.');
        }

        DB::transaction(function () use ($report) {
            if ($report->photo) {
                Storage::disk('public')->delete($report->photo);
            }
            $report->claims()->delete();
            $report->delete();
        });

        return redirect()->route('laporan.barang.saya')->with('success', 'Laporan berhasil dihapus.');
    }

    /** Dipanggil saat notifikasi ditutup (X). */
    public function baca(ItemClaim $claim)
    {
        $claim->loadMissing('report');
        $uid = auth()->id();

        if ($claim->report->user_id === $uid) {
            if (!$claim->reporter_seen_at) $claim->forceFill(['reporter_seen_at' => now()])->save();
        } elseif ($claim->user_id === $uid) {
            if (!$claim->claimant_seen_at) $claim->forceFill(['claimant_seen_at' => now()])->save();
        } else {
            abort(403);
        }

        return response()->noContent();
    }

    private function authorizeOwner(Report $report): void
    {
        abort_unless($report->user_id === auth()->id() && $report->type === 'barang_hilang', 403);
    }
    /** KEHILANGAN: pemilik mengirim berkas kepemilikannya. */
    public function kirimBerkas(Request $request, Report $report, ItemClaim $claim)
    {
        $this->authorizeOwner($report);
        abort_unless($claim->report_id === $report->id && $report->item_kind === 'kehilangan', 404);
        abort_unless($claim->current_step == 2 && !$claim->rejected_at && !$claim->owner_submitted_at, 422);

        $data = $request->validate([
            'owner_unique_feature' => 'required|string',
            'owner_inner_color'    => 'required|string',
            'owner_full_detail'    => 'required|string',
            'owner_proof_photo'    => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ], [
            'owner_unique_feature.required' => 'Identitas unik barang wajib diisi.',
            'owner_inner_color.required'    => 'Warna furing/lapisan dalam wajib diisi.',
            'owner_full_detail.required'    => 'Penjelasan rinci wajib diisi.',
            'owner_proof_photo.required'    => 'Foto lama bersama barang wajib diunggah.',
        ]);

        $path = $request->file('owner_proof_photo')->store('claims/owner-proofs', 'public');

        $claim->forceFill([
            'owner_unique_feature' => $data['owner_unique_feature'],
            'owner_inner_color'    => $data['owner_inner_color'],
            'owner_full_detail'    => $data['owner_full_detail'],
            'owner_proof_photo'    => $path,
            'owner_submitted_at'   => now(),
            'claimant_seen_at'     => null, // supaya penemu mendapat notifikasi
        ])->save();

        return back()->with('success', 'Berkas terkirim. Sekarang Anda bisa membuka tanggapan penemu.');
    }
}
