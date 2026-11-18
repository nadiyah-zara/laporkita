@php
    // Set false kalau tidak ingin foto barang temuan/hilang tampil di publik
    // (misalnya khawatir fotonya berisi KTP, ATM, atau dokumen pribadi).
    $tampilFotoBarang = true;

    $foto = fn ($path) => $path ? asset('storage/' . $path) : null;
@endphp

<x-public-layout title="Dokumentasi Penanganan - LaporKita">
    <main class="min-h-screen bg-[#f8fafc]">

        {{-- HERO HEADER --}}
        <section class="border-b border-slate-200/80 bg-white py-12 md:py-16">
            <div class="mx-auto max-w-5xl px-6">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/70 px-3 py-1 text-xs font-semibold text-[#0066FF]">
                    <span class="h-2 w-2 rounded-full bg-[#0066FF]"></span>
                    Transparansi Kerja Nyata
                </div>

                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Dokumentasi Hasil Penanganan
                </h1>
                <p class="mt-2.5 max-w-2xl text-sm leading-relaxed text-slate-500">
                    Kumpulan bukti penyelesaian laporan infrastruktur dan serah-terima barang temuan yang telah berhasil dituntaskan bersama masyarakat.
                </p>

                {{-- FILTER TAB & PENCARIAN --}}
                <div class="mt-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach (['semua' => 'Semua Laporan', 'infrastruktur' => 'Infrastruktur', 'barang' => 'Barang Hilang'] as $key => $label)
                            @php $isActive = ($jenis === $key); @endphp
                            <a href="{{ route('dokumentasi.index', array_filter(['jenis' => $key, 'search' => request('search')])) }}"
                               class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all {{ $isActive ? 'bg-[#0066FF] text-white shadow-sm shadow-blue-500/25' : 'border border-slate-200/80 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                                <span>{{ $label }}</span>
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $counts[$key] }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Form Pencarian --}}
                    <form method="GET" class="w-full sm:w-80">
                        <input type="hidden" name="jenis" value="{{ $jenis }}">
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau lokasi..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- LIST FEED DOKUMENTASI --}}
        <section class="mx-auto max-w-5xl px-6 py-10">
            <div class="space-y-7">
                @forelse ($reports as $r)
                    @php
                        $isInfra = ($r->type === 'infrastruktur');
                        $sebelum = $foto(is_array($r->photos) && count($r->photos) ? $r->photos[0] : $r->photo);
                        $sesudah = $foto($r->photo_after);
                        $fotoBarang = $foto($r->photo);
                    @endphp

                    <article class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm transition hover:shadow-md">
                        
                        {{-- Judul dan Tag Layanan --}}
                        <div class="flex flex-wrap items-center justify-between gap-2.5">
                            <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-[11px] font-bold {{ $isInfra ? 'border-blue-200 bg-blue-50 text-[#0066FF]' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $isInfra ? 'bg-[#0066FF]' : 'bg-amber-500' }}"></span>
                                {{ $isInfra ? 'Infrastruktur Publik' : 'Barang Temuan Warga' }}
                            </span>
                            
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Selesai Ditangani
                            </span>
                        </div>

                        <h2 class="mt-3 text-lg font-bold text-slate-900 sm:text-xl">
                            {{ $isInfra ? $r->title : 'Penyerahan Kembali: ' . $r->title }}
                        </h2>

                        {{-- AREA FOTO (SEBELUM / SESUDAH) --}}
                        <div class="mt-4">
                            @if ($isInfra)
                                @if ($sebelum && $sesudah)
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        {{-- Foto Sebelum --}}
                                        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                                            <img src="{{ $sebelum }}" alt="Kondisi sebelum pengerjaan" loading="lazy" class="h-64 w-full object-cover transition duration-300 group-hover:scale-102">
                                            <div class="absolute bottom-3 left-3">
                                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900/75 px-3 py-1.5 text-xs font-bold text-white shadow-sm backdrop-blur-md">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>
                                                    Sebelum Pengerjaan
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Foto Sesudah --}}
                                        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                                            <img src="{{ $sesudah }}" alt="Kondisi setelah pengerjaan" loading="lazy" class="h-64 w-full object-cover transition duration-300 group-hover:scale-102">
                                            <div class="absolute bottom-3 left-3">
                                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900/75 px-3 py-1.5 text-xs font-bold text-white shadow-sm backdrop-blur-md">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                    Setelah Rampung
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($sesudah || $sebelum)
                                    <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                                        <img src="{{ $sesudah ?? $sebelum }}" alt="{{ $r->title }}" loading="lazy" class="h-72 w-full object-cover">
                                        <div class="absolute bottom-3 left-3">
                                            <span class="rounded-lg bg-slate-900/75 px-3 py-1.5 text-xs font-bold text-white shadow-sm backdrop-blur-md">
                                                {{ $sesudah ? 'Hasil Akhir' : 'Foto Kejadian Awal' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @elseif ($tampilFotoBarang && $fotoBarang)
                                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                                    <img src="{{ $fotoBarang }}" alt="{{ $r->title }}" loading="lazy" class="max-h-80 w-full object-cover">
                                </div>
                            @else
                                {{-- Placeholder Elegan (Tanpa Emotikon) --}}
                                <div class="flex h-44 items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-gradient-to-br from-blue-50/70 to-slate-50 p-6 text-center">
                                    <div>
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-[#0066FF] shadow-xs">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="mt-2.5 text-xs font-bold text-slate-800">Barang Berhasil Dikembalikan</p>
                                        <p class="text-[11px] text-slate-500">Telah diverifikasi dan diserahterimakan langsung ke pemilik sah.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Catatan Penyelesaian --}}
                        @if ($isInfra && $r->completion_notes)
                            <div class="mt-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ringkasan Pengerjaan Dinas</p>
                                <p class="mt-1 text-xs leading-relaxed text-slate-700">
                                    {{ \Illuminate\Support\Str::limit($r->completion_notes, 220) }}
                                </p>
                            </div>
                        @endif

                        {{-- FOOTER / METADATA --}}
                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs text-slate-500">
                            <div class="flex flex-wrap items-center gap-3">
                                @if ($r->category)
                                    <span class="font-semibold text-slate-700">{{ $r->category->name }}</span>
                                    <span>·</span>
                                @endif
                                
                                @if ($r->address)
                                    <div class="flex items-center gap-1 text-slate-500">
                                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate max-w-xs">{{ $r->address }}</span>
                                    </div>
                                    <span>·</span>
                                @endif

                                <div class="flex items-center gap-1 text-slate-400">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Selesai pada {{ $r->updated_at->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>
                        </div>

                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-14 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-700">Belum Ada Dokumentasi Publik</p>
                        <p class="mt-0.5 text-xs text-slate-400">Dokumentasi hasil pengerjaan fisik atau serah terima barang akan tampil di sini setelah diverifikasi.</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            <div class="mt-8">
                {{ $reports->links() }}
            </div>
        </section>
    </main>
</x-public-layout>