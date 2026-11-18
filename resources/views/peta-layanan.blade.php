<x-public-layout title="Peta Sebaran Layanan - LaporKita">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- CUSTOM PIN & RADAR PULSE STYLE --}}
    <style>
        .pin-wrap {
            background: none !important;
            border: 0 !important;
            position: relative;
        }

        /* Gelombang Denyut Radar */
        .pin-radar {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 22px;
            height: 22px;
            margin: -11px 0 0 -11px;
            border-radius: 50%;
            animation: radar-pulse 2.2s infinite cubic-bezier(0.215, 0.61, 0.355, 1);
            pointer-events: none;
            z-index: 1;
        }

        @keyframes radar-pulse {
            0% { transform: scale(0.6); opacity: 0.9; }
            70% { transform: scale(3.2); opacity: 0.2; }
            100% { transform: scale(3.8); opacity: 0; }
        }

        /* Bentuk Fisik Pin */
        .pin {
            position: relative;
            z-index: 2;
            width: 42px;
            height: 42px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #ffffff;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.35), 0 4px 6px -2px rgba(0, 0, 0, 0.15);
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
        }

        .pin:hover {
            transform: rotate(-45deg) scale(1.18);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.45);
            z-index: 9999 !important;
        }

        .pin svg {
            transform: rotate(45deg);
            width: 20px;
            height: 20px;
            color: #ffffff;
            stroke-width: 2.2;
        }

        /* 1. Pin Biru = Barang Ditemukan */
        .pin-ditemukan { background: linear-gradient(135deg, #0066FF, #0047b3); }
        .radar-ditemukan { background-color: #0066FF; box-shadow: 0 0 14px #0066FF; }

        /* 2. Pin Merah = Barang Dicari */
        .pin-dicari { background: linear-gradient(135deg, #f43f5e, #be123c); }
        .radar-dicari { background-color: #f43f5e; box-shadow: 0 0 14px #f43f5e; }

        /* 3. Pin Oranye = Kerusakan Infrastruktur */
        .pin-infrastruktur { background: linear-gradient(135deg, #f59e0b, #b45309); }
        .radar-infrastruktur { background-color: #f59e0b; box-shadow: 0 0 14px #f59e0b; }

        /* 4. Pin Hijau = Lokasi Pencarian */
        .pin-lokasi { background: linear-gradient(135deg, #10b981, #047857); }
        .radar-lokasi { background-color: #10b981; box-shadow: 0 0 14px #10b981; }

        /* Popup Box Custom Style */
        .report-popup .leaflet-popup-content-wrapper {
            border-radius: 1.25rem;
            padding: 0;
            box-shadow: 0 20px 35px -5px rgba(12, 32, 59, 0.25);
            border: 1px solid rgba(226, 232, 240, 0.9);
            overflow: hidden;
        }

        .report-popup .leaflet-popup-content {
            margin: 16px !important;
            width: 275px !important;
            line-height: 1.5;
        }

        .leaflet-popup-tip-container { display: none; }

        .leaflet-control-zoom {
            border: 0 !important;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
            border-radius: 0.85rem !important;
            overflow: hidden;
        }

        .leaflet-control-zoom a {
            background-color: #ffffff !important;
            color: #0c203b !important;
            font-weight: 700 !important;
            width: 38px !important;
            height: 38px !important;
            line-height: 38px !important;
        }
    </style>

    <div class="mx-auto max-w-6xl px-4 pt-8 pb-16 sm:px-6">

        {{-- BAR PANDUAN CEPAT / LEGENDA & TOMBOL LAPOR --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            {{-- Legenda Indikator Titik Peta --}}
            <div class="flex flex-wrap items-center gap-2 rounded-2xl bg-white px-4 py-2.5 shadow-sm border border-slate-200/80 text-xs">
                <span class="font-bold text-slate-700 mr-1">Petunjuk Titik:</span>
                <span class="inline-flex items-center gap-1.5 font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> Kerusakan Infrastruktur
                </span>
                <span class="inline-flex items-center gap-1.5 font-semibold text-[#0066FF] bg-blue-50 px-2 py-0.5 rounded-md">
                    <span class="h-2 w-2 rounded-full bg-[#0066FF]"></span> Barang Ditemukan
                </span>
                <span class="inline-flex items-center gap-1.5 font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span> Barang Dicari
                </span>
            </div>

            {{-- Tombol Lapor Cepat --}}
            <div class="flex items-center gap-2">
                @if (Route::has('laporan.infrastruktur.create'))
                <a href="{{ route('laporan.infrastruktur.create') }}" 
                   class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-amber-600 transition active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Lapor Infrastruktur</span>
                </a>
                @endif

                @if (Route::has('laporan.barang.create'))
                <a href="{{ route('laporan.barang.create') }}" 
                   class="inline-flex items-center gap-1.5 rounded-xl bg-[#0066FF] px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Lapor Barang Hilang</span>
                </a>
                @endif
            </div>
        </div>

        {{-- ====================== PETA UTAMA ====================== --}}
        <form id="layerForm" method="GET" action="{{ route('peta.layanan') }}"
            x-data="{ layerOpen: false, layer: '{{ $layer }}' }">

            <input type="hidden" name="layer" :value="layer">
            <input type="hidden" name="radius" value="{{ $radius }}">

            <div class="relative overflow-hidden rounded-3xl shadow-xl ring-1 ring-slate-200">

                {{-- Canvas Leaflet Peta --}}
                <div id="map"
                    class="relative z-0 h-[520px] w-full sm:h-[600px]"
                    data-markers="{{ json_encode($markers ?? []) }}"
                    data-lat="{{ $center['lat'] ?? 3.5617 }}"
                    data-lng="{{ $center['lng'] ?? 98.6568 }}"
                    data-radius="{{ $radius ?? 2.5 }}">
                </div>

                {{-- TOP BAR: SEARCH & DROPDOWN LAYER AKTIF --}}
                <div class="absolute inset-x-4 top-4 z-20 flex flex-col gap-3 sm:flex-row">

                    {{-- Search Input Interaktif (Langsung Mengarahkan Peta) --}}
                    <div class="relative flex flex-1 items-center gap-2 rounded-2xl border border-white/90 bg-white/95 px-4 py-2.5 shadow-lg backdrop-blur-md">
                        <svg class="h-4 w-4 text-[#0066FF] shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="searchInput"
                            placeholder="Ketik lokasi atau nama laporan (contoh: Politeknik Negeri Medan, Jl. Dr. Mansyur)..."
                            class="w-full border-0 bg-transparent p-0 text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                        <button type="button" id="btnSearchLoc"
                            class="shrink-0 rounded-xl bg-blue-50 px-3.5 py-1.5 text-xs font-bold text-[#0066FF] hover:bg-blue-100 transition active:scale-95">
                            Cari Titik
                        </button>
                    </div>

                    {{-- Dropdown Pilihan Layer Aktif --}}
                    <div class="relative sm:w-[45%]" @click.outside="layerOpen = false">
                        <button type="button" @click="layerOpen = !layerOpen"
                            class="flex w-full items-center justify-between gap-3 rounded-2xl border border-white/90 bg-white/95 px-4 py-3 text-xs font-bold text-slate-800 shadow-lg backdrop-blur-md">
                            <span class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                                <span>Layer:
                                    <span class="text-[#0066FF]" x-text="{ barang: 'Barang Hilang & Temuan', infrastruktur: 'Infrastruktur Publik', semua: 'Semua Laporan' }[layer]"></span>
                                </span>
                            </span>
                            <svg class="h-4 w-4 text-slate-400 transition-transform" :class="layerOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="layerOpen" x-cloak x-transition
                            class="absolute right-0 z-30 mt-2 w-full overflow-hidden rounded-2xl border border-slate-100 bg-white p-1.5 shadow-2xl">
                            @foreach (['semua' => 'Semua Laporan', 'infrastruktur' => 'Infrastruktur Publik', 'barang' => 'Barang Hilang & Temuan'] as $value => $label)
                            <button type="button"
                                @click="layer = '{{ $value }}'; layerOpen = false; $nextTick(() => $el.closest('form').submit())"
                                :class="layer === '{{ $value }}' ? 'bg-blue-50 text-[#0066FF] font-bold' : 'text-slate-600 hover:bg-slate-50 font-semibold'"
                                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-left text-xs transition-colors">
                                <span>{{ $label }}</span>
                                @if ($value === 'infrastruktur')
                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                @elseif ($value === 'barang')
                                    <span class="h-2 w-2 rounded-full bg-[#0066FF]"></span>
                                @else
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- BOTTOM BAR: INFO RADIUS & STATUS PETA --}}
                <div class="absolute inset-x-4 bottom-4 z-20 flex items-center justify-between gap-4 rounded-2xl border border-white/90 bg-white/95 px-5 py-3 shadow-lg backdrop-blur-md">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <p id="mapInfoText" class="text-xs font-semibold text-slate-700">
                            Menampilkan <span class="font-extrabold text-slate-900">{{ $reports->count() }}</span> titik aktif dalam radius {{ $radiusLabel }} km
                        </p>
                    </div>

                    <a href="{{ route('peta.layanan', array_filter(['layer' => $layer !== $defaultLayer ? $layer : null, 'radius' => $nextRadius])) }}"
                        class="inline-flex items-center gap-1.5 shrink-0 text-xs font-bold text-[#0066FF] hover:text-blue-800 transition-colors">
                        <span>{{ $canExpand ? 'Perluas Radar Jangkauan' : 'Reset Radius Default' }}</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </form>

        {{-- ====================== DAFTAR LAPORAN DI BAWAH PETA ====================== --}}
        <section class="mt-14" x-data="{ photo: null }">
            <div class="flex items-center justify-between border-b border-slate-200/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1.5 rounded-full bg-[#0066FF]"></div>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                            Daftar Laporan Tersemat
                        </h2>
                        <p class="text-xs text-slate-500">Titik-titik laporan yang masuk dalam radar pemantauan saat ini.</p>
                    </div>
                </div>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#0066FF] border border-blue-100">
                    {{ $reports->count() }} Laporan
                </span>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($reports as $report)
                @php
                    $isInfra = ($report->type === 'infrastruktur');
                    $isFound = ($report->item_kind === 'penemuan');
                @endphp

                <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        {{-- Tag Status Berikon Vektor --}}
                        @if ($isInfra)
                            <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-bold {{ $report->status === 'in_progress' ? 'border-amber-200 bg-amber-500 text-white' : 'border-amber-200 bg-amber-50 text-amber-800' }}">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                                </svg>
                                <span>{{ $report->status === 'in_progress' ? 'Sedang Dikerjakan' : 'Perlu Diperbaiki' }}</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-bold {{ $isFound ? 'border-blue-200 bg-blue-50 text-[#0066FF]' : 'border-rose-200 bg-rose-50 text-rose-600' }}">
                                @if ($isFound)
                                    <svg class="h-3.5 w-3.5 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Barang Ditemukan</span>
                                @else
                                    <svg class="h-3.5 w-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" />
                                    </svg>
                                    <span>Barang Dicari</span>
                                @endif
                            </span>
                        @endif

                        <div class="flex items-center gap-1 text-xs text-slate-400">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <h3 class="mt-3 text-lg font-bold tracking-tight text-slate-900 sm:text-xl">{{ $report->title }}</h3>
                    <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">{{ $report->description }}</p>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-t border-slate-100 pt-3.5">
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <svg class="h-3.5 w-3.5 text-[#0066FF] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate max-w-md">{{ $report->address }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($report->photo)
                            <button type="button" @click="photo = '{{ asset('storage/' . $report->photo) }}'"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50 active:scale-95">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Foto</span>
                            </button>
                            @endif

                            @if ($isInfra)
                            <a href="{{ route('laporan.infrastruktur.detail', $report->id) }}"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-[#0066FF] px-4 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-blue-700 active:scale-95">
                                <span>Lihat Rincian</span>
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @else
                            <a href="{{ route('laporan.barang.index') }}"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-[#0066FF] px-4 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-blue-700 active:scale-95">
                                <span>{{ $isFound ? 'Klaim Temuan' : 'Bantu Temukan' }}</span>
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                </article>
                @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-bold text-slate-800">Tidak ada laporan pada area radar ini</p>
                    <p class="mt-0.5 text-xs text-slate-400">Coba ubah filter layer aktif atau perluas radius pencarian Anda.</p>
                </div>
                @endforelse
            </div>

            {{-- Modal Foto Interaktif --}}
            <div x-show="photo" x-cloak x-transition.opacity
                @keydown.escape.window="photo = null" @click.self="photo = null"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/80 p-6 backdrop-blur-sm">
                <div class="relative max-h-full max-w-2xl">
                    <img :src="photo" alt="Bukti Foto" class="max-h-[80vh] rounded-2xl bg-white object-contain shadow-2xl">
                    <button type="button" @click="photo = null"
                        class="absolute -right-3 -top-3 rounded-full bg-white p-2 text-slate-700 shadow-lg hover:bg-slate-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    </div>

    {{-- LEAFLET JAVASCRIPT & LOGIKA MARKER DENGAN OPENSTREETMAP (BEBAS API KEY) --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function() {
            const mapEl = document.getElementById('map');

            let reports = [];
            try {
                reports = JSON.parse(mapEl.getAttribute('data-markers') || '[]');
            } catch (e) {
                reports = [];
            }

            const lat = parseFloat(mapEl.getAttribute('data-lat')) || 3.5617;
            const lng = parseFloat(mapEl.getAttribute('data-lng')) || 98.6568;
            const center = [lat, lng];
            const radiusKm = parseFloat(mapEl.getAttribute('data-radius')) || 2.5;

            // IKON SVG RESMI
            const icons = {
                ditemukan: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>',
                dicari: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>',
                infrastruktur: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
                lokasi: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>'
            };

            const esc = (s) => String(s || '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));

            // Inisialisasi Peta
            const map = L.map('map', {
                zoomControl: false,
                scrollWheelZoom: false
            }).setView(center, 15);

            L.control.zoom({ position: 'bottomright' }).addTo(map);

            // ==============================================================
            // TILE PETA RESMI OPENSTREETMAP (100% GRATIS, TANPA API KEY, TERANG & LENGKAP)
            // ==============================================================
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            map.on('focus', () => map.scrollWheelZoom.enable());
            map.on('blur', () => map.scrollWheelZoom.disable());

            // Lingkaran Radar Pemantauan
            L.circle(center, {
                radius: radiusKm * 1000,
                color: '#0066FF',
                weight: 2,
                dashArray: '6 6',
                fillColor: '#0066FF',
                fillOpacity: 0.05,
                interactive: false
            }).addTo(map);

            // Pasang Marker Laporan
            const points = [];
            const markerLayers = {};

            reports.forEach((r) => {
                const iconHtml = icons[r.type] || icons.infrastruktur;
                const icon = L.divIcon({
                    className: 'pin-wrap',
                    html: `
                        <div class="pin-radar radar-${r.type}"></div>
                        <div class="pin pin-${r.type}">${iconHtml}</div>
                    `,
                    iconSize: [42, 42],
                    iconAnchor: [21, 42],
                    popupAnchor: [0, -42]
                });

                let badgeHtml, actionText;
                if (r.type === 'infrastruktur') {
                    const isProgress = (r.status === 'in_progress');
                    badgeHtml = `<span style="${isProgress ? 'background:#f59e0b;color:#fff' : 'background:#fef3c7;color:#b45309'};padding:3px 8px;border-radius:6px;font-weight:700;font-size:10px">${isProgress ? 'Sedang Dikerjakan' : 'Perlu Diperbaiki'}</span>`;
                    actionText = 'Lihat Detail';
                } else if (r.type === 'ditemukan') {
                    badgeHtml = `<span style="background:#eff6ff;color:#0066FF;padding:3px 8px;border-radius:6px;font-weight:700;font-size:10px">Barang Ditemukan</span>`;
                    actionText = 'Klaim Temuan';
                } else {
                    badgeHtml = `<span style="background:#fff1f2;color:#e11d48;padding:3px 8px;border-radius:6px;font-weight:700;font-size:10px">Barang Dicari</span>`;
                    actionText = 'Bantu Temukan';
                }

                const popup = `
                    <div style="font-family:inherit">
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:10px;color:#64748b;margin-bottom:8px">
                            ${badgeHtml}
                            <span style="font-size:10px;color:#94a3b8">${esc(r.time)}</span>
                        </div>
                        <p style="margin:0 0 5px;font-size:13px;font-weight:800;color:#0f172a;line-height:1.35">${esc(r.title)}</p>
                        <p style="margin:0;font-size:11px;color:#475569;line-height:1.45">${esc(r.description).substring(0, 85)}...</p>
                        
                        <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f1f5f9;padding-top:10px">
                            <span style="font-size:10px;color:#64748b;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(r.location)}</span>
                            <a href="${r.url}" style="background:#0066FF;color:#fff;font-size:10px;font-weight:700;padding:6px 12px;border-radius:8px;text-decoration:none;white-space:nowrap;display:inline-block">${actionText}</a>
                        </div>
                    </div>`;

                const marker = L.marker([r.lat, r.lng], { icon })
                    .addTo(map)
                    .bindPopup(popup, {
                        className: 'report-popup',
                        closeButton: false,
                        maxWidth: 285
                    });

                markerLayers[r.id] = marker;
                points.push([r.lat, r.lng]);
            });

            // Fokus mencakup semua laporan jika ada
            if (points.length) {
                map.fitBounds(points, { padding: [80, 80], maxZoom: 16 });
            }

            // ==============================================================
            // FITUR PENCARIAN CERDAS: LANGSUNG TERBANG KE LOKASI / LAPORAN
            // ==============================================================
            let searchMarker = null;
            const searchInput = document.getElementById('searchInput');
            const btnSearchLoc = document.getElementById('btnSearchLoc');
            const mapInfoText = document.getElementById('mapInfoText');

            async function performSearch(query) {
                if (!query) return;

                // 1. Cek apakah cocok dengan judul/alamat laporan yang sudah ada di peta
                const matched = reports.find(r => 
                    r.title.toLowerCase().includes(query.toLowerCase()) || 
                    (r.location && r.location.toLowerCase().includes(query.toLowerCase())) ||
                    (r.description && r.description.toLowerCase().includes(query.toLowerCase()))
                );

                if (matched && markerLayers[matched.id]) {
                    map.flyTo([matched.lat, matched.lng], 17, { duration: 1.3 });
                    markerLayers[matched.id].openPopup();
                    mapInfoText.innerHTML = `Peta diarahkan ke laporan: <b class="text-[#0066FF]">${esc(matched.title)}</b>`;
                    return;
                }

                // 2. Jika bukan judul laporan, cari nama tempat/jalan di OpenStreetMap
                btnSearchLoc.innerText = 'Mencari...';
                try {
                    // Beri prioritas area Kota Medan
                    const searchQuery = query.toLowerCase().includes('medan') ? query : (query + ', Medan');
                    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}&limit=1`;
                    
                    const res = await fetch(url);
                    const data = await res.json();

                    if (data && data.length > 0) {
                        const targetLat = parseFloat(data[0].lat);
                        const targetLng = parseFloat(data[0].lon);
                        const placeName = data[0].display_name;

                        // Peta terbang ke lokasi yang dicari
                        map.flyTo([targetLat, targetLng], 16, { duration: 1.5 });

                        if (searchMarker) map.removeLayer(searchMarker);

                        // Pin Khusus Lokasi (Hijau Berdenyut)
                        const locIcon = L.divIcon({
                            className: 'pin-wrap',
                            html: `
                                <div class="pin-radar radar-lokasi"></div>
                                <div class="pin pin-lokasi">${icons.lokasi}</div>
                            `,
                            iconSize: [42, 42],
                            iconAnchor: [21, 42],
                            popupAnchor: [0, -42]
                        });

                        searchMarker = L.marker([targetLat, targetLng], { icon: locIcon })
                            .addTo(map)
                            .bindPopup(`
                                <div style="font-family:inherit;padding:4px">
                                    <span style="background:#10b981;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px">Titik Lokasi Ditemukan</span>
                                    <p style="margin:6px 0 0;font-size:12px;font-weight:700;color:#0f172a;line-height:1.4">${esc(placeName)}</p>
                                </div>
                            `, { className: 'report-popup', closeButton: false, maxWidth: 260 })
                            .openPopup();

                        mapInfoText.innerHTML = `Peta diarahkan ke: <b class="text-emerald-600">${esc(query)}</b>`;
                    } else {
                        alert(`Titik "${query}" tidak ditemukan. Coba ketik nama jalan, kampus, atau tempat yang lebih spesifik.`);
                    }
                } catch (err) {
                    alert('Gagal mengambil titik lokasi. Pastikan koneksi internet Anda aktif.');
                } finally {
                    btnSearchLoc.innerText = 'Cari Titik';
                }
            }

            btnSearchLoc.addEventListener('click', () => {
                performSearch(searchInput.value.trim());
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Mencegah reload halaman
                    performSearch(searchInput.value.trim());
                }
            });
        })();
    </script>
</x-public-layout>