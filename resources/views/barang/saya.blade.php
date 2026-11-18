<x-public-layout title="Laporan Barang Saya - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5">
                <a href="{{ route('laporan.barang.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Laporan Barang</span>
                </a>
            </div>

            {{-- HEADER HALAMAN --}}
            <div class="mb-6 flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kelola Laporan Barang Saya</h1>
                    <p class="text-xs text-slate-500">Pantau laporan kehilangan atau temuan yang Anda buat dan kelola klaim dari warga.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
                    <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- FILTER PENCARIAN, KATEGORI, DAN SORTING --}}
            <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_200px_160px]">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan judul barang..."
                        class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-xs font-medium text-slate-800 shadow-sm transition focus:border-[#0066FF] focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                </div>

                <select name="category_id" onchange="this.form.submit()" 
                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-[#0066FF] focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="sort" onchange="this.form.submit()" 
                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition focus:border-[#0066FF] focus:outline-none">
                    <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Paling Terbaru</option>
                    <option value="terlama" @selected(request('sort') === 'terlama')>Paling Lama</option>
                </select>
            </form>

            {{-- TAB SELEKSI --}}
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @foreach ([['semua', 'Semua'], ['milik', 'Laporan Saya'], ['menanggapi', 'Menanggapi']] as [$key, $label])
                    @php $isActive = ($tab === $key); @endphp
                    <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all {{ $isActive ? 'bg-[#0066FF] text-white shadow-sm shadow-blue-500/25' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                        <span>{{ $label }}</span>
                        <span class="rounded-full px-2 py-0.2 text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $counts[$key] }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- DAFTAR LAPORAN --}}
            <div class="mt-5 space-y-3.5">
                @forelse ($items as $item)
                    <a href="{{ $item['url'] }}" 
                       class="group block rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900 group-hover:text-[#0066FF] transition-colors">
                                        {{ $item['kode'] }}
                                    </span>
                                    <span class="rounded-md border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-[#0066FF]">
                                        {{ $item['jenis'] === 'milik' ? 'Laporan Saya' : 'Menanggapi' }}
                                    </span>
                                    
                                    @if (!empty($item['hidden']))
                                        <span class="rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">Disembunyikan</span>
                                    @endif
                                    
                                    @if ($item['baru'] > 0)
                                        <span class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                            {{ $item['baru'] }} tanggapan baru
                                        </span>
                                    @endif
                                </div>
                                <h2 class="mt-2 text-base font-bold text-slate-800 group-hover:text-[#0066FF] transition-colors">
                                    {{ $item['judul'] }}
                                </h2>
                            </div>

                            <div class="flex shrink-0 flex-col items-start gap-1.5 border-t border-slate-100 pt-3 sm:items-end sm:border-0 sm:pt-0">
                                @if ($item['kind'] === 'penemuan')
                                    <span class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-[#0066FF]">
                                        <svg class="h-3.5 w-3.5 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Ditemukan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-600">
                                        <svg class="h-3.5 w-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        Dicari
                                    </span>
                                @endif
                                <p class="text-[11px] text-slate-400">
                                    Diajukan: {{ $item['waktu']->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-14 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-700">Belum Ada Laporan</p>
                        <p class="mt-0.5 text-xs text-slate-400">Laporan barang yang Anda buat akan terdaftar di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</x-public-layout>