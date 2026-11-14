@props(['title' => 'Admin - LaporKita'])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4f7fb] text-slate-800 antialiased">
    <div class="flex min-h-screen" x-data="{ menu: false }">

        {{-- SIDEBAR --}}
        <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-[#0c203b] text-white transition-transform lg:translate-x-0"
            :class="menu ? 'translate-x-0' : ''">
            <div class="border-b border-white/10 px-6 py-5">
                <span class="text-xl font-extrabold tracking-tight">LAPOR<span class="text-blue-400">KITA</span></span>
                <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-widest text-blue-200">Panel Admin</p>
            </div>

            <nav class="space-y-1 p-4 text-sm font-semibold">
                @php
                    $link = fn ($active) => $active ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-white/10';
                @endphp

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 {{ $link(request()->routeIs('admin.dashboard')) }}">
                    📊 Dashboard
                </a>

                {{-- Menu berikut aktif setelah modulnya dibuat --}}
<a href="{{ route('admin.infrastruktur.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 {{ $link(request()->routeIs('admin.infrastruktur.*')) }}">
    🏗️ Laporan Infrastruktur
</a>                <span class="flex cursor-not-allowed items-center gap-3 rounded-xl px-4 py-2.5 text-blue-200/50">🔍 Barang Hilang <em class="ml-auto text-[9px] not-italic">segera</em></span>
            </nav>

            <div class="absolute inset-x-0 bottom-0 space-y-2 border-t border-white/10 p-4 text-xs">
                <p class="truncate px-2 font-semibold text-blue-100">{{ auth()->user()->name }}</p>
                <a href="{{ route('beranda') }}" class="block rounded-lg px-2 py-1.5 text-blue-200 hover:bg-white/10">↗ Lihat Situs</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-2 py-1.5 text-left font-semibold text-rose-300 hover:bg-white/10">Keluar</button>
                </form>
            </div>
        </aside>

        {{-- KONTEN --}}
        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            <header class="flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 lg:hidden">
                <button type="button" @click="menu = !menu" class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-bold">☰</button>
                <span class="text-sm font-extrabold text-[#0c203b]">Panel Admin</span>
            </header>

            <main class="flex-1 p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>