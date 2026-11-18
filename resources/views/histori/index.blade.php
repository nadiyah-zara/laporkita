<x-public-layout title="Histori Laporan - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5">
                <a href="{{ route('beranda') }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            {{-- HEADER HALAMAN --}}
            <div class="mb-6 flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Histori Laporan Saya</h1>
                    <p class="text-xs text-slate-500">Arsip riwayat keluhan warga dan klaim barang temuan yang telah selesai atau ditutup.</p>
                </div>
            </div>

            {{-- RINGKASAN METRIK HISTORI --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @php
                    $summaryCards = [
                        ['label' => 'Total Tuntas', 'count' => $ringkasan['selesai'], 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                        ['label' => 'Infrastruktur', 'count' => $ringkasan['infrastruktur'], 'color' => 'text-[#0066FF]', 'bg' => 'bg-blue-50'],
                        ['label' => 'Barang Kembali', 'count' => $ringkasan['barang'], 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
                        ['label' => 'Laporan Ditolak', 'count' => $ringkasan['ditolak'], 'color' => 'text-rose-600', 'bg' => 'bg-rose-50'],
                    ];
                @endphp

                @foreach ($summaryCards as $card)
                    <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-sm">
                        <span class="inline-block text-[11px] font-semibold text-slate-500">{{ $card['label'] }}</span>
                        <div class="mt-3 flex items-baseline gap-1">
                            <span class="text-2xl font-extrabold tracking-tight {{ $card['color'] }}">{{ $card['count'] }}</span>
                            <span class="text-[11px] text-slate-400">laporan</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FILTER PENCARIAN & STATUS --}}
            <form method="GET" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-[1fr_200px]">
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                
                {{-- Input Pencarian --}}
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan judul laporan atau kode tiket..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-xs font-medium text-slate-800 shadow-sm transition focus:border-[#0066FF] focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                </div>

                {{-- Filter Dropdown Status --}}
                <select name="status" onchange="this.form.submit()" 
                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-[#0066FF] focus:outline-none">
                    <option value="semua" @selected($status === 'semua')>Semua Status</option>
                    <option value="selesai" @selected($status === 'selesai')>Status: Selesai</option>
                    <option value="ditolak" @selected($status === 'ditolak')>Status: Ditolak</option>
                </select>
            </form>

            {{-- TAB KATEGORI JENIS --}}
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @foreach (['semua' => 'Semua', 'infrastruktur' => 'Infrastruktur', 'barang' => 'Barang Hilang'] as $key => $label)
                    @php $isActive = ($jenis === $key); @endphp
                    <a href="{{ route('histori.index', array_filter(['jenis' => $key, 'status' => $status !== 'semua' ? $status : null, 'search' => request('search')])) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all {{ $isActive ? 'bg-[#0066FF] text-white shadow-sm shadow-blue-500/25' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                        <span>{{ $label }}</span>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $counts[$key] }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- DAFTAR ITEM HISTORI --}}
            <div class="mt-5 space-y-3.5">
                @forelse ($items as $item)
                    @php
                        $isSelesai = ($item['status'] === 'selesai');
                    @endphp

                    <a href="{{ $item['url'] }}" 
                       class="group block rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                            
                            {{-- Sisi Kiri: Kode, Peran, Judul --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900 group-hover:text-[#0066FF] transition-colors">
                                        {{ $item['kode'] }}
                                    </span>
                                    
                                    <span class="rounded-md border border-blue-100 bg-blue-50/70 px-2 py-0.5 text-[10px] font-bold text-[#0066FF]">
                                        {{ $item['peran'] === 'milik' ? 'Laporan Saya' : 'Klaim / Tanggapan' }}
                                    </span>
                                </div>

                                <h2 class="mt-2 text-base font-bold text-slate-800 group-hover:text-[#0066FF] transition-colors">
                                    {{ $item['judul'] }}
                                </h2>

                                @if ($item['kategori'])
                                    <p class="mt-1 text-xs text-slate-400">
                                        Kategori: <span class="font-medium text-slate-600">{{ $item['kategori'] }}</span>
                                    </p>
                                @endif
                            </div>

                            {{-- Sisi Kanan: Badges & Timestamp --}}
                            <div class="flex shrink-0 flex-col items-start gap-2 border-t border-slate-100 pt-3 sm:items-end sm:border-0 sm:pt-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    {{-- Badge Jenis --}}
                                    @if ($item['jenis'] === 'infrastruktur')
                                        <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            Infrastruktur
                                        </span>
                                    @elseif ($item['kind'] === 'penemuan')
                                        <span class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-[#0066FF]">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Ditemukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-600">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            Dicari
                                        </span>
                                    @endif

                                    {{-- Badge Status --}}
                                    @if ($isSelesai)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </div>

                                {{-- Waktu Penutupan --}}
                                <div class="flex items-center gap-1 text-[11px] text-slate-400">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>
                                        {{ $isSelesai ? 'Selesai' : 'Ditutup' }}: 
                                        {{ $item['waktu']->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB 
                                        ({{ $item['waktu']->diffForHumans() }})
                                    </span>
                                </div>
                            </div>

                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-14 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-700">Belum Ada Histori Laporan</p>
                        <p class="mt-0.5 text-xs text-slate-400">Laporan yang telah tuntas ditangani atau ditutup akan terarsip otomatis di sini.</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINASI --}}
            <div class="mt-6">
                {{ $items->links() }}
            </div>
        </div>
    </main>
</x-public-layout>