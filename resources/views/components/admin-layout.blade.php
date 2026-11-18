@props(['title' => 'Admin - LaporKita'])

<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-[#f8fafc] text-slate-800 antialiased font-sans">
    <div class="min-h-full" x-data="{ menu: false }">

        {{-- BACKDROP MOBILE (Menutup sidebar jika klik di luar area) --}}
        <div x-show="menu" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="menu = false" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden"
             style="display: none;"></div>

        {{-- SIDEBAR --}}
        <aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-[#0b1b33] text-white transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="menu ? 'translate-x-0 shadow-2xl' : '-translate-x-full'">
            
            {{-- BRAND LOGO --}}
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-800/80 px-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#0066FF] text-white shadow-sm font-bold text-base">
                        L
                    </div>
                    <div>
                        <span class="text-base font-extrabold tracking-tight text-white">LAPOR<span class="text-[#0066FF]">KITA</span></span>
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Panel Admin</span>
                    </div>
                </a>
                {{-- Tombol Tutup Sidebar di Layar Mobile --}}
                <button type="button" @click="menu = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- MENU NAVIGASI --}}
            <nav class="flex-1 space-y-1 overflow-y-auto px-3.5 py-4 text-xs font-semibold">
                @php
                    $navItem = function($active) {
                        return $active 
                            ? 'bg-[#0066FF] text-white shadow-sm shadow-blue-500/20' 
                            : 'text-slate-300 hover:bg-slate-800/60 hover:text-white';
                    };
                @endphp

                {{-- Group: Utama --}}
                <div class="px-3 pb-1 pt-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Menu Utama
                </div>

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}" 
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all {{ $navItem(request()->routeIs('admin.dashboard')) }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                {{-- Laporan Infrastruktur --}}
                <a href="{{ route('admin.infrastruktur.index') }}" 
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all {{ $navItem(request()->routeIs('admin.infrastruktur.*')) }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Laporan Infrastruktur</span>
                </a>

                {{-- Barang Hilang --}}
                <a href="{{ route('admin.barang.index') }}" 
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all {{ $navItem(request()->routeIs('admin.barang.*')) }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Barang Hilang</span>
                </a>

                {{-- Group: Kelola Data --}}
                <div class="px-3 pb-1 pt-5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Kelola Sistem
                </div>

                {{-- Kategori --}}
                <a href="{{ route('admin.kategori.index') }}" 
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all {{ $navItem(request()->routeIs('admin.kategori.*')) }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>Kategori</span>
                </a>

                {{-- Pengguna --}}
                <a href="{{ route('admin.pengguna.index') }}" 
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all {{ $navItem(request()->routeIs('admin.pengguna.*')) }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Pengguna</span>
                </a>

                {{-- Rekap --}}
                <a href="{{ route('admin.rekap.index') }}" 
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all {{ $navItem(request()->routeIs('admin.rekap.*')) }}">
                    <svg class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Rekap & Laporan</span>
                </a>
            </nav>

            {{-- PROFIL USER & PENGATURAN BAWAH --}}
            <div class="shrink-0 border-t border-slate-800/80 bg-[#081529]/80 p-3.5">
                <div class="mb-3 flex items-center gap-2.5 px-1.5 py-1">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-[#0066FF] to-blue-400 font-bold text-white shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-[10px] text-slate-400">Administrator</p>
                    </div>
                </div>

                <div class="space-y-0.5 text-xs font-medium">
                    <a href="{{ route('admin.profil.edit') }}" 
                       class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-slate-300 transition-colors hover:bg-slate-800 hover:text-white {{ request()->routeIs('admin.profil.*') ? 'bg-slate-800 text-white font-semibold' : '' }}">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Profil Saya</span>
                    </a>

                    <a href="{{ route('beranda') }}" target="_blank"
                       class="flex items-center justify-between rounded-lg px-2.5 py-2 text-slate-300 transition-colors hover:bg-slate-800 hover:text-white">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Lihat Situs</span>
                        </div>
                        <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="pt-1">
                        @csrf
                        <button type="submit" 
                                class="flex w-full items-center gap-2 rounded-lg px-2.5 py-2 text-left font-semibold text-rose-400 transition-colors hover:bg-rose-500/10 hover:text-rose-300">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- AREA KONTEN UTAMA --}}
        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            
            {{-- HEADER MOBILE --}}
            <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur-xs lg:hidden">
                <div class="flex items-center gap-3">
                    <button type="button" @click="menu = !menu" 
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-700 transition-colors hover:bg-slate-100 active:scale-95">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-sm font-extrabold tracking-tight text-slate-900">LAPOR<span class="text-[#0066FF]">KITA</span></span>
                </div>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-[#0066FF]">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </header>

            {{-- MAIN SLOT --}}
            <main class="flex-1 p-4 sm:p-7 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>