<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ItemClaim;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.notification-bell', function ($view) {
            $notifs = collect();

            if (auth()->check()) {
                $uid = auth()->id();

                // 1. Laporan saya (temuan / kehilangan) ditanggapi seseorang
                $ditanggapi = ItemClaim::with('report')
                    ->whereNull('reporter_seen_at')->whereNull('rejected_at')
                    ->whereHas('report', fn($q) => $q->where('user_id', $uid))
                    ->latest()->get();

                foreach ($ditanggapi as $c) {
                    $jenis = $c->report->item_kind === 'kehilangan' ? 'Kehilangan' : 'Temuan';
                    $notifs->push([
                        'id' => 'claim-' . $c->id,
                        'title' => "Laporan {$jenis} Anda Ditanggapi Seseorang!",
                        'body' => 'Laporan “' . $c->report->title . ' (' . $c->report->ticket_code . ')” ditanggapi seseorang, cek sekarang!',
                        'url' => route('laporan.barang.tanggapan', [$c->report_id, $c->id]),
                        'read_url' => route('notifikasi.baca', $c),
                    ]);
                }

                // 2. Pemilik sudah mengirim berkas -> penemu diminta mencocokkan
                $berkasMasuk = ItemClaim::with('report')
                    ->where('user_id', $uid)->where('current_step', 2)
                    ->whereNull('rejected_at')->whereNull('claimant_seen_at')
                    ->whereNotNull('owner_submitted_at')
                    ->get();

                foreach ($berkasMasuk as $c) {
                    $notifs->push([
                        'id' => 'berkas-' . $c->id,
                        'title' => 'Pemilik Barang Mengirim Berkas',
                        'body' => 'Pemilik “' . $c->report->title . ' (' . $c->report->ticket_code . ')” sudah mengirim berkas. Cocokkan dengan barang yang Anda temukan.',
                        'url' => route('barang.klaim.aktivitas', $c),
                        'read_url' => route('notifikasi.baca', $c),
                    ]);
                }

                // 3. Pengingat jadwal pengambilan hari ini (kedua pihak)
                $jadwal = ItemClaim::with('report')
                    ->where('current_step', 5)->whereNull('rejected_at')
                    ->whereDate('pickup_date', today())
                    ->where(fn($q) => $q->where('user_id', $uid)
                        ->orWhereHas('report', fn($r) => $r->where('user_id', $uid)))
                    ->get();

                foreach ($jadwal as $c) {
                    $isReporter = $c->report->user_id === $uid;
                    $notifs->push([
                        'id' => 'jadwal-' . $c->id,
                        'title' => 'Jadwal Transaksi Pengembalian Barang',
                        'body' => 'Pengembalian “' . $c->report->title . ' (' . $c->report->ticket_code . ')” pada hari ini. Lokasi: '
                            . $c->pickup_location . ', Jam: ' . $c->pickup_time,
                        'url' => $isReporter
                            ? route('laporan.barang.tanggapan', [$c->report_id, $c->id])
                            : route('barang.klaim.aktivitas', $c),
                        'read_url' => null,
                    ]);
                }
            }

            $view->with('navNotifs', $notifs);
        });
    }
}
