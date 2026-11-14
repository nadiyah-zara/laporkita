<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $infra = fn () => Report::infrastruktur();

        $stats = [
            'pending'     => $infra()->where('status', 'pending')->count(),
            'verified'    => $infra()->where('status', 'verified')->count(),
            'in_progress' => $infra()->where('status', 'in_progress')->count(),
            'selesai'     => $infra()->whereIn('status', ['done', 'resolved'])->count(),
            'rejected'    => $infra()->where('status', 'rejected')->count(),
        ];

        $butuhTindakan = Report::infrastruktur()
            ->with('category')
            ->where('status', 'pending')
            ->orderByRaw("urgency = 'kritis' desc")
            ->latest()
            ->limit(10)
            ->get();

        $barang = [
            'aktif'   => Report::barang()->whereNotIn('status', ['resolved', 'done'])->count(),
            'selesai' => Report::barang()->whereIn('status', ['resolved', 'done'])->count(),
        ];

        return view('admin.dashboard', compact('stats', 'butuhTindakan', 'barang'));
    }
}