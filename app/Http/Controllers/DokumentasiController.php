<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class DokumentasiController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->query('jenis', 'semua');
        if (!in_array($jenis, ['semua', 'infrastruktur', 'barang'])) {
            $jenis = 'semua';
        }

        $counts = [
            'semua'         => Report::selesai()->count(),
            'infrastruktur' => Report::selesai()->infrastruktur()->count(),
            'barang'        => Report::selesai()->barang()->count(),
        ];

        $query = Report::selesai()->with('category')->latest('updated_at');

        if ($jenis === 'infrastruktur') {
            $query->infrastruktur();
        } elseif ($jenis === 'barang') {
            $query->barang();
        }

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(9)->withQueryString();

        return view('dokumentasi.index', compact('reports', 'jenis', 'counts'));
    }
}