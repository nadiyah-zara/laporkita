<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetaLayananController extends Controller
{
    /** Titik pusat peta (Politeknik Negeri Medan). Ganti sesuai kebutuhan. */
    private const CENTER = ['lat' => 3.5617, 'lng' => 98.6568];

    /** Pilihan radius (km) untuk tombol "Perluas Radar". */
    private const RADIUS_STEPS = [2.5, 5, 10, 25];

    /** Layer yang tersedia. Yang pertama jadi default. */
    private const LAYERS = ['barang', 'infrastruktur', 'semua'];

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

        $reports = Report::query()
            ->tayang()
            ->withinRadius(self::CENTER['lat'], self::CENTER['lng'], $radius)
            ->when($layer === 'barang', fn ($q) => $q->barang())
            ->when($layer === 'infrastruktur', fn ($q) => $q->infrastruktur())
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        $markers = $reports->map(fn (Report $r) => [
            'id' => $r->id,
            // ditemukan | dicari | infrastruktur
            'type' => $r->type === 'infrastruktur' ? 'infrastruktur' : $r->item_kind,
            'status' => $r->status,
            'title' => $r->title,
            'description' => $r->description,
            'location' => $r->address,
            'lat' => $r->latitude,
            'lng' => $r->longitude,
            'time' => $r->created_at->diffForHumans(),
        ])->values();

        $nextRadius = collect(self::RADIUS_STEPS)->first(fn ($r) => $r > $radius)
            ?? self::RADIUS_STEPS[0];

        return view('peta-layanan', [
            'reports' => $reports,
            'markers' => $markers,
            'center' => self::CENTER,
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