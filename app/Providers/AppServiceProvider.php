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

                // 1. Laporan temuan saya ditanggapi
                ItemClaim::with('report')
                    ->whereNull('reporter_seen_at')->whereNull('rejected_at')
                    ->whereHas('report', fn($q) => $q->where('user_id', $uid)->where('item_kind', 'penemuan'))
                    ->latest()->get()
                    ->each(fn($c) => $notifs->push([
                        'id' => 'claim-' . $c->id,
                        'title' => 'Laporan Temuan Anda Ditanggapi Seseorang!',
                        'body' => 'Laporan “' . $c->report->title . ' (' . $c->report->ticket_code . ')” ditanggapi seseorang, cek sekarang!',
                        'url' => route('laporan.barang.tanggapan', [$c->report_id, $c->id]),
                        'read_url' => route('notifikasi.baca', $c),
                    ]));

                // 2. Pengingat jadwal pengambilan hari ini (penemu & pengklaim)
                ItemClaim::with('report')
                    ->where('current_step', 5)->whereNull('rejected_at')
                    ->whereDate('pickup_date', today())
                    ->where(fn($q) => $q->where('user_id', $uid)
                        ->orWhereHas('report', fn($r) => $r->where('user_id', $uid)))
                    ->get()
                    ->each(fn($c) => $notifs->push([
                        'id' => 'jadwal-' . $c->id,
                        'title' => 'Jadwal Transaksi Pengembalian Barang',
                        'body' => 'Pengembalian “' . $c->report->title . ' (' . $c->report->ticket_code . ')” pada hari ini. Lokasi: '
                            . $c->pickup_location . ', Jam: ' . $c->pickup_time,
                        'url' => $c->report->user_id === $uid
                            ? route('laporan.barang.tanggapan', [$c->report_id, $c->id])
                            : route('barang.klaim.aktivitas', $c), // <-- CEK
                        'read_url' => null,
                    ]));
            }

            $view->with('navNotifs', $notifs);
        });
    }
}
