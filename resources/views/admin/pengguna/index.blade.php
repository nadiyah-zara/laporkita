<x-admin-layout title="Pengguna - Admin LaporKita">

    <div class="mb-6 flex items-center gap-3">
        <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Pengguna</h1>
            <p class="text-xs text-slate-500">Daftar akun warga. Blokir akun yang menyalahgunakan layanan.</p>
        </div>
    </div>

    @if (session('success')) <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div> @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    {{-- TAB --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach (['semua' => 'Semua', 'aktif' => 'Aktif', 'diblokir' => 'Diblokir'] as $key => $label)
            <a href="{{ route('admin.pengguna.index', array_filter(['tab' => $key === 'semua' ? null : $key, 'search' => request('search')])) }}"
                class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[11px] font-bold {{ $tab === $key ? 'bg-[#0c203b] text-white' : 'bg-white text-slate-600 shadow-sm hover:bg-slate-50' }}">
                {{ $label }}
                <span class="rounded-full px-1.5 text-[10px] {{ $tab === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    <form method="GET" class="mt-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
        <input type="hidden" name="tab" value="{{ $tab === 'semua' ? '' : $tab }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-blue-600 focus:bg-white focus:outline-none">
    </form>

    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table class="w-full min-w-[760px] text-left text-xs">
            <thead class="border-b border-slate-100 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Pengguna</th>
                    <th class="px-5 py-3">Laporan</th>
                    <th class="px-5 py-3">Tanggapan</th>
                    <th class="px-5 py-3">Bergabung</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100" x-data="{ open: null }">
                @forelse ($users as $u)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-4 align-top">
                            <p class="font-bold text-slate-800">{{ $u->name }}
                                @if ($u->role === 'admin') <span class="ml-1 rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-600">Admin</span> @endif
                            </p>
                            <p class="text-[11px] text-slate-400">{{ $u->email }}</p>
                        </td>
                        <td class="px-5 py-4 align-top font-bold text-slate-700">{{ $laporan[$u->id] ?? 0 }}</td>
                        <td class="px-5 py-4 align-top font-bold text-slate-700">{{ $klaim[$u->id] ?? 0 }}</td>
                        <td class="px-5 py-4 align-top text-[11px] text-slate-500">{{ $u->created_at?->timezone('Asia/Jakarta')->format('d M Y') }}</td>
                        <td class="px-5 py-4 align-top">
                            @if ($u->blocked_at)
                                <span class="rounded-md bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-600">Diblokir</span>
                            @else
                                <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-600">Aktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right align-top">
                            @if ($u->role === 'admin')
                                <span class="text-[11px] text-slate-300">-</span>
                            @elseif ($u->blocked_at)
                                <form method="POST" action="{{ route('admin.pengguna.buka', $u) }}" onsubmit="return confirm('Buka blokir akun ini?')">
                                    @csrf
                                    <button class="rounded-lg bg-emerald-600 px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-emerald-700">Buka Blokir</button>
                                </form>
                            @else
                                <button type="button" @click="open = open === {{ $u->id }} ? null : {{ $u->id }}"
                                    class="rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-1.5 text-[11px] font-bold text-rose-600 hover:bg-rose-100">Blokir</button>
                            @endif
                        </td>
                    </tr>

                    @if ($u->blocked_at)
                        <tr><td colspan="6" class="bg-rose-50/40 px-5 py-2 text-[11px] text-rose-600">Alasan: {{ $u->blocked_reason }}</td></tr>
                    @endif

                    <tr x-show="open === {{ $u->id }}" x-cloak>
                        <td colspan="6" class="bg-slate-50 px-5 py-4">
                            <form method="POST" action="{{ route('admin.pengguna.blokir', $u) }}" class="flex flex-wrap items-center gap-2">
                                @csrf
                                <input type="text" name="blocked_reason" required maxlength="255" placeholder="Alasan pemblokiran"
                                    class="min-w-[240px] flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:border-rose-500 focus:outline-none">
                                <button class="rounded-lg bg-rose-600 px-4 py-2 text-[11px] font-bold text-white hover:bg-rose-700"
                                    onclick="return confirm('Blokir akun ini? Dia akan langsung dikeluarkan.')">Konfirmasi</button>
                                <button type="button" @click="open = null" class="rounded-lg bg-slate-200 px-4 py-2 text-[11px] font-bold text-slate-600">Batal</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-14 text-center text-slate-400">Tidak ada pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
</x-admin-layout>