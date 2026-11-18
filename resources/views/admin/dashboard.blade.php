<x-admin-layout title="Dashboard - Admin LaporKita">

    {{-- HEADER DASHBOARD --}}
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Ringkasan Dashboard</h1>
                <p class="text-xs text-slate-500">Pantau dan kelola laporan infrastruktur publik terkini dari warga.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-medium text-slate-600 shadow-sm">
            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
            Sistem Pemantauan Aktif
        </div>
    </div>

    {{-- KARTU STATUS (METRICS OVERVIEW) - SUDAH DIPERBAIKI PADDING & HIERARKI ANGKA --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        @php
            $cardConfigs = [
                ['label' => 'Menunggu Verifikasi', 'count' => $stats['pending'], 'color' => 'text-amber-600', 'bg' => 'bg-amber-50', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Terverifikasi', 'count' => $stats['verified'], 'color' => 'text-[#0066FF]', 'bg' => 'bg-blue-50', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Sedang Dikerjakan', 'count' => $stats['in_progress'], 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                ['label' => 'Selesai', 'count' => $stats['selesai'], 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'icon' => 'M5 13l4 4L19 7'],
                ['label' => 'Ditolak', 'count' => $stats['rejected'], 'color' => 'text-rose-600', 'bg' => 'bg-rose-50', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp

        @foreach ($cardConfigs as $c)
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            {{-- Label & Ikon --}}
            <div class="flex items-start justify-between gap-2">
                <span class="inline-flex rounded-lg {{ $c['bg'] }} px-2.5 py-1 text-[11px] font-semibold tracking-wide {{ $c['color'] }}">
                    {{ $c['label'] }}
                </span>
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $c['bg'] }} {{ $c['color'] }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $c['icon'] }}" />
                    </svg>
                </div>
            </div>

            {{-- Angka & Keterangan (Tersusun rapi dengan margin bawah yang pas) --}}
            <div class="mt-5 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold tracking-tight text-slate-800">{{ $c['count'] }}</span>
                <span class="text-xs font-medium text-slate-400">laporan</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="mt-7 grid gap-6 lg:grid-cols-3">

        {{-- PERLU TINDAKAN (VERIFIKASI) --}}
        <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-slate-900">Perlu Segera Diverifikasi</h2>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    {{ $stats['pending'] }} Menunggu
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($butuhTindakan as $r)
                <div class="group flex flex-col justify-between gap-3 py-4 transition-colors hover:bg-slate-50/50 sm:flex-row sm:items-center">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-md border border-blue-100 bg-blue-50/70 px-2 py-0.5 font-mono text-xs font-bold text-[#0066FF]">
                                {{ $r->ticket_code ?? 'Tanpa kode' }}
                            </span>
                            
                            @if ($r->urgency === 'kritis')
                            <span class="inline-flex items-center gap-1 rounded-md border border-rose-100 bg-rose-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-rose-600">
                                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-rose-500"></span>
                                Kritis
                            </span>
                            @endif

                            @if ($r->category)
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                                {{ $r->category->name }}
                            </span>
                            @endif
                        </div>

                        <h3 class="mt-1.5 truncate text-sm font-semibold text-slate-800 transition-colors group-hover:text-[#0066FF]">
                            {{ $r->title }}
                        </h3>

                        <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $r->address }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center justify-between gap-4 border-t border-slate-100 pt-2 sm:border-0 sm:pt-0 sm:flex-col sm:items-end">
                        <div class="flex items-center gap-1 text-[11px] text-slate-400">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $r->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('admin.infrastruktur.show', $r) }}" 
                           class="inline-flex items-center gap-1.5 rounded-lg bg-[#0066FF] px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-95">
                            <span>Tinjau</span>
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-xs font-semibold text-slate-700">Semua Laporan Tertangani</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">Tidak ada laporan baru yang menunggu verifikasi saat ini.</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- BARANG HILANG & DITEMUKAN --}}
        <section class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <div>
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-4">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-[#0066FF]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Barang Hilang & Temuan</h2>
                        <p class="text-[11px] text-slate-400">Pantauan aktivitas antarwarga.</p>
                    </div>
                </div>

                {{-- Status Grid Ringkas --}}
                <div class="mt-5 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-blue-100 bg-blue-50/50 p-3.5">
                            <span class="block text-[11px] font-medium text-slate-500">Laporan Aktif</span>
                            <span class="mt-1 block text-2xl font-black text-[#0066FF]">{{ $barang['aktif'] }}</span>
                        </div>
                        <div class="rounded-xl border border-rose-100 bg-rose-50/50 p-3.5">
                            <span class="block text-[11px] font-medium text-slate-500">Disembunyikan</span>
                            <span class="mt-1 block text-2xl font-black text-rose-600">{{ $barang['disembunyikan'] }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50/40 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-700">Berhasil Dikembalikan</span>
                        </div>
                        <span class="text-lg font-bold text-emerald-600">{{ $barang['selesai'] }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4">
                <a href="{{ route('admin.barang.index') }}" 
                   class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition-all hover:border-[#0066FF] hover:bg-blue-50/30 hover:text-[#0066FF]">
                    <span>Kelola Laporan Barang</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </section>

    </div>
</x-admin-layout>