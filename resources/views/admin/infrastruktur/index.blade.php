@php
    $badge = [
        'pending'     => ['Menunggu Verifikasi', 'bg-amber-50 text-amber-700 border-amber-200/60', 'bg-amber-500'],
        'verified'    => ['Terverifikasi', 'bg-blue-50 text-[#0066FF] border-blue-200/60', 'bg-[#0066FF]'],
        'in_progress' => ['Dikerjakan', 'bg-indigo-50 text-indigo-700 border-indigo-200/60', 'bg-indigo-500'],
        'done'        => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'bg-emerald-500'],
        'resolved'    => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'bg-emerald-500'],
        'rejected'    => ['Ditolak', 'bg-rose-50 text-rose-700 border-rose-200/60', 'bg-rose-500'],
    ];
    $tabs = [
        'semua'       => 'Semua', 
        'pending'     => 'Menunggu', 
        'verified'    => 'Terverifikasi', 
        'in_progress' => 'Dikerjakan', 
        'done'        => 'Selesai', 
        'rejected'    => 'Ditolak'
    ];
@endphp

<x-admin-layout title="Laporan Infrastruktur - Admin LaporKita">

    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Laporan Infrastruktur</h1>
                <p class="text-xs text-slate-500">Periksa validitas, tugaskan tim lapangan, dan pantau penanganan masalah warga.</p>
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

    {{-- TAB STATUS LAPORAN --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabs as $key => $label)
            @php $isActive = ($status === $key); @endphp
            <a href="{{ route('admin.infrastruktur.index', array_filter(['status' => $key === 'semua' ? null : $key, 'search' => request('search'), 'urgency' => request('urgency'), 'category_id' => request('category_id')])) }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all {{ $isActive ? 'bg-[#0066FF] text-white shadow-sm shadow-blue-500/25' : 'border border-slate-200/80 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                <span>{{ $label }}</span>
                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                    {{ $counts[$key] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- FILTER PENCARIAN & KATEGORI --}}
    <form method="GET" class="mt-4 grid grid-cols-1 gap-3 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-sm md:grid-cols-4">
        <input type="hidden" name="status" value="{{ $status === 'semua' ? '' : $status }}">
        
        {{-- Input Pencarian --}}
        <div class="relative md:col-span-2">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul laporan, kode tiket, atau alamat..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-10 pr-4 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
        </div>

        {{-- Dropdown Urgensi --}}
        <select name="urgency" onchange="this.form.submit()" 
                class="rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition focus:border-[#0066FF] focus:bg-white focus:outline-none">
            <option value="">Semua Tingkat Urgensi</option>
            <option value="kritis" @selected(request('urgency') === 'kritis')>Prioritas Kritis</option>
            <option value="standar" @selected(request('urgency') === 'standar')>Prioritas Standar</option>
        </select>

        {{-- Dropdown Kategori --}}
        <select name="category_id" onchange="this.form.submit()" 
                class="rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition focus:border-[#0066FF] focus:bg-white focus:outline-none">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </form>

    {{-- TABEL DATA LAPORAN --}}
    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-xs">
                <thead class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Kode Tiket</th>
                        <th class="px-5 py-3.5">Judul & Lokasi</th>
                        <th class="px-5 py-3.5">Status Penanganan</th>
                        <th class="px-5 py-3.5">Waktu Masuk</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $r)
                        @php 
                            [$label, $badgeStyle, $dotStyle] = $badge[$r->status] ?? [$r->status, 'bg-slate-100 text-slate-700 border-slate-200', 'bg-slate-400']; 
                        @endphp
                        <tr class="transition-colors hover:bg-slate-50/60">
                            {{-- Kolom Tiket --}}
                            <td class="px-5 py-4 align-top">
                                <span class="font-mono text-xs font-bold text-slate-900">{{ $r->ticket_code ?? '-' }}</span>
                                @if ($r->urgency === 'kritis')
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-rose-600">
                                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-rose-500"></span>
                                            Kritis
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Kolom Judul & Lokasi --}}
                            <td class="px-5 py-4 align-top">
                                <p class="font-bold text-slate-800">{{ $r->title }}</p>
                                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <span class="font-medium text-slate-600">{{ $r->category?->name ?? 'Umum' }}</span>
                                    <span>·</span>
                                    <span class="truncate max-w-xs">{{ \Illuminate\Support\Str::limit($r->address, 45) }}</span>
                                </div>
                            </td>

                            {{-- Kolom Status --}}
                            <td class="px-5 py-4 align-top">
                                <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-[11px] font-bold {{ $badgeStyle }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dotStyle }}"></span>
                                    {{ $label }}
                                </span>
                                @if ($r->status === 'in_progress')
                                    <div class="mt-2.5 flex items-center gap-2">
                                        <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-indigo-500 transition-all duration-300" style="width: {{ $r->progress_percentage ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-indigo-600">{{ $r->progress_percentage ?? 0 }}%</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Kolom Waktu --}}
                            <td class="px-5 py-4 align-top text-[11px] text-slate-500">
                                <div class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $r->created_at->diffForHumans() }}</span>
                                </div>
                            </td>

                            {{-- Kolom Aksi --}}
                            <td class="px-5 py-4 text-right align-top">
                                <a href="{{ route('admin.infrastruktur.show', $r) }}" 
                                   class="inline-flex items-center gap-1.5 rounded-xl {{ $r->status === 'pending' ? 'bg-[#0066FF] hover:bg-blue-700 text-white' : 'border border-slate-200 bg-white hover:bg-slate-50 text-slate-700' }} px-3.5 py-1.5 text-[11px] font-bold shadow-xs transition active:scale-95">
                                    <span>{{ $r->status === 'pending' ? 'Tinjau' : 'Detail' }}</span>
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-xs font-semibold text-slate-700">Tidak ada laporan yang ditemukan</p>
                                <p class="mt-0.5 text-[11px] text-slate-400">Coba sesuaikan kata kunci pencarian atau filter status Anda.</p>
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