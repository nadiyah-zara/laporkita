<x-public-layout title="Lapor Barang Hilang & Ditemukan - LaporKita">
    <main class="min-h-screen bg-[#f8fafc]">
        {{-- HERO SECTION --}}
        <section class="relative overflow-hidden bg-white py-12 md:py-16">
            <div class="mx-auto grid max-w-7xl items-center gap-8 px-6 lg:grid-cols-12">
                <div class="z-10 lg:col-span-6">
                    <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-[#0c203b] sm:text-5xl">
                        Satu Laporan,<br>
                        Kembalikan Barang Hilang<br>
                        Ke Pemiliknya.
                    </h1>
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        @auth
                        <a href="{{ route('laporan.barang.pilih') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-500/25 transition-all hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Laporan Sekarang
                        </a>
                        @else
                        <button type="button" @click="modal = 'login'" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-500/25 transition-all hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Laporan Sekarang
                        </button>
                        @endauth
                        <a href="{{ route('peta.layanan') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-700 shadow-sm transition-all hover:bg-slate-50">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Jelajahi Peta Layanan
                        </a>
                    </div>
                </div>

                {{-- Banner Hero Image (STNK / KTP / Kunci) --}}
                <div class="relative lg:col-span-6">
                    <div class="overflow-hidden rounded-3xl shadow-xl">
                        <img src="{{ asset('images/Mask group.png') }}"
                            onerror="this.src='https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?w=1600&auto=format&fit=crop&q=80'"
                            alt="Pekerjaan Infrastruktur LaporKita"
                            class="h-full w-full object-cover object-right md:object-center opacity-90" />
                    </div>
                </div>
            </div>
        </section>

        {{-- MAIN FEED SECTION --}}
        <section class="mx-auto max-w-7xl px-6 py-8">
            {{-- TOMBOL MENUJU LAPORAN ANDA (HANYA MUNCUL JIKA SUDAH LOGIN) --}}
            @auth
            <div class="mb-6 flex justify-end">
                <a href="{{ route('laporan.barang.saya') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700">
                    Halaman Laporan Anda
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
            @endauth

            {{-- FILTER BAR --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('laporan.barang.index') }}"
                    x-data="{ detecting: false }"
                    class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">

                    {{-- Hidden: koordinat GPS user --}}
                    <input type="hidden" name="user_lat" id="user_lat" value="{{ request('user_lat') }}">
                    <input type="hidden" name="user_lng" id="user_lng" value="{{ request('user_lng') }}">

                    {{-- 1. Search --}}
                    <div class="relative">
                        <svg class="absolute top-3.5 left-3.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari alamat atau judul..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pr-4 pl-10 text-xs font-medium text-slate-700 focus:border-blue-600 focus:bg-white focus:outline-none">
                    </div>

                    {{-- 2. Radius Filter --}}
                    <div class="relative">
                        <svg class="pointer-events-none absolute top-3 left-3.5 h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        {{-- Spinner saat deteksi GPS --}}
                        <svg x-show="detecting" x-cloak class="pointer-events-none absolute top-3 right-7 h-3.5 w-3.5 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <svg x-show="!detecting" class="pointer-events-none absolute top-3.5 right-3 h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <select name="radius"
                            @change="
                        const val = $event.target.value;
                        if (val && navigator.geolocation) {
                            detecting = true;
                            navigator.geolocation.getCurrentPosition(
                                pos => {
                                    document.getElementById('user_lat').value = pos.coords.latitude.toFixed(6);
                                    document.getElementById('user_lng').value = pos.coords.longitude.toFixed(6);
                                    $el.form.submit();
                                },
                                () => { detecting = false; $el.form.submit(); }
                            );
                        } else { $el.form.submit(); }
                    "
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-9 pr-8 text-xs font-semibold text-slate-700 focus:outline-none cursor-pointer">
                            <option value="" {{ !request('radius') ? 'selected' : '' }}>Radius: Semua Jarak</option>
                            <option value="1" {{ request('radius') == '1'  ? 'selected' : '' }}>Radius: 1 KM</option>
                            <option value="2" {{ request('radius') == '2'  ? 'selected' : '' }}>Radius: 2 KM</option>
                            <option value="5" {{ request('radius') == '5'  ? 'selected' : '' }}>Radius: 5 KM</option>
                            <option value="10" {{ request('radius') == '10' ? 'selected' : '' }}>Radius: 10 KM</option>
                            <option value="25" {{ request('radius') == '25' ? 'selected' : '' }}>Radius: 25 KM</option>
                            <option value="50" {{ request('radius') == '50' ? 'selected' : '' }}>Radius: 50 KM</option>
                        </select>
                    </div>

                    {{-- 3. Filter Waktu --}}
                    <div class="relative">
                        <svg class="pointer-events-none absolute top-3 left-3.5 h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <svg class="pointer-events-none absolute top-3.5 right-3 h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <select name="days" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 pl-9 pr-8 text-xs font-semibold text-slate-700 focus:outline-none cursor-pointer">
                            <option value="all" {{ request('days','all') === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                            <option value="1" {{ request('days') == '1'   ? 'selected' : '' }}>Hari Ini</option>
                            <option value="3" {{ request('days') == '3'   ? 'selected' : '' }}>3 Hari Terakhir</option>
                            <option value="7" {{ request('days') == '7'   ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="30" {{ request('days') == '30'  ? 'selected' : '' }}>30 Hari Terakhir</option>
                            <option value="90" {{ request('days') == '90'  ? 'selected' : '' }}>3 Bulan Terakhir</option>
                            <option value="365" {{ request('days') == '365' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                        </select>
                    </div>

                    {{-- 4. Kategori --}}
                    <div class="relative">
                        <svg class="pointer-events-none absolute top-3.5 right-3 h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        <select name="category_id" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/60 py-2.5 px-4 text-xs font-semibold text-slate-700 focus:outline-none cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- LIST BARANG FEED --}}
            <div class="mt-6 space-y-4">
                @forelse ($reports as $report)
                <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        @if ($report->status == 'resolved' || $report->status == 'selesai')
                        {{-- JIKA SUDAH SELESAI --}}
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                            Selesai (Sudah Dikembalikan)
                        </span>
                        @elseif ($report->item_kind == 'penemuan')
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                            Ditemukan
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600">
                            Dicari
                        </span>
                        @endif
                        <span class="text-xs font-medium text-slate-400">{{ $report->created_at->diffForHumans() }}</span>
                    </div>

                    <h3 class="mt-3 text-xl font-bold tracking-tight text-[#0c203b]">{{ $report->title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $report->description }}</p>

                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-50 pt-4">
                        <div class="flex items-center gap-2 font-mono text-xs font-medium text-slate-500">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            {{ $report->address }}
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Tombol Lihat Foto (Jika ada foto) --}}
                            @if ($report->photo)
                            <a href="{{ asset('storage/' . $report->photo) }}" target="_blank" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                Lihat Foto
                            </a>
                            @endif

                            {{-- LOGIKA TOMBOL KLAIM ATAU BADGE SELESAI --}}
                            @if ($report->status == 'resolved' || $report->status == 'selesai')
                            {{-- Jika sudah selesai: Kunci dan beri label hijau --}}
                            <span class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2 text-xs font-bold text-emerald-700">
                                Tuntas Dikembalikan
                            </span>
                            @else
                            {{-- Jika belum selesai & jenisnya penemuan: Tampilkan tombol klaim --}}
                            @if ($report->item_kind == 'penemuan')
                            @auth
                            @if ($report->user_id !== auth()->id())
                            <a href="{{ route('barang.klaim.create', $report->id) }}"
                                class="rounded-xl bg-[#0c203b] px-5 py-2 text-xs font-bold text-white hover:bg-slate-800 transition-all">
                                Klaim Temuan
                            </a>
                            @else
                            <a href="{{ route('laporan.barang.saya.show', $report) }}" class="rounded-xl bg-slate-100 px-5 py-2 text-xs font-bold text-slate-600">
                                Lihat Tanggapan
                            </a>
                            @endif
                            @endauth
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center text-slate-500">
                    Belum ada laporan barang hilang atau ditemukan.
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        </section>
    </main>
</x-public-layout>