<x-admin-layout title="Kelola Pengguna - Admin LaporKita">

    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Data Pengguna</h1>
                <p class="text-xs text-slate-500">Kelola akun warga terdaftar dan moderasi pengguna yang melanggar ketentuan.</p>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-700 shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- TAB STATUS PENGGUNA --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach (['semua' => 'Semua', 'aktif' => 'Aktif', 'diblokir' => 'Diblokir'] as $key => $label)
            @php $isActive = ($tab === $key); @endphp
            <a href="{{ route('admin.pengguna.index', array_filter(['tab' => $key === 'semua' ? null : $key, 'search' => request('search')])) }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all {{ $isActive ? 'bg-[#0066FF] text-white shadow-sm shadow-blue-500/25' : 'border border-slate-200/80 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                <span>{{ $label }}</span>
                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $counts[$key] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- FORM PENCARIAN --}}
    <form method="GET" class="mt-4 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-sm">
        <input type="hidden" name="tab" value="{{ $tab === 'semua' ? '' : $tab }}">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama lengkap atau alamat email pengguna..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-10 pr-4 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
        </div>
    </form>

    {{-- TABEL PENGGUNA --}}
    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-xs">
                <thead class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Profil Pengguna</th>
                        <th class="px-5 py-3.5">Laporan</th>
                        <th class="px-5 py-3.5">Klaim / Respon</th>
                        <th class="px-5 py-3.5">Tgl Bergabung</th>
                        <th class="px-5 py-3.5">Status Akun</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100" x-data="{ open: null }">
                    @forelse ($users as $u)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            {{-- Profil --}}
                            <td class="px-5 py-4 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 font-bold text-[#0066FF]">
                                        {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="font-bold text-slate-900 truncate">{{ $u->name }}</p>
                                            @if ($u->role === 'admin')
                                                <span class="rounded-md bg-indigo-50 px-2 py-0.2 text-[10px] font-bold text-indigo-600 border border-indigo-100">Admin</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Laporan --}}
                            <td class="px-5 py-4 align-top font-bold text-slate-700">
                                {{ $laporan[$u->id] ?? 0 }}
                            </td>

                            {{-- Tanggapan --}}
                            <td class="px-5 py-4 align-top font-bold text-slate-700">
                                {{ $klaim[$u->id] ?? 0 }}
                            </td>

                            {{-- Bergabung --}}
                            <td class="px-5 py-4 align-top text-[11px] text-slate-500">
                                {{ $u->created_at?->timezone('Asia/Jakarta')->translatedFormat('d M Y') ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4 align-top">
                                @if ($u->blocked_at)
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Diblokir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-right align-top">
                                @if ($u->role === 'admin')
                                    <span class="text-[11px] text-slate-300">-</span>
                                @elseif ($u->blocked_at)
                                    <form method="POST" action="{{ route('admin.pengguna.buka', $u) }}" onsubmit="return confirm('Buka kembali blokir pada akun ini?')">
                                        @csrf
                                        <button class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-700 active:scale-95">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                            </svg>
                                            <span>Buka Blokir</span>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" @click="open = open === {{ $u->id }} ? null : {{ $u->id }}"
                                        class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50/60 px-3 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100 active:scale-95">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <span>Blokir</span>
                                    </button>
                                @endif
                            </td>
                        </tr>

                        {{-- Catatan Alasan Blokir --}}
                        @if ($u->blocked_at)
                            <tr class="bg-rose-50/40">
                                <td colspan="6" class="px-5 py-2.5 text-[11px] text-rose-700 border-t border-rose-100">
                                    <span class="font-bold">Alasan Diblokir:</span> {{ $u->blocked_reason }}
                                </td>
                            </tr>
                        @endif

                        {{-- Baris Form Input Alasan Blokir --}}
                        <tr x-show="open === {{ $u->id }}" x-cloak class="bg-slate-50/90 border-t border-slate-200">
                            <td colspan="6" class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.pengguna.blokir', $u) }}" class="flex flex-wrap items-center gap-2.5">
                                    @csrf
                                    <input type="text" name="blocked_reason" required maxlength="255" placeholder="Tuliskan alasan pemblokiran akun ini..."
                                        class="min-w-[260px] flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-800 transition focus:border-rose-500 focus:outline-none">
                                    <button class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-rose-700 active:scale-95"
                                        onclick="return confirm('Blokir akun ini? Sesi pengguna akan langsung diputus.')">
                                        Konfirmasi Blokir
                                    </button>
                                    <button type="button" @click="open = null" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100">
                                        Batal
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <p class="text-xs font-semibold text-slate-600">Tidak ada pengguna ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINASI --}}
    <div class="mt-5">
        {{ $users->links() }}
    </div>
</x-admin-layout>