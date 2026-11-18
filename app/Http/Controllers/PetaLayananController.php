<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetaLayananController extends Controller
{
    /** Titik pusat default (Politeknik Negeri Medan). */
    private const CENTER = ['lat' => 3.5617, 'lng' => 98.6568];

    /** Pilihan radius (km). */
    private const RADIUS_STEPS = [2.5, 5, 10, 25];

    /** Default layer dibuat 'semua' agar semua laporan langsung muncul */
    private const LAYERS = ['semua', 'infrastruktur', 'barang'];

    public function index(Request $request): View
    {
        $layer = in_array($request->query('layer'), self::LAYERS, true)
            ? $request->query('layer')
            : self::LAYERS[0];

        $radius = (float) $request->query('radius', self::RADIUS_STEPS[0]);
        if (! in_array($radius, self::RADIUS_STEPS, true)) {
            $radius = self::RADIUS_STEPS[0];
        }

        $search = trim((string) $request->query('q', ''));

        // Query laporan
        $reportsQuery = Report::query()->tayang();

        // Jika tidak sedang mencari teks spesifik, gunakan batas radius
        if ($search === '') {
            $reportsQuery->withinRadius(self::CENTER['lat'], self::CENTER['lng'], $radius);
        } else {
            // Jika mencari nama lokasi/laporan, cari di judul, deskripsi, dan alamat
            $reportsQuery->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $reports = $reportsQuery
            ->when($layer === 'barang', fn ($q) => $q->barang())
            ->when($layer === 'infrastruktur', fn ($q) => $q->infrastruktur())
            ->latest()
            ->get();

        // Tentukan pusat peta: jika ada hasil pencarian, pusatkan ke laporan pertama
        $center = self::CENTER;
        if ($search !== '' && $reports->isNotEmpty()) {
            $center = [
                'lat' => (float) $reports->first()->latitude,
                'lng' => (float) $reports->first()->longitude,
            ];
        }

        // Mapping marker dengan perbaikan type & penambahan url
        $markers = $reports->map(function (Report $r) {
            // Normalisasi tipe ke: 'infrastruktur', 'ditemukan', atau 'dicari'
            $markerType = 'infrastruktur';
            if ($r->type !== 'infrastruktur') {
                $markerType = ($r->item_kind === 'penemuan') ? 'ditemukan' : 'dicari';
            }

            return [
                'id' => $r->id,
                'type' => $markerType,
                'status' => $r->status,
                'title' => $r->title,
                'description' => $r->description,
                'location' => $r->address,
                'lat' => (float) $r->latitude,
                'lng' => (float) $r->longitude,
                'time' => $r->created_at->diffForHumans(),
                'url' => $r->type === 'infrastruktur'
                    ? route('laporan.infrastruktur.detail', $r->id)
                    : route('laporan.barang.index'),
            ];
        })->values();

        $nextRadius = collect(self::RADIUS_STEPS)->first(fn ($r) => $r > $radius)
            ?? self::RADIUS_STEPS[0];

        return view('peta-layanan', [
            'reports' => $reports,
            'markers' => $markers,
            'center' => $center,
            'layer' => $layer,
            'defaultLayer' => self::LAYERS[0],
            'search' => $search,
            'radius' => $radius,
            'radiusLabel' => rtrim(rtrim(number_format($radius, 1, ',', ''), '0'), ','),
            'nextRadius' => $nextRadius,
            'canExpand' => $nextRadius > $radius,
        ]);
    }
}