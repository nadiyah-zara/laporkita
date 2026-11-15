<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemClaim;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), ['aktif', 'diblokir']) ? $request->query('tab') : 'semua';

        $counts = [
            'semua'    => User::count(),
            'aktif'    => User::whereNull('blocked_at')->count(),
            'diblokir' => User::whereNotNull('blocked_at')->count(),
        ];

        $query = User::query();

        if ($tab === 'aktif') {
            $query->whereNull('blocked_at');
        } elseif ($tab === 'diblokir') {
            $query->whereNotNull('blocked_at');
        }

        if ($search = trim((string) $request->query('search'))) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $ids = $users->pluck('id');

        $laporan = Report::whereIn('user_id', $ids)->select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')->pluck('total', 'user_id');
        $klaim = ItemClaim::whereIn('user_id', $ids)->select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')->pluck('total', 'user_id');

        return view('admin.pengguna.index', compact('users', 'counts', 'tab', 'laporan', 'klaim'));
    }

    public function blokir(Request $request, User $user)
    {
        abort_if($user->role === 'admin' || $user->id === auth()->id(), 403, 'Akun admin tidak dapat diblokir.');

        $data = $request->validate(
            ['blocked_reason' => 'required|string|max:255'],
            ['blocked_reason.required' => 'Alasan pemblokiran wajib diisi.']
        );

        $user->forceFill(['blocked_at' => now(), 'blocked_reason' => $data['blocked_reason']])->save();

        // Keluarkan langsung dari semua sesi login yang aktif
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', $user->name . ' diblokir.');
    }

    public function buka(User $user)
    {
        $user->forceFill(['blocked_at' => null, 'blocked_reason' => null])->save();

        return back()->with('success', 'Blokir ' . $user->name . ' dibuka.');
    }
}