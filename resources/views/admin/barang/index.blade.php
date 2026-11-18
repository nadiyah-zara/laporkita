@php
    $tabs = [
        'semua'          => 'Semua', 
        'dicari'         => 'Dicari', 
        'ditemukan'      => 'Ditemukan', 
        'selesai'        => 'Selesai', 
        'disembunyikan'  => 'Disembunyikan'
    ];
@endphp

<x-admin-layout title="Barang Hilang & Temuan - Admin LaporKita">

    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Barang Hilang & Ditemukan</h1>
                <p class="text-xs text-slate-500">Moderasi laporan antarwarga serta awasi klaim kepemilikan agar terhindar dari spam.</p>
            </div>
        </div>
    </div>

    {{-- ALERT SUKSES --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- TAB KATEGORI STATUS --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabs as $key => $label)
            @php $isActive = ($tab === $key); @endphp
            <a href="{{ route('admin.barang.index', array_filter(['tab' => $key === 'semua' ? null : $key, 'search' => request('search')])) }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all {{ $isActive ? 'bg-[#0066FF] text-white shadow-sm shadow-blue-500/25' : 'border border-slate-200/80 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                <span>{{ $label }}</span>
                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $counts[$key] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- FORMULIR PENCARIAN --}}
    <form method="GET" class="mt-4 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-sm">
        <input type="hidden" name="tab" value="{{ $tab === 'semua' ? '' : $tab }}">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan judul barang, kode tiket, atau lokasi kejadian..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-10 pr-4 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
        </div>
    </form>

    {{-- TABEL DATA BARANG --}}
    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-xs">
                <thead class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Kode Tiket</th>
                        <th class="px-5 py-3.5">Rincian Barang</th>
                        <th class="px-5 py-3.5">Status Publikasi</th>
                        <th class="px-5 py-3.5">Tanggapan</th>
                        <th class="px-5 py-3.5">Waktu Masuk</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $r)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            {{-- Kode Tiket & Jenis Barang --}}
                            <td class="px-5 py-4 align-top">
                                <span class="font-mono text-xs font-bold text-slate-900">{{ $r->ticket_code }}</span>
                                <div class="mt-1">
                                    @if ($r->item_kind === 'penemuan')
                                        <span class="inline-flex items-center gap-1 rounded-md border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-[#0066FF]">
                                            <span class="h-1.5 w-1.5 rounded-full bg-[#0066FF]"></span>
                                            Ditemukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-md border border-rose-100 bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Dicari
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Judul & Pembuat Laporan --}}
                            <td class="px-5 py-4 align-top">
                                <p class="font-bold text-slate-800">{{ $r->title }}</p>
                                <div class="mt-0.5 text-[11px] text-slate-400">
                                    <span>{{ $r->category?->name ?? 'Umum' }}</span>
                                    <span>·</span>
                                    <span>{{ \Illuminate\Support\Str::limit($r->address, 40) }}</span>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Pelapor: <span class="font-medium text-slate-700">{{ $r->user?->name ?? $r->reporter_name }}</span>
                                </p>
                            </td>

                            {{-- Status Moderasi / Selesai --}}
                            <td class="px-5 py-4 align-top">
                                @if ($r->hidden_at)
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Disembunyikan
                                    </span>
                                @elseif (in_array($r->status, ['resolved', 'done']))
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-[#0066FF]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#0066FF]"></span>
                                        Aktif Publik
                                    </span>
                                @endif
                            </td>

                            {{-- Jumlah Klaim / Tanggapan --}}
                            <td class="px-5 py-4 align-top">
                                <div class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100/80 px-2.5 py-1 text-xs font-bold text-slate-700">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>{{ $r->claims_count }} klaim</span>
                                </div>
                            </td>

                            {{-- Waktu Masuk --}}
                            <td class="px-5 py-4 align-top text-[11px] text-slate-500">
                                <div class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $r->created_at->diffForHumans() }}</span>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-right align-top">
                                <a href="{{ route('admin.barang.show', $r) }}" 
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-[11px] font-bold text-slate-700 shadow-xs transition hover:border-[#0066FF] hover:bg-blue-50/40 hover:text-[#0066FF] active:scale-95">
                                    <span>Tinjau</span>
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-xs font-semibold text-slate-700">Tidak ada laporan barang pada kategori ini</p>
                                <p class="mt-0.5 text-[11px] text-slate-400">Silakan pilih tab status lain atau ubah kata kunci pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINASI --}}
    <div class="mt-5">
        {{ $reports->links() }}
    </div>
</x-admin-layout>