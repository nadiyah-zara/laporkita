<x-public-layout title="Form Pelaporan - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-6">
                <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- HEADER JUDUL DENGAN AKSEN GARIS BIRU TEBAL --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">
                    Form Pelaporan
                </h1>
            </div>

            {{-- KARTU FORM UTAMA --}}
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-10">

                <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- 1. KATEGORI & TINGKAT KEGENTINGAN --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Kategori --}}
                        <div>
                            <label for="category_id" class="block text-xs font-bold text-slate-800">
                                Kategori Kerusakan Fasilitas <span class="text-rose-500">*</span>
                            </label>
                            <select id="category_id" name="category_id" required
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-sm text-slate-800 transition-all focus:border-blue-600 focus:bg-white focus:outline-none">
                                <option value="">Pilih Kategori Kerusakan...</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tingkat Kegentingan / Bahaya --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-800">
                                Tingkat Kegentingan / Bahaya <span class="text-rose-500">*</span>
                            </label>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                <label class="flex cursor-pointer items-start gap-2.5 rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3 text-xs transition-all hover:bg-[#f0f4ff]">
                                    <input type="radio" name="urgency" value="standar" class="mt-0.5 text-blue-600 focus:ring-blue-500" {{ old('urgency', 'standar') == 'standar' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-bold text-slate-900">Biasa (Standar)</p>
                                        <p class="text-[10px] text-slate-500">Antrean 3x24 Jam</p>
                                    </div>
                                </label>
                                <label class="flex cursor-pointer items-start gap-2.5 rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3 text-xs transition-all hover:bg-[#f0f4ff]">
                                    <input type="radio" name="urgency" value="kritis" class="mt-0.5 text-rose-600 focus:ring-rose-500" {{ old('urgency') == 'kritis' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-bold text-rose-600">Kritis / Darurat</p>
                                        <p class="text-[10px] text-slate-500">Bahaya Pengendara Jalan</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- JUDUL LAPORAN --}}
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-800">
                            Judul Singkat Laporan Kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            placeholder="Contoh: Jalan Rusak dan Berbatu Parah"
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-600 focus:bg-white focus:outline-none" />
                        @error('title') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 2. DESKRIPSI DETAIL KERUSAKAN --}}
                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-800">
                            Deskripsi Detail Kerusakan & Dampak <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="3" required
                            placeholder="Sebutkan perkiraan ukuran lubang (panjang/lebar/dalam), intensitas genangan air, atau potensi bahaya kecelakaan bagi warga..."
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-600 focus:bg-white focus:outline-none">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 3. TITIK LOKASI GEOSPASIAL KERUSAKAN DENGAN GPS --}}
                    <div class="rounded-2xl border border-blue-100 bg-[#f0f4ff]/60 p-5"
                        x-data="{
        gettingLocation: false,
        lat: '{{ old('latitude', '-6.319452') }}',
        lng: '{{ old('longitude', '106.865120') }}',
        suggestions: [],
        showSuggestions: false,
        searchTimer: null,
        getLocation() {
            this.gettingLocation = true;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.lat = pos.coords.latitude.toFixed(6);
                        this.lng = pos.coords.longitude.toFixed(6);
                        this.gettingLocation = false;
                        this.showSuggestions = false;
                    },
                    (err) => {
                        alert('Gagal mendeteksi lokasi: ' + err.message);
                        this.gettingLocation = false;
                    }
                );
            } else {
                alert('Browser tidak mendukung deteksi lokasi.');
                this.gettingLocation = false;
            }
        },
        searchAddress(val) {
            clearTimeout(this.searchTimer);
            if (val.length < 3) { this.suggestions = []; this.showSuggestions = false; return; }
            this.searchTimer = setTimeout(async () => {
                try {
                    const res = await fetch(
                        'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(val) +
                        '&format=json&limit=5&countrycodes=id&accept-language=id',
                        { headers: { 'Accept-Language': 'id' } }
                    );
                    const data = await res.json();
                    this.suggestions = data;
                    this.showSuggestions = data.length > 0;
                } catch(e) {
                    this.suggestions = [];
                }
            }, 400);
        },
        selectAddress(item) {
            this.lat = parseFloat(item.lat).toFixed(6);
            this.lng = parseFloat(item.lon).toFixed(6);
            this.$refs.addressInput.value = item.display_name;
            this.suggestions = [];
            this.showSuggestions = false;
        }
     }">

                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-900">
                                    Titik Lokasi Geospasial Kerusakan <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-500">Tentukan titik seakurat mungkin agar mobilisasi alat berat cepat dan tepat sasaran.</p>
                            </div>
                            <button type="button" @click="getLocation()" :disabled="gettingLocation"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                    <circle cx="12" cy="9" r="2.5" />
                                </svg>
                                <span x-text="gettingLocation ? 'Mencari Titik...' : 'Gunakan Titik Lokasi Perangkat'"></span>
                            </button>
                        </div>

                        {{-- Baris Input Patokan & Tampilan Koordinat Terkunci --}}
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-12">
                            <div class="relative sm:col-span-8">
                                <input type="text" name="address" x-ref="addressInput"
                                    value="{{ old('address') }}" required
                                    placeholder="Patokan lokasi: Contoh '50 meter sebelum lampu merah simpang, depan minimarket...'"
                                    @input="searchAddress($event.target.value)"
                                    @blur="setTimeout(() => showSuggestions = false, 200)"
                                    value="{{ old('address') }}" required
                                    @focus="suggestions.length > 0 && (showSuggestions = true)"
                                    class="w-full rounded-xl border border-slate-200 bg-white p-3 text-xs text-slate-800 placeholder:text-slate-400 focus:border-blue-600 focus:outline-none">

                                {{-- Dropdown Saran Lokasi --}}
                                {{-- Dropdown Saran Lokasi (versi cantik) --}}
                                <div x-show="showSuggestions" x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="absolute z-30 mt-1.5 w-full overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl shadow-slate-200/60">

                                    {{-- Header dropdown --}}
                                    <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-3 py-2">
                                        <svg class="h-3 w-3 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span class="text-[10px] font-bold tracking-wide text-slate-500 uppercase">Saran Lokasi</span>
                                    </div>

                                    <template x-for="(item, idx) in suggestions" :key="item.place_id">
                                        <button type="button"
                                            @mousedown.prevent="selectAddress(item)"
                                            class="group flex w-full items-start gap-3 px-4 py-3 text-left transition-colors hover:bg-blue-50 border-b border-slate-50 last:border-0">
                                            {{-- Nomor urut --}}
                                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-600 group-hover:bg-blue-200"
                                                x-text="idx + 1"></span>
                                            {{-- Teks lokasi --}}
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-xs font-semibold text-[#0c203b] group-hover:text-blue-700"
                                                    x-text="item.display_name.split(',')[0]"></p>
                                                <p class="mt-0.5 truncate text-[10px] text-slate-400"
                                                    x-text="item.display_name.split(',').slice(1).join(',').trim()"></p>
                                            </div>
                                            {{-- Ikon panah --}}
                                            <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-slate-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </template>

                                    {{-- Footer kecil --}}
                                    <div class="flex items-center justify-end gap-1 border-t border-slate-50 bg-slate-50 px-3 py-1.5">
                                        <span class="text-[9px] text-slate-400">Data peta dari OpenStreetMap</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-blue-200 bg-white px-3 py-2 text-xs font-semibold text-blue-700 sm:col-span-4">
                                <span class="flex items-center gap-1 text-[11px]">🔗 Koordinat GPS Terkunci:</span>
                                <span class="font-mono text-slate-900 text-[11px]" x-text="lat + ', ' + lng"></span>
                            </div>
                        </div>

                        <input type="hidden" name="latitude" :value="lat">
                        <input type="hidden" name="longitude" :value="lng">

                        {{-- Map Canvas Preview --}}
                        <div class="mt-3 h-36 w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100 relative">
                            <iframe class="h-full w-full pointer-events-none opacity-80"
                                :src="'https://maps.google.com/maps?q=' + lat + ',' + lng + '&hl=id&z=15&output=embed'">
                            </iframe>
                        </div>
                    </div>

                    {{-- 4. UNGGAH BUKTI VISUAL KERUSAKAN (MINIMAL 2 FOTO) --}}
                    <div x-data="{
                        previews: [],
                        handleFiles(files) {
                            this.previews = [];
                            for(let i = 0; i < files.length; i++) {
                                const reader = new FileReader();
                                reader.onload = (e) => { this.previews.push(e.target.result); };
                                reader.readAsDataURL(files[i]);
                            }
                        }
                    }">
                        <label class="block text-xs font-bold text-slate-800">
                            Unggah Bukti Visual Kerusakan (Minimal 2 Foto) <span class="text-rose-500">*</span>
                        </label>

                        <div class="mt-2 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/50 p-6 text-center transition-all hover:border-blue-400">
                            <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <label class="mt-2 cursor-pointer text-xs font-bold text-slate-800 hover:text-blue-600">
                                <span>Tarik foto ke sini atau klik untuk mengambil dari galeri/kamera</span>
                                <input type="file" name="photos[]" multiple required accept="image/*" class="sr-only" @change="handleFiles($event.target.files)">
                            </label>
                            <p class="mt-1 text-[11px] text-slate-500">1 Foto tampak luas (konteks jalan & rambu) dan 1 foto jarak dekat untuk estimasi volume dinas teknis (Format: JPG, PNG maks 10MB)</p>

                            {{-- Live Previews Grid --}}
                            <template x-if="previews.length > 0">
                                <div class="mt-4 flex flex-wrap gap-3">
                                    <template x-for="(img, idx) in previews" :key="idx">
                                        <img :src="img" class="h-24 w-28 rounded-xl border border-slate-200 object-cover shadow-sm">
                                    </template>
                                </div>
                            </template>
                        </div>
                        @error('photos') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 5. IDENTITAS PELAPOR & KONTAK WHATSAPP --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="reporter_name" class="block text-xs font-bold text-slate-800">
                                Nama Lengkap Pelapor <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="reporter_name" name="reporter_name" value="{{ old('reporter_name', auth()->user()->name ?? '') }}" required
                                placeholder="Masukkan nama sesuai KTP"
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                            @error('reporter_name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="reporter_phone" class="block text-xs font-bold text-slate-800">
                                Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="reporter_phone" name="reporter_phone" value="{{ old('reporter_phone') }}" required
                                placeholder="+62 8xx-xxxx-xxxx (Untuk notifikasi status perbaikan)"
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                            @error('reporter_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- FOOTER PERLINDUNGAN DATA & TOMBOL KIRIM --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-6">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Identitas pelapor dilindungi sesuai UU Perlindungan Data Pribadi (UU PDP).</span>
                        </div>
                        <button type="submit" class="rounded-xl bg-[#0066FF] px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-500/25 transition-all hover:bg-blue-700">
                            Kirim Laporan Kerusakan Resmi
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </main>
</x-public-layout>