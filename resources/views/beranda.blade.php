<x-public-layout title="LaporKita - Beranda">

    {{-- ================================================================= --}}
    {{-- HERO SECTION                                                      --}}
    {{-- ================================================================= --}}
    <section class="relative overflow-hidden bg-white pt-10 pb-12 lg:pt-14 lg:pb-14">

        {{-- BACKGROUND FOTO DENGAN GRADIENT OVERLAY --}}
        <div class="pointer-events-none absolute inset-0 z-0">
            <img src="{{ asset('images/hero-bg.png') }}"
                onerror="this.src='https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?w=1600&auto=format&fit=crop&q=80'"
                alt="Pekerjaan Infrastruktur LaporKita"
                class="h-full w-full object-cover object-right md:object-center opacity-90" />

        </div>

        <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-12">

            {{-- KONTEN KIRI: JUDUL & CTA --}}
            <div class="lg:col-span-7">
                {{-- BADGE PILL RESMI --}}
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100/80 bg-blue-50/90 px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm backdrop-blur-sm">
                    <span>Portal Resmi Pengaduan & Layanan Masyarakat Digital</span>
                    <svg class="h-4 w-4 text-blue-600 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                </div>

                {{-- HEADING BESAR --}}
                <h1 class="mt-6 text-4xl font-extrabold leading-[1.15] tracking-tight text-[#0c203b] sm:text-5xl lg:text-[54px]">
                    Satu Laporan, <br class="hidden sm:inline" />
                    <span class="text-blue-600">Langkah Nyata</span> untuk Kota yang Lebih Baik.
                </h1>

                {{-- TOMBOL AKSI HERO --}}
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    @auth
                        <a href="{{ route('laporan.infrastruktur') }}"
                            class="inline-flex items-center gap-2.5 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700">
                            Buat Laporan Sekarang
                        </a>
                    @else
                        <button type="button" @click="modal = 'login'"
                            class="inline-flex items-center gap-2.5 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700">
                            Buat Laporan Sekarang
                        </button>
                    @endauth
                    
                    <a href="{{ route('peta.layanan') }}" class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white/90 px-6 py-3.5 text-sm font-bold text-slate-800 shadow-sm backdrop-blur-sm transition-all hover:bg-slate-50">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                            <line x1="8" y1="2" x2="8" y2="18"></line>
                            <line x1="16" y1="6" x2="16" y2="22"></line>
                        </svg>
                        Jelajahi Peta Layanan
                    </a>
                </div>
            </div>

            {{-- KONTEN KANAN: FLOATING LIVE MAP DENGAN DATA NYATA LAPORAN --}}
            <div class="relative lg:col-span-5">
                <div class="relative mx-auto h-[380px] w-full max-w-[460px] overflow-hidden rounded-3xl border border-white/80 bg-[#eaf2fa] p-4 shadow-2xl shadow-slate-900/15 backdrop-blur-md sm:h-[400px]">

                    {{-- GRID SIMULASI PETA DENGAN SATELIT IFRAME SESUAI TITIK TERBARU --}}
                    @if ($laporanPetaHero && $laporanPetaHero->latitude && $laporanPetaHero->longitude)
                        <iframe class="absolute inset-0 h-full w-full pointer-events-none opacity-60" 
                                src="https://maps.google.com/maps?q={{ $laporanPetaHero->latitude }},{{ $laporanPetaHero->longitude }}&t=m&z=14&output=embed">
                        </iframe>
                    @else
                        <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.7) 2px, transparent 2px), linear-gradient(to bottom, rgba(255,255,255,0.7) 2px, transparent 2px); background-size: 50px 50px;">
                            <div class="absolute top-10 left-6 h-24 w-24 rounded-3xl border border-emerald-200/50 bg-[#d5edd9]/80"></div>
                        </div>
                    @endif

                    {{-- PILL LIVE STATUS --}}
                    <div class="absolute top-4 left-4 z-20 inline-flex items-center gap-2 rounded-full border border-slate-100 bg-white/90 px-3.5 py-1.5 text-[10px] font-bold text-slate-800 shadow-sm backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                        LIVE • PEMBAHARUAN LOKASI SAAT INI
                    </div>

                    {{-- PIN 1: MERAH (Laporan Masuk) --}}
                    <div class="absolute top-16 left-16 z-20 transition-transform hover:scale-110">
                        <div class="flex h-11 w-11 -rotate-6 items-center justify-center rounded-2xl bg-red-600 text-white shadow-lg shadow-red-600/40">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                    </div>

                    {{-- PIN 2: ORANYE (Infrastruktur Aktif) --}}
                    <div class="absolute top-28 left-40 z-20 transition-transform hover:scale-110">
                        <div class="flex h-11 w-11 rotate-6 items-center justify-center rounded-2xl bg-[#d97706] text-white shadow-lg shadow-amber-600/40">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                        </div>
                    </div>

                    {{-- PIN 3: BIRU (Layanan Posko) --}}
                    <div class="absolute top-16 right-10 z-20 transition-transform hover:scale-110">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/40">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11m0 0c0 2.473.345 4.866.99 7.132M12 11V3"></path></svg>
                        </div>
                    </div>

                    {{-- KARTU DETAIL MENGAMBANG DI BAWAH PETA (MENGAMBIL DATA LAPORAN TERAKHIR) --}}
                    <div class="absolute bottom-4 right-4 left-4 z-30 flex flex-col gap-2 rounded-2xl border border-slate-100 bg-white/95 p-3.5 shadow-xl backdrop-blur-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-[10px] font-bold tracking-wide text-blue-600 uppercase">
                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                </svg>
                                {{ $laporanPetaHero ? ($laporanPetaHero->type == 'infrastruktur' ? 'INFRASTRUKTUR' : 'BARANG') : 'SISTEM SIAGA' }} • {{ $laporanPetaHero->ticket_code ?? '#LK-LIVE' }}
                            </div>
                            <span class="rounded-md border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600">
                                {{ $laporanPetaHero ? $laporanPetaHero->status_label : 'Sistem Aktif' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-100 pt-2">
                            <div class="max-w-[240px]">
                                <h4 class="truncate text-xs font-bold text-[#0c203b]">
                                    {{ $laporanPetaHero->title ?? 'Pemantauan Terpadu Kota' }}
                                </h4>
                                <p class="mt-0.5 flex items-center gap-1 text-[10px] text-slate-400 truncate">
                                    <svg class="h-3 w-3 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    {{ $laporanPetaHero->address ?? 'Pusat Kendali Pengaduan' }}
                                </p>
                            </div>
                            <a href="{{ route('peta.layanan') }}" class="text-[10px] font-bold text-blue-600 hover:underline">
                                Buka Peta &rarr;
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- STATISTIK TRANSPARANSI KINERJA PUBLIK (DINAMIS DARI DATABASE)      --}}
    {{-- ================================================================= --}}
    <section class="bg-gradient-to-b from-[#f4f7fb] via-[#f7faff] to-white py-20">
        <div class="mx-auto max-w-7xl px-6">

            <div class="text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100/60 bg-blue-50 px-3.5 py-1.5 text-xs font-bold tracking-wider text-blue-700 uppercase shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    DATA PEMBARUAN REAL-TIME
                </div>
                <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-[#0c203b] sm:text-4xl">
                    Transparansi Kinerja Layanan Publik
                </h2>
                <p class="mx-auto mt-2.5 max-w-2xl text-sm text-slate-500 sm:text-base">
                    Akuntabilitas terbuka bagi seluruh warga dengan sinkronisasi basis data instansi secara berkala.
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @php
                $cards = [
                    ['Total Laporan Warga', number_format($stats['total'], 0, ',', '.'), '+' . $stats['minggu_ini'] . ' laporan minggu ini'],
                    ['Barang Kembali ke Pemilik', number_format($stats['barang_kembali'], 0, ',', '.'), 'Barang terverifikasi selesai'],
                    ['Kerusakan Fasilitas Ditangani', number_format($stats['infra_selesai'], 0, ',', '.'), 'Proses & rampung perbaikan'],
                    ['Tingkat Penuntasan Dinas', str_replace('.', ',', (string)$stats['persen']) . '%', 'Persentase keberhasilan tindak lanjut'],
                ];
                @endphp

                @foreach ($cards as [$label, $angka, $ket])
                <div class="flex flex-col justify-between rounded-[24px] border border-slate-100/80 bg-white p-6 shadow-xl shadow-slate-200/50 transition-all hover:-translate-y-1 hover:shadow-2xl">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">{{ $label }}</span>
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                @if ($loop->index === 0)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                @elseif ($loop->index === 1)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                @elseif ($loop->index === 2)
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                @endif
                            </div>
                        </div>

                        <p class="mt-4 text-3xl font-black tracking-tight sm:text-4xl {{ $loop->last ? 'text-blue-600' : 'text-[#0c203b]' }}">
                            {{ $angka }}
                        </p>

                        <p class="mt-2 flex items-center gap-1.5 text-xs font-medium text-slate-500">
                            <span>{{ $ket }}</span>
                        </p>
                    </div>

                    <div class="mt-5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ min(100, max(15, (int)$stats['total'] * 8)) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- CARA KERJA SISTEM LAPORKITA                                       --}}
    {{-- ================================================================= --}}
    @php
    $langkah = [
        [
            'no' => '01',
            'judul' => 'Buat Laporan',
            'isi' => 'Ambil foto bukti, tentukan titik lokasi akurat pada peta, dan lengkapi deskripsi aduan atau kerusakan.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />',
        ],
        [
            'no' => '02',
            'judul' => 'Verifikasi Cepat',
            'isi' => 'Tim administrator memeriksa keabsahan data, menyaring duplikasi, dan validasi laporan dalam tempo singkat.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />',
        ],
        [
            'no' => '03',
            'judul' => 'Tindak Lanjut',
            'isi' => 'Laporan barang tayang di peta publik; aduan infrastruktur otomatis didistribusikan ke dinas operasional terkait.',
            'icon' => '<circle cx="6" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><circle cx="18" cy="9" r="3"></circle><path d="M6 9v6"></path><path d="M18 9a9 9 0 0 1-9 9"></path>',
        ],
        [
            'no' => '04',
            'judul' => 'Pantau Hasil',
            'isi' => 'Pantau progres penanganan di peta publik hingga status tuntas lengkap dengan foto Before & After.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
        ],
    ];
    @endphp

    <section id="cara-kerja" class="relative overflow-hidden bg-gradient-to-b from-[#f3f7fb] via-[#f7faff] to-white py-20 select-none"
        x-data="{
             active: 2,
             total: {{ count($langkah) }},
             getCardStyle(index) {
                 const diff = index - this.active;
                 const isMobile = window.innerWidth < 640;
                 const spacing = isMobile ? 65 : 110;
                 const translateX = diff * spacing;
                 const scale = 1 - Math.abs(diff) * 0.08;
                 const zIndex = 30 - Math.abs(diff) * 5;
                 const opacity = Math.abs(diff) > 2 ? 0 : (1 - Math.abs(diff) * 0.12);

                 return {
                     transform: `translate3d(calc(-50% + ${translateX}px), -50%, 0) scale(${Math.max(scale, 0.72)})`,
                     zIndex: zIndex,
                     opacity: opacity,
                     pointerEvents: Math.abs(diff) > 2 ? 'none' : 'auto'
                 };
             }
         }">

        <div class="mx-auto max-w-3xl px-6 text-center">
            <h2 class="text-3xl font-extrabold tracking-tight text-[#0c203b] sm:text-4xl">
                Cara Kerja Sistem LaporKita
            </h2>
            <p class="mt-3 text-sm text-slate-500 sm:text-base">
                Dari pengunggahan bukti hingga penanganan tuntas hanya dalam 4 tahapan transparan.
            </p>
        </div>

        <div class="relative mx-auto mt-8 h-[400px] w-full max-w-5xl sm:mt-12 sm:h-[440px]">
            @foreach ($langkah as $index => $item)
            <div class="absolute top-1/2 left-1/2 flex h-[330px] w-[285px] flex-col justify-between rounded-[28px] bg-white p-7 transition-all duration-500 ease-out sm:h-[360px] sm:w-[340px] sm:p-8 cursor-pointer"
                :style="getCardStyle({{ $index }})"
                :class="active === {{ $index }} ? 'shadow-[0_22px_50px_-10px_rgba(37,99,235,0.35)] ring-1 ring-blue-500/10' : 'shadow-xl shadow-slate-200/60 border border-slate-100 hover:shadow-2xl'"
                @click="active = {{ $index }}">

                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#0c203b] text-lg font-bold text-white shadow-md shadow-slate-900/10">
                            {{ $item['no'] }}
                        </div>
                        <div class="p-1 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                {!! $item['icon'] !!}
                            </svg>
                        </div>
                    </div>

                    <h3 class="mt-6 text-xl font-bold text-[#0c203b] sm:mt-7 sm:text-2xl">
                        {{ (int) $item['no'] }}. {{ $item['judul'] }}
                    </h3>

                    <p class="mt-3 text-xs leading-relaxed text-slate-500 sm:text-sm">
                        {{ $item['isi'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 flex items-center justify-center gap-2.5">
            @foreach ($langkah as $index => $item)
            <button type="button" @click="active = {{ $index }}"
                class="transition-all duration-300 focus:outline-none"
                :class="active === {{ $index }} ? 'h-2.5 w-2.5 bg-[#0c203b] rounded-full scale-110' : 'h-2 w-2 bg-slate-300 hover:bg-slate-400 rounded-full'">
            </button>
            @endforeach
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- LAPORAN TERBARU (MAKSIMAL 5 DARI DATABASE DENGAN SCROLL)          --}}
    {{-- ================================================================= --}}
    <section class="mx-auto max-w-7xl px-6 py-12">

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3.5">
            <div class="flex items-center gap-2.5">
                <span class="inline-block h-7 w-1.5 rounded-full bg-blue-600"></span>
                <h2 class="text-2xl font-extrabold tracking-tight text-[#0c203b] sm:text-3xl">
                    Laporan Terbaru
                </h2>
                <div class="inline-flex items-center gap-1.5 rounded-full border border-blue-100/80 bg-blue-50/90 px-3 py-1 text-[11px] font-bold text-blue-700 shadow-sm ml-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    DATA PEMBARUAN REAL-TIME
                </div>
            </div>
            <a href="{{ route('laporan.infrastruktur.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                Lihat Semua Laporan &rarr;
            </a>
        </div>

        <div class="relative">
            <div class="custom-feed-scroll max-h-[520px] space-y-4 overflow-y-auto pb-14 pr-2 sm:pr-3">
                @forelse ($laporanTerbaru as $laporan)
                <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-6 shadow-md shadow-slate-200/40 transition-all hover:shadow-lg">

                    {{-- STATUS BADGE & WAKTU --}}
                    <div class="flex items-center justify-between">
                        @if ($laporan->type === 'barang_hilang')
                            @if ($laporan->item_kind === 'penemuan')
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-blue-100/70 bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                                Ditemukan
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-rose-100/70 bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-600">
                                Dicari
                            </span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-amber-100/70 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">
                                {{ $laporan->status === 'in_progress' ? 'Sedang Dikerjakan' : ($laporan->status === 'resolved' ? 'Selesai' : 'Perlu Diperbaiki') }}
                            </span>
                        @endif

                        <span class="text-xs font-medium text-slate-400">
                            {{ $laporan->created_at->diffForHumans() }}
                        </span>
                    </div>

                    {{-- JUDUL & DESKRIPSI --}}
                    <div class="mt-3">
                        <h3 class="text-lg font-bold text-[#0c203b] sm:text-xl">
                            {{ $laporan->title }}
                        </h3>
                        <p class="mt-1.5 text-xs leading-relaxed text-slate-500 sm:text-sm">
                            {{ Str::limit($laporan->description, 140) }}
                        </p>
                    </div>

                    {{-- BARIS BAWAH: LOKASI & TOMBOL AKSI --}}
                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-50 pt-3">
                        <span class="font-mono text-[11px] tracking-wider text-slate-500 sm:text-xs">
                            📍 {{ $laporan->address }}
                        </span>
                        <div class="flex items-center gap-2">
                            @if ($laporan->photo)
                                @auth
                                <a href="{{ asset('storage/' . $laporan->photo) }}" target="_blank" class="inline-flex items-center rounded-xl bg-slate-700/80 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800">
                                    Lihat Foto
                                </a>
                                @else
                                <button type="button" @click="modal = 'login'" class="inline-flex items-center rounded-xl bg-slate-700/80 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800">
                                    Lihat Foto
                                </button>
                                @endauth
                            @endif

                            @if ($laporan->type === 'infrastruktur')
                            <a href="{{ route('laporan.infrastruktur.detail', $laporan->id) }}" class="inline-flex items-center rounded-xl bg-[#0c203b] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800">
                                Lihat Detail
                            </a>
                            @else
                            <a href="{{ route('laporan.barang.index') }}" class="inline-flex items-center rounded-xl bg-[#0c203b] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800">
                                Lihat di Barang Hilang
                            </a>
                            @endif
                        </div>
                    </div>

                </div>
                @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center text-slate-500">
                    Belum ada laporan terbaru yang masuk ke sistem.
                </div>
                @endforelse
            </div>

            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-[#f0f4f9] via-[#f0f4f9]/80 to-transparent"></div>
        </div>
    </section>

    {{-- ================================================================= --}}
    {{-- CTA BAWAH: AJAKAN MELAPOR & KONTAK DARURAT                        --}}
    {{-- ================================================================= --}}
    <section class="mx-auto max-w-7xl px-6 py-12">
        <div class="relative overflow-hidden rounded-[32px] bg-gradient-to-r from-[#06182e] via-[#082242] to-[#0c3260] p-8 shadow-2xl shadow-slate-900/20 sm:p-12 lg:p-14">
            <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-blue-500/15 blur-3xl"></div>

            <div class="relative z-10 grid items-center gap-10 lg:grid-cols-12 lg:gap-14">
                <div class="lg:col-span-7">
                    <h2 class="text-3xl font-extrabold leading-[1.2] tracking-tight text-white sm:text-4xl lg:text-[40px]">
                        Punya Temuan Barang atau Melihat Fasilitas Publik Rusak?
                    </h2>
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-300 sm:text-base">
                        Jangan tunda. Kontribusi kecil Anda menjadi solusi penting bagi tetangga dan keindahan tata ruang kota kita. Gratis dan tanpa birokrasi berbelit.
                    </p>

                    <div class="mt-8">
                        @auth
                        <a href="{{ route('laporan.infrastruktur') }}" class="inline-flex items-center gap-2.5 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <span>Laporkan Sekarang</span>
                        </a>
                        @else
                        <button type="button" @click="modal = 'login'" class="inline-flex items-center gap-2.5 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <span>Laporkan Sekarang</span>
                        </button>
                        @endauth
                    </div>
                </div>

                {{-- BANTUAN MENDESAK --}}
                <div class="lg:col-span-5">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-6 backdrop-blur-md sm:p-7">
                        <h3 class="text-lg font-bold text-white">Butuh Bantuan Mendesak?</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-300/90 sm:text-[13px]">
                            Tim layanan masyarakat kami dapat dihubungi melalui pusat panggilan resmi terpadu:
                        </p>

                        <a href="tel:112" class="group mt-4 flex items-center gap-3.5 rounded-xl border border-white/10 bg-white/[0.06] p-3.5 transition-all hover:bg-white/[0.12]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white group-hover:bg-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">HOTLINE DARURAT NASIONAL</span>
                                <span class="text-sm font-bold text-white sm:text-base">112 <span class="text-xs font-normal text-slate-300">(Bebas Pulsa 24 Jam)</span></span>
                            </div>
                        </a>

                        <a href="https://wa.me/6282267177099" target="_blank" rel="noopener noreferrer" class="group mt-3 flex items-center gap-3.5 rounded-xl border border-white/10 bg-white/[0.06] p-3.5 transition-all hover:bg-white/[0.12]">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white group-hover:bg-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">WHATSAPP ADUAN CEPAT</span>
                                <span class="text-sm font-bold text-white sm:text-base">+62 822 - 6717 - 7099</span>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-public-layout>