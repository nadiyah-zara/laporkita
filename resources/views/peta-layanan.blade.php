<x-public-layout title="Peta Layanan - LaporKita">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- CUSTOM PIN SESUAI DESAIN GAMBAR --}}
    <style>
        .pin-wrap {
            background: none;
            border: 0;
        }

        .pin {
            width: 42px;
            height: 42px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2.5px solid #ffffff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.35);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .pin:hover {
            transform: rotate(-45deg) scale(1.15);
            z-index: 9999 !important;
        }

        .pin svg {
            transform: rotate(45deg);
            width: 20px;
            height: 20px;
            color: #ffffff;
        }

        /* 1. Pin Biru = Barang Ditemukan (Tangan) */
        .pin-ditemukan {
            background: #1d4ed8;
        }

        /* 2. Pin Merah = Barang Dicari / Kehilangan (Kaca Pembesar) */
        .pin-dicari {
            background: #dc2626;
        }

        /* 3. Pin Oranye = Kerusakan Infrastruktur (Kunci Pas) */
        .pin-infrastruktur {
            background: #d97706;
        }

        /* Popup Box Custom Style */
        .report-popup .leaflet-popup-content-wrapper {
            border-radius: 1.25rem;
            padding: 0;
            box-shadow: 0 15px 35px rgba(12, 32, 59, 0.25);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .report-popup .leaflet-popup-content {
            margin: 16px;
            width: 270px !important;
        }

        .leaflet-popup-tip-container {
            display: none;
        }

        .leaflet-bottom.leaflet-right {
            margin-bottom: 5.5rem;
            margin-right: 1.25rem;
        }

        .leaflet-control-zoom {
            border: 0 !important;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15) !important;
            border-radius: 0.75rem !important;
            overflow: hidden;
        }

        .leaflet-control-zoom a {
            background-color: #ffffff !important;
            color: #0c203b !important;
            font-weight: 800 !important;
            width: 36px !important;
            height: 36px !important;
            line-height: 36px !important;
        }
    </style>

    <div class="mx-auto max-w-6xl px-6 pt-8 pb-16">

        {{-- ====================== PETA UTAMA ====================== --}}
        <form method="GET" action="{{ route('peta.layanan') }}"
            x-data="{ layerOpen: false, layer: '{{ $layer }}' }">

            <input type="hidden" name="layer" :value="layer">
            <input type="hidden" name="radius" value="{{ $radius }}">

            <div class="relative overflow-hidden rounded-3xl shadow-2xl ring-1 ring-slate-200">

                {{-- Canvas Leaflet Peta --}}
                <div id="map"
                    class="relative z-0 h-[480px] w-full sm:h-[560px]"
                    data-markers="{{ json_encode($markers ?? []) }}"
                    data-lat="{{ $center['lat'] ?? 3.5653 }}"
                    data-lng="{{ $center['lng'] ?? 98.6565 }}"
                    data-radius="{{ $radius ?? 2.5 }}">
                </div>

                {{-- TOP BAR: SEARCH & DROPDOWN LAYER AKTIF --}}
                <div class="absolute inset-x-4 top-4 z-20 flex flex-col gap-3 sm:flex-row">

                    {{-- Search Input --}}
                    <label class="flex flex-1 items-center gap-3 rounded-2xl bg-white/95 px-4 py-3 shadow-lg backdrop-blur">
                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari laporan atau lokasi (contoh: Politeknik Negeri Medan, Dr. Mansyur)..."
                            class="w-full border-0 bg-transparent p-0 text-xs font-bold text-[#0c203b] placeholder:text-slate-400 focus:outline-none focus:ring-0">
                    </label>

                    {{-- Dropdown Pilihan Layer Aktif --}}
                    <div class="relative sm:w-[48%]" @click.outside="layerOpen = false">
                        <button type="button" @click="layerOpen = !layerOpen"
                            class="flex w-full items-center justify-between gap-3 rounded-2xl bg-white/95 px-4 py-3 text-xs font-bold text-[#0c203b] shadow-lg backdrop-blur">
                            <span class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                                <span>Layer Aktif:
                                    <span class="text-blue-600" x-text="{ barang: 'Barang Hilang & Ditemukan', infrastruktur: 'Infrastruktur', semua: 'Semua Laporan' }[layer]"></span>
                                </span>
                            </span>
                            <svg class="h-4 w-4 transition-transform text-slate-400" :class="layerOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="layerOpen" x-cloak x-transition
                            class="absolute right-0 z-30 mt-2 w-full overflow-hidden rounded-2xl border border-slate-100 bg-white p-1.5 shadow-2xl">
                            @foreach (['semua' => 'Semua Laporan', 'infrastruktur' => 'Infrastruktur', 'barang' => 'Barang Hilang & Ditemukan'] as $value => $label)
                            <button type="button"
                                @click="layer = '{{ $value }}'; layerOpen = false; $nextTick(() => $el.closest('form').submit())"
                                :class="layer === '{{ $value }}' ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-600 hover:bg-slate-50 font-semibold'"
                                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-left text-xs transition-colors">
                                <span>{{ $label }}</span>
                                @if ($value === 'infrastruktur')
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                @elseif ($value === 'barang')
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- BOTTOM BAR: INFO RADIUS & PERLUAS RADAR --}}
                <div class="absolute inset-x-4 bottom-4 z-20 flex items-center justify-between gap-4 rounded-2xl bg-white/95 px-5 py-3 shadow-lg backdrop-blur">
                    <p class="text-xs font-semibold text-slate-700">
                        Menampilkan <span class="font-extrabold text-[#0c203b]">{{ $reports->count() }}</span> laporan aktif dalam radius {{ $radiusLabel }} km
                    </p>
                    <a href="{{ route('peta.layanan', array_filter(['q' => $search, 'layer' => $layer !== $defaultLayer ? $layer : null, 'radius' => $nextRadius])) }}"
                        class="shrink-0 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                        {{ $canExpand ? 'Perluas Radar' : 'Reset Radar' }} &rarr;
                    </a>
                </div>
            </div>
        </form>

        {{-- ====================== DAFTAR LAPORAN DI BAWAH PETA ====================== --}}
        <section class="mt-14" x-data="{ photo: null }">
            <div class="flex items-center justify-between">
                <h2 class="flex items-center gap-3 text-2xl font-extrabold tracking-tight text-[#0c203b] sm:text-3xl">
                    <span class="h-8 w-1.5 rounded-full bg-blue-600"></span>
                    Daftar Laporan Tersemat
                </h2>
                <span class="text-xs font-semibold text-slate-400">Total: {{ $reports->count() }} Titik</span>
            </div>

            <div class="mt-8 space-y-5">
                @forelse ($reports as $report)
                @php
                $isInfra = ($report->type === 'infrastruktur');
                $isFound = ($report->item_kind === 'penemuan');
                @endphp

                <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100 transition-all hover:shadow-md sm:p-7">
                    <div class="flex items-start justify-between gap-4">
                        @if ($isInfra)
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold {{ $report->status === 'in_progress' ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-800' }}">
                            🔧 {{ $report->status === 'in_progress' ? 'Sedang Diperbaiki' : 'Perlu Diperbaiki' }}
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold {{ $isFound ? 'bg-blue-50 text-blue-700' : 'bg-rose-50 text-rose-600' }}">
                            @if ($isFound) ✋ Ditemukan @else 🔍 Dicari @endif
                        </span>
                        @endif
                        <span class="text-xs font-medium text-slate-400">{{ $report->created_at->diffForHumans() }}</span>
                    </div>

                    <h3 class="mt-3 text-xl font-extrabold tracking-tight text-[#0c203b] sm:text-2xl">{{ $report->title }}</h3>
                    <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $report->description }}</p>

                    <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-t border-slate-50 pt-4">
                        <p class="font-mono text-xs text-slate-500">📍 {{ $report->address }}</p>

                        <div class="flex items-center gap-2">
                            @if ($report->photo)
                            <button type="button" @click="photo = '{{ asset('storage/' . $report->photo) }}'"
                                class="rounded-xl bg-slate-700/80 px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-slate-800">
                                Lihat Foto
                            </button>
                            @endif

                            @if ($isInfra)
                            <a href="{{ route('laporan.infrastruktur.detail', $report->id) }}"
                                class="rounded-xl bg-[#0c203b] px-5 py-2 text-xs font-bold text-white transition-colors hover:bg-slate-800">
                                Lihat Detail
                            </a>
                            @else
                            <a href="{{ route('laporan.barang.index') }}"
                                class="rounded-xl bg-[#0c203b] px-5 py-2 text-xs font-bold text-white transition-colors hover:bg-slate-800">
                                {{ $isFound ? 'Klaim Temuan' : 'Saya Menemukannya' }}
                            </a>
                            @endif
                        </div>
                    </div>
                </article>
                @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
                    <p class="text-base font-bold text-[#0c203b]">Belum ada laporan aktif di layer ini</p>
                    <p class="mt-1 text-xs text-slate-400">Silakan ubah layer aktif ke "Semua Laporan" atau perluas radius pencarian.</p>
                </div>
                @endforelse
            </div>

            {{-- Modal Foto --}}
            <div x-show="photo" x-cloak x-transition.opacity
                @keydown.escape.window="photo = null" @click.self="photo = null"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-black/75 p-6 backdrop-blur-sm">
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

    {{-- LEAFLET JAVASCRIPT & LOGIKA MARKER SESUAI GAMBAR --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function() {
            const mapEl = document.getElementById('map');

            // Ambil data dari atribut HTML dengan fallback aman
            let reports = [];
            try {
                reports = JSON.parse(mapEl.getAttribute('data-markers') || '[]');
            } catch (e) {
                reports = [];
            }

            const lat = parseFloat(mapEl.getAttribute('data-lat')) || 3.5653;
            const lng = parseFloat(mapEl.getAttribute('data-lng')) || 98.6565;
            const center = [lat, lng];
            const radiusKm = parseFloat(mapEl.getAttribute('data-radius')) || 2.5;

            // SVG ICON SESUAI FOTO DESAIN UI/UX
            const icons = {
                // Tangan (Ditemukan)
                ditemukan: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2"/><path d="M14 10V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v2"/><path d="M10 10.5V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg>',
                // Kaca Pembesar (Dicari)
                dicari: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>',
                // Kunci Pas Silang (Infrastruktur)
                infrastruktur: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
            };

            const esc = (s) => String(s || '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c]));

            // Inisialisasi Peta
            const map = L.map('map', {
                zoomControl: false,
                scrollWheelZoom: false
            }).setView(center, 16);
            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);

            // TAMPILAN CITRA SATELIT ASLI + LABEL JALAN
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Tiles &copy; Esri'
            }).addTo(map);
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19
            }).addTo(map);

            map.on('focus', () => map.scrollWheelZoom.enable());
            map.on('blur', () => map.scrollWheelZoom.disable());

            // Lingkaran Radius
            L.circle(center, {
                radius: radiusKm * 1000,
                color: '#ffffff',
                weight: 1.5,
                dashArray: '6 6',
                fillColor: '#3b82f6',
                fillOpacity: 0.05,
                interactive: false
            }).addTo(map);

            const points = [];
            reports.forEach((r) => {
                const icon = L.divIcon({
                    className: 'pin-wrap',
                    html: `<div class="pin pin-${r.type}">${icons[r.type]}</div>`,
                    iconSize: [42, 42],
                    iconAnchor: [21, 44],
                    popupAnchor: [0, -42]
                });

                let badgeHtml, actionText;
                if (r.type === 'infrastruktur') {
                    const isProgress = (r.status === 'in_progress');
                    badgeHtml = `<span style="${isProgress ? 'background:#d97706;color:#fff' : 'background:#fef3c7;color:#92400e'};padding:3px 8px;border-radius:6px;font-weight:700;font-size:10px"> ${isProgress ? 'Sedang Diperbaiki' : 'Perlu Diperbaiki'}</span>`;
                    actionText = 'Lihat Detail';
                } else if (r.type === 'ditemukan') {
                    badgeHtml = `<span style="background:#eff6ff;color:#1d4ed8;padding:3px 8px;border-radius:6px;font-weight:700;font-size:10px"> Ditemukan</span>`;
                    actionText = 'Klaim Temuan';
                } else {
                    badgeHtml = `<span style="background:#fef2f2;color:#b91c1c;padding:3px 8px;border-radius:6px;font-weight:700;font-size:10px"> Dicari</span>`;
                    actionText = 'Saya Menemukannya';
                }

                // Popup Template Persis Foto Desain
                const popup = `
                    <div style="font-family:inherit">
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:10px;color:#64748b;margin-bottom:8px">
                            ${badgeHtml}
                            <span style="font-size:10px;color:#94a3b8">${esc(r.time)}</span>
                        </div>
                        <p style="margin:0 0 4px;font-size:14px;font-weight:800;color:#0c203b;line-height:1.3">${esc(r.title)}</p>
                        <p style="margin:0;font-size:11px;color:#475569;line-height:1.4">${esc(r.description).substring(0, 85)}...</p>
                        <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f1f5f9;padding-top:8px">
                            <span style="font-family:ui-monospace,monospace;font-size:10px;color:#64748b;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">📍 ${esc(r.location)}</span>
                            <a href="${r.url}" style="background:#0c203b;color:#fff;font-size:10px;font-weight:700;padding:6px 12px;border-radius:8px;text-decoration:none;white-space:nowrap">${actionText}</a>
                        </div>
                    </div>`;

                L.marker([r.lat, r.lng], {
                        icon
                    }).addTo(map)
                    .bindPopup(popup, {
                        className: 'report-popup',
                        closeButton: false,
                        maxWidth: 280
                    });

                points.push([r.lat, r.lng]);
            });

            // Fokus otomatis ke titik laporan jika ada data
            if (points.length) {
                map.fitBounds(points, {
                    padding: [90, 90],
                    maxZoom: 17
                });
            }
        })();
    </script>
</x-public-layout>