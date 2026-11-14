<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'LaporKita' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ modal: '{{ old('_modal', request('modal')) }}' }"
    class="bg-white text-slate-700 antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    {{-- NAVBAR --}}
    <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6">
            <div class="flex items-center gap-10">
                <a href="{{ route('beranda') }}" class="text-2xl font-extrabold text-blue-700">
                    LAPOR<span class="text-slate-900">KITA</span>
                </a>
                <nav class="hidden items-center gap-8 text-sm font-semibold md:flex">
                    <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'text-slate-900' : 'text-slate-400 hover:text-slate-900' }}">
                        Beranda
                    </a>

                    <a href="{{ route('peta.layanan') }}" class="{{ request()->routeIs('peta.layanan') ? 'text-slate-900' : 'text-slate-400 hover:text-slate-900' }}">
                        Peta Layanan
                    </a>

                    {{-- MENU LAPOR INFRASTRUKTUR (MENGARAH KE FOTO 1 UNTUK SEMUA ORANG) --}}
                    <a href="{{ route('laporan.infrastruktur.index') }}"
                        class="{{ request()->routeIs('laporan.infrastruktur*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                        Lapor Infrastruktur
                    </a>

                    <a href="{{ route('laporan.barang.index') }}"
                        class="{{ request()->routeIs('laporan.barang*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                        Lapor Barang Hilang
                    </a>

                    <a href="#" class="text-slate-400 hover:text-slate-900">
                        Dokumentasi Penanganan
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-3 text-sm font-semibold">
                @auth
                {{-- ============================================================= --}}
                {{-- TAMPILAN SAAT SUDAH LOGIN (SESUAI DESAIN GAMBAR ANDA)        --}}
                {{-- ============================================================= --}}
                <div class="flex items-center gap-4 sm:gap-5">

                    {{-- 1. ICON LONCENG NOTIFIKASI --}}
                    <x-notification-bell />

                    {{-- 2. FOTO PROFIL BUNDAR + ARROW DOWN --}}
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button type="button"
                            @click="profileOpen = !profileOpen"
                            class="group flex items-center gap-2 rounded-full p-0.5 transition-transform hover:scale-105 focus:outline-none">

                            {{-- Foto Profil Bundar (Otomatis generate inisial nama jika belum ada foto) --}}
                            <img src="{{ auth()->user()->avatar
        ? asset('storage/' . auth()->user()->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0c203b&color=ffffff&bold=true' }}"
                                alt="{{ auth()->user()->name }}"
                                class="h-10 w-10 rounded-full border border-slate-200 object-cover shadow-sm" />

                            {{-- Icon Panah Bawah (Chevron Down) sesuai gambar --}}
                            <svg class="h-4 w-4 stroke-[2.5] text-[#0c203b] transition-transform duration-200"
                                :class="profileOpen ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- DROPDOWN MENU USER (AKUN, HISTORI & LOGOUT) --}}
                        <div x-show="profileOpen"
                            @click.outside="profileOpen = false"
                            x-cloak
                            x-transition
                            class="absolute right-0 z-50 mt-3 w-56 rounded-2xl border border-slate-100 bg-white p-2 shadow-2xl">

                            {{-- NAMA & EMAIL USER --}}
                            <div class="flex items-center gap-3 border-b border-slate-100 px-3.5 py-3">
                                <img src="{{ auth()->user()->avatar
                ? asset('storage/' . auth()->user()->avatar)
                : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0c203b&color=ffffff&bold=true' }}"
                                    class="h-9 w-9 shrink-0 rounded-full object-cover" alt="">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-[#0c203b]">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <div class="space-y-0.5 py-1.5">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-50 hover:text-blue-600">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Profil Saya</span>
                                </a>

                                <a href="#" class="flex items-center gap-2.5 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-50 hover:text-blue-600">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Histori Laporan</span>
                                </a>
                            </div>

                            {{-- TOMBOL KELUAR (LOGOUT) --}}
                            <div class="border-t border-slate-100 pt-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 rounded-xl px-3.5 py-2 text-left text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50">
                                        <svg class="h-4 w-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Keluar (Logout)</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
                @else
                {{-- TAMPILAN JIKA BELUM LOGIN (TOMBOL MASUK & DAFTAR) --}}
                <div class="flex items-center gap-3">
                    <button type="button" @click="modal = 'login'" class="px-4 py-2 font-semibold text-slate-700 hover:text-blue-600 transition-colors">
                        Masuk
                    </button>
                    <button type="button" @click="modal = 'register'" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-blue-600/30 transition-all hover:bg-blue-700">
                        Daftar
                    </button>
                </div>
                @endauth
            </div>
    </header>

    <main>{{ $slot }}</main>

    {{-- ================================================================= --}}
    {{-- CEK APAKAH SEDANG DI HALAMAN FORM ATAU CEK PROGRES                --}}
    {{-- ================================================================= --}}
    @php
    $halamanTanpaFooter = request()->routeIs(
    // 1. Semua Halaman Form Input
    'laporan.infrastruktur', // Form Buat Infrastruktur
    'laporan.create',
    'laporan.barang.pilih', // Pilihan Jenis Barang
    'laporan.barang.kehilangan', // Form Kehilangan
    'laporan.barang.penemuan', // Form Penemuan
    // 2. Semua Halaman Cek Progres / Tracking
    'laporan.infrastruktur.saya', // Laporan Saya
    'laporan.saya',
    'laporan.infrastruktur.detail', // Detail Progres & Slider Foto
    'laporan.show'
    );
    @endphp

    @if (!$halamanTanpaFooter)
    <footer class="mt-24 border-t border-slate-200/70 bg-white text-slate-700">
        <div class="mx-auto max-w-7xl px-6 py-14 lg:py-16">

            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-12 lg:gap-8">

                {{-- KOLOM 1: BRANDING --}}
                <div class="lg:col-span-4">
                    <a href="{{ route('beranda') }}" class="text-2xl font-extrabold tracking-tight text-[#0c203b]">
                        Lapor<span class="text-blue-600">Kita</span>
                    </a>
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-500">
                        Sistem pelayanan pengaduan dan laporan masyarakat berbasis digital terpadu untuk percepatan penanganan infrastruktur dan pelayanan publik.
                    </p>
                    <div class="mt-5 inline-flex items-center gap-2 rounded-full border border-blue-100/80 bg-blue-50/70 px-3.5 py-1.5 text-xs font-semibold text-blue-700 shadow-sm">
                        <svg class="h-4 w-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Layanan Publik Terverifikasi Resmi Pemerintah</span>
                    </div>
                </div>

                {{-- KOLOM 2: KATEGORI ADUAN (DIARAHKAN KE MODUL MASING-MASING) --}}
                <div class="lg:col-span-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#0c203b]">Kategori Aduan</p>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                        <li><a href="{{ route('laporan.infrastruktur.index') }}" class="transition-colors hover:text-blue-600">Infrastruktur Jalan & Jembatan</a></li>
                        <li><a href="{{ route('laporan.infrastruktur.index') }}" class="transition-colors hover:text-blue-600">Penerangan Jalan Umum</a></li>
                        <li><a href="{{ route('laporan.barang.index') }}" class="transition-colors hover:text-blue-600">Barang Hilang & Ditemukan</a></li>
                        <li><a href="{{ route('laporan.barang.index', ['item_kind' => 'kehilangan']) }}" class="transition-colors hover:text-blue-600">Pencarian Barang Hilang</a></li>
                        <li><a href="{{ route('laporan.barang.index', ['item_kind' => 'penemuan']) }}" class="transition-colors hover:text-blue-600">Barang Temuan Warga</a></li>
                    </ul>
                </div>

                {{-- KOLOM 3: NAVIGASI CEPAT --}}
                <div class="lg:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#0c203b]">Navigasi Cepat</p>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-600">
                        <li><a href="{{ route('peta.layanan') }}" class="transition-colors hover:text-blue-600">Peta Interaktif GIS</a></li>
                        <li><a href="{{ route('beranda') }}#cara-kerja" class="transition-colors hover:text-blue-600">Alur Verifikasi Laporan</a></li>
                        <li><a href="{{ route('laporan.infrastruktur.index') }}" class="transition-colors hover:text-blue-600">Katalog Laporan Publik</a></li>
                    </ul>
                </div>

                {{-- KOLOM 4: KONTAK & DARURAT --}}
                <div class="lg:col-span-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#0c203b]">Kontak & Darurat</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <a href="tel:112" class="font-bold text-slate-800 transition-colors hover:text-blue-600">112 (Bebas Pulsa 24 Jam)</a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <a href="mailto:pengaduan@laporkita.go.id" class="transition-colors hover:text-blue-600">pengaduan@laporkita.go.id</a>
                        </li>
                        <li class="flex items-center gap-2.5 text-xs text-slate-500">
                            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Layanan Pemantauan Siaga Aktif</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-100 pt-6 text-xs text-slate-500 sm:flex-row">
                <p class="text-center sm:text-left">
                    &copy; {{ date('Y') }} LaporKita - Portal Pelayanan Publik & Pengaduan Terpadu.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-6">
                    <a href="#" class="transition-colors hover:text-blue-600">Kebijakan Privasi</a>
                    <a href="#" class="transition-colors hover:text-blue-600">Syarat & Ketentuan</a>
                </div>
            </div>

        </div>
    </footer>
    @endif
    <x-auth-modals />
    <script>
        lucide.createIcons();
    </script>
</body>

</html>