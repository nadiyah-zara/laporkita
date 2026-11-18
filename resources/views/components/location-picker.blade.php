@props([
'lat' => null,
'lng' => null,
'addressInput' => 'address',
'center' => [3.5952, 98.6722], // pusat peta awal (Medan), ubah sesuai kota layananmu
])

@php
$cfg = [
'lat' => ($lat !== null && $lat !== '') ? (float) $lat : null,
'lng' => ($lng !== null && $lng !== '') ? (float) $lng : null,
'center' => $center,
'addressInput' => $addressInput,
];
@endphp

@once
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    window.lokasiPicker = function(cfg) {
        // Objek Leaflet disimpan di luar state Alpine supaya tidak dibungkus proxy
        let map = null;
        let marker = null;

        return {
            lat: cfg.lat,
            lng: cfg.lng,
            mode: 'cari',
            q: '',
            hasil: [],
            mencari: false,
            mendeteksi: false,
            pesan: '',
            otomatis: '',

            init() {
                const ada = this.lat !== null && this.lng !== null;

                map = L.map(this.$refs.peta).setView(ada ? [this.lat, this.lng] : cfg.center, ada ? 17 : 13);

                // Peta jalan modern (Esri), tanpa API key
                const jalan = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: 'Tiles &copy; Esri',
                }).addTo(map);

                // Peta standar OpenStreetMap (cadangan)
                const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                });

                const satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: 'Tiles &copy; Esri',
                });

                L.control.layers({
                    'Peta': jalan,
                    'OpenStreetMap': osm,
                    'Satelit': satelit
                }, null, {
                    position: 'topright'
                }).addTo(map);

                // Kalau peta Esri gagal dimuat, pindah otomatis ke OpenStreetMap
                let gagal = 0;
                jalan.on('tileerror', () => {
                    if (++gagal >= 3 && map.hasLayer(jalan)) {
                        map.removeLayer(jalan);
                        osm.addTo(map);
                    }
                });

                if (ada) this.taruh(this.lat, this.lng);

                map.on('click', (e) => this.taruh(e.latlng.lat, e.latlng.lng, true));
                setTimeout(() => map.invalidateSize(), 300);
            },

            taruh(lat, lng, isiAlamat = false) {
                lat = Number(lat);
                lng = Number(lng);
                this.lat = Number(lat.toFixed(6));
                this.lng = Number(lng.toFixed(6));

                if (!marker) {
                    marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map);
                    marker.on('dragend', () => {
                        const p = marker.getLatLng();
                        this.taruh(p.lat, p.lng, true);
                    });
                } else {
                    marker.setLatLng([lat, lng]);
                }

                if (isiAlamat) this.alamatDariTitik(lat, lng);
            },

            // Variasi ejaan yang sering bikin pencarian gagal
            variasi(q) {
                const rapi = q.replace(/\bdr\.?\s*/gi, 'Dr. ').replace(/\s+/g, ' ').trim();
                const daftar = [
                    q,
                    rapi,
                    rapi.replace(/mansyur/gi, 'Mansur'),
                    rapi.replace(/mansur/gi, 'Mansyur'),
                    rapi.replace(/\bDr\.\s*/g, 'Dokter '),
                ];
                return [...new Set(daftar.map(v => v.trim()).filter(Boolean))].slice(0, 4);
            },

            async nominatim(q) {
                const [lat, lng] = cfg.center;
                const d = 0.25; // area utama sekitar kota layanan
                const viewbox = [lng - d, lat + d, lng + d, lat - d].join(',');
                const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=id&accept-language=id' +
                    '&viewbox=' + viewbox + '&q=' + encodeURIComponent(q);

                const res = await fetch(url);
                if (!res.ok) throw new Error('nominatim');
                return (await res.json()).map(r => ({
                    place_id: 'n' + r.place_id,
                    lat: r.lat,
                    lon: r.lon,
                    display_name: r.display_name,
                }));
            },

            async photon(q) {
                const [lat, lng] = cfg.center;
                const url = 'https://photon.komoot.io/api/?limit=5&lat=' + lat + '&lon=' + lng + '&q=' + encodeURIComponent(q);

                const res = await fetch(url);
                if (!res.ok) throw new Error('photon');
                const data = await res.json();

                return (data.features || []).map((f, i) => {
                    const p = f.properties || {};
                    const jalan = [p.street, p.housenumber].filter(Boolean).join(' ');
                    return {
                        place_id: 'p' + i + '-' + (p.osm_id || ''),
                        lat: f.geometry.coordinates[1],
                        lon: f.geometry.coordinates[0],
                        display_name: [p.name, jalan, p.district, p.city, p.state].filter(Boolean).join(', '),
                    };
                });
            },

            async cari() {
                const q = this.q.trim();
                if (q.length < 3) {
                    this.pesan = 'Ketik minimal 3 huruf nama tempat atau jalan.';
                    return;
                }

                this.mencari = true;
                this.pesan = '';
                this.hasil = [];

                try {
                    // 1. Nominatim dengan beberapa variasi ejaan (jeda 1 detik antar percobaan sesuai aturan layanan)
                    for (const v of this.variasi(q)) {
                        try {
                            this.hasil = await this.nominatim(v);
                        } catch (e) {
                            this.hasil = [];
                        }
                        if (this.hasil.length) break;
                        await new Promise(r => setTimeout(r, 1100));
                    }

                    // 2. Cadangan: Photon, lebih toleran terhadap salah ketik
                    if (!this.hasil.length) {
                        for (const v of this.variasi(q).slice(0, 2)) {
                            try {
                                this.hasil = await this.photon(v);
                            } catch (e) {
                                this.hasil = [];
                            }
                            if (this.hasil.length) break;
                        }
                    }

                    if (!this.hasil.length) {
                        this.pesan = 'Tempat itu belum ada di data peta. Coba ketik nama jalannya saja, lalu geser pin ke lokasi yang tepat.';
                    }
                } finally {
                    this.mencari = false;
                }
            },

            pilih(r) {
                this.taruh(r.lat, r.lon);
                map.setView([r.lat, r.lon], 17);
                this.hasil = [];
                this.isiKolomAlamat(r.display_name);
            },

            deteksi() {
                if (!navigator.geolocation) {
                    this.pesan = 'Browser tidak mendukung deteksi lokasi.';
                    return;
                }

                this.mendeteksi = true;
                this.pesan = '';

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.taruh(pos.coords.latitude, pos.coords.longitude, true);
                        map.setView([pos.coords.latitude, pos.coords.longitude], 17);
                        this.mendeteksi = false;
                    },
                    (err) => {
                        this.mendeteksi = false;
                        this.pesan = err.code === 1 ?
                            'Izin lokasi ditolak. Izinkan akses lokasi di browser, atau gunakan pencarian nama tempat.' :
                            'Lokasi tidak dapat dideteksi. Gunakan pencarian nama tempat.';
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000
                    }
                );
            },

            async alamatDariTitik(lat, lng) {
                try {
                    const res = await fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=18&accept-language=id&lat=' + lat + '&lon=' + lng);
                    if (!res.ok) return;
                    const d = await res.json();
                    if (d.display_name) this.isiKolomAlamat(d.display_name);
                } catch (e) {
                    /* alamat tetap bisa diketik manual */
                }
            },

            // Hanya mengisi kolom alamat bila masih kosong atau isinya hasil isian otomatis sebelumnya
            isiKolomAlamat(teks) {
                const el = document.querySelector('[name="' + cfg.addressInput + '"]');
                if (!el) return;

                const ringkas = teks.split(',').slice(0, 4).join(',').trim().slice(0, 250);
                if (el.value === '' || el.value === this.otomatis) {
                    el.value = ringkas;
                    this.otomatis = ringkas;
                    el.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            },
        };
    };
</script>
@endonce

<div x-data="lokasiPicker(@js($cfg))" class="mt-4">

    <div class="mb-2 flex items-center justify-between">
        <span class="text-xs font-bold text-slate-800">Tentukan Titik Lokasi pada Peta <span class="text-rose-500"></span></span>
    </div>

    {{-- DUA PILIHAN --}}
    <div class="inline-flex rounded-xl bg-slate-100 p-1 text-[11px] font-bold">
        <button type="button" @click="mode = 'cari'"
            class="rounded-lg px-4 py-1.5 transition"
            :class="mode === 'cari' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500'">Cari Nama Tempat</button>
        <button type="button" @click="mode = 'gps'"
            class="rounded-lg px-4 py-1.5 transition"
            :class="mode === 'gps' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500'">Lokasi Saya Saat Ini</button>
    </div>

    {{-- PILIHAN 1: CARI --}}
    <div x-show="mode === 'cari'" class="relative mt-3">
        <div class="flex gap-2">
            <input type="text" x-model="q" @keydown.enter.prevent="cari()"
                placeholder="Contoh: Masjid Raya Al-Mashun, atau Jl. Jamin Ginting Medan"
                class="w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
            <button type="button" @click="cari()" :disabled="mencari"
                class="shrink-0 rounded-xl bg-blue-600 px-5 text-xs font-bold text-white hover:bg-blue-700 disabled:opacity-60">
                <span x-text="mencari ? 'Mencari...' : 'Cari'"></span>
            </button>
        </div>

        <ul x-show="hasil.length" x-cloak class="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
            <template x-for="r in hasil" :key="r.place_id">
                <li>
                    <button type="button" @click="pilih(r)" class="block w-full border-b border-slate-100 px-4 py-2.5 text-left text-[11px] leading-snug text-slate-700 last:border-0 hover:bg-blue-50"
                        x-text="r.display_name"></button>
                </li>
            </template>
        </ul>
    </div>

    {{-- PILIHAN 2: GPS --}}
    <div x-show="mode === 'gps'" x-cloak class="mt-3">
        <button type="button" @click="deteksi()" :disabled="mendeteksi"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-700 disabled:opacity-60">
            <span x-text="mendeteksi ? 'Mendeteksi lokasi...' : 'Deteksi Lokasi Saya'"></span>
        </button>
        <p class="mt-1.5 text-[10px] text-slate-400">Pakai ini hanya bila Anda sedang berada di lokasi kejadian.</p>
    </div>

    <p x-show="pesan" x-text="pesan" x-cloak class="mt-2 text-[11px] font-semibold text-rose-600"></p>

    {{-- PETA --}}
    <div class="relative isolate z-0 mt-3 h-64 w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
        <div x-ref="peta" class="h-full w-full"></div>
    </div>

    <p class="mt-2 text-[11px]" :class="lat !== null ? 'font-semibold text-emerald-600' : 'text-slate-400'">
        <template x-if="lat !== null"><span x-text="'✓ Titik dipilih: ' + lat + ', ' + lng"></span></template>
        <template x-if="lat === null"><span>Belum ada titik. Cari nama tempat, gunakan lokasi saat ini, atau klik langsung pada peta.</span></template>
    </p>
    <p class="text-[10px] text-slate-400">Klik peta atau geser pin untuk menyesuaikan titik yang tepat.</p>

    <input type="hidden" name="latitude" :value="lat">
    <input type="hidden" name="longitude" :value="lng">

    @error('latitude') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
</div>