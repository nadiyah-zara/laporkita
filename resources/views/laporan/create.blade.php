<x-public-layout title="Form Pelaporan Infrastruktur - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5">
                <a href="{{ route('laporan.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Beranda Laporan</span>
                </a>
            </div>

            {{-- HEADER --}}
            <div class="mb-6 flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Form Pelaporan Infrastruktur</h1>
                    <p class="text-xs text-slate-500">Laporkan kerusakan fasilitas umum untuk segera diverifikasi dan ditangani dinas terkait.</p>
                </div>
            </div>

            {{-- KARTU FORM --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-9">

                <form id="form-laporan" method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-7">
                    @csrf

                    {{-- RINGKASAN ERROR --}}
                    @if ($errors->any())
                        <div id="galat" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
                            <p class="mb-1">Laporan belum terkirim. Periksa isian berikut:</p>
                            <ul class="list-disc space-y-0.5 pl-5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                            <p class="mt-2 font-normal text-rose-600">Foto perlu dipilih ulang karena browser tidak menyimpan berkas yang sudah dipilih.</p>
                        </div>
                        <script>document.getElementById('galat')?.scrollIntoView({ behavior: 'smooth', block: 'center' });</script>
                    @endif

                    {{-- 1. KATEGORI & PRIORITAS --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="category_id" class="block text-xs font-bold text-slate-800">
                                Kategori Kerusakan Fasilitas <span class="text-rose-500"></span>
                            </label>
                            <select id="category_id" name="category_id" required
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs font-semibold text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                                <option value="">Pilih Kategori Kerusakan...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800">
                                Tingkat Prioritas Penanganan <span class="text-rose-500"></span>
                            </label>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                <label class="flex cursor-pointer items-start gap-2.5 rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs transition hover:bg-slate-100 has-[:checked]:border-[#0066FF] has-[:checked]:bg-blue-50/50">
                                    <input type="radio" name="urgency" value="standar" class="mt-0.5 text-[#0066FF] focus:ring-[#0066FF]" {{ old('urgency', 'standar') == 'standar' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-bold text-slate-800">Standar</p>
                                        <p class="text-[10px] text-slate-400">Antrean pengerjaan reguler</p>
                                    </div>
                                </label>
                                <label class="flex cursor-pointer items-start gap-2.5 rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs transition hover:bg-rose-50/40 has-[:checked]:border-rose-300 has-[:checked]:bg-rose-50/60">
                                    <input type="radio" name="urgency" value="kritis" class="mt-0.5 text-rose-600 focus:ring-rose-500" {{ old('urgency') == 'kritis' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-bold text-rose-600">Darurat (Kritis)</p>
                                        <p class="text-[10px] text-slate-400">Bahaya fatal kecelakaan</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- JUDUL --}}
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-800">Judul Singkat Laporan <span class="text-rose-500"></span></label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255"
                            placeholder="Contoh: Jalan Berlubang Parah di Depan Pintu Masuk Kampus"
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                        @error('title') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- DESKRIPSI --}}
                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-800">Deskripsi Rinci Kerusakan & Dampak <span class="text-rose-500"></span></label>
                        <textarea id="description" name="description" rows="3" required
                            placeholder="Jelaskan ukuran perkiraan lubang, dampak bagi lalu lintas warga, atau potensi genangan saat hujan..."
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 2. LOKASI (peta) --}}
                    <div>
                        <x-location-picker :lat="old('latitude')" :lng="old('longitude')" />
                    </div>

                    {{-- ALAMAT (terisi otomatis dari peta, tetap bisa diubah) --}}
                    <div>
                        <label for="address" class="block text-xs font-bold text-slate-800">Alamat / Patokan Lokasi <span class="text-rose-500"></span></label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" required maxlength="255"
                            placeholder="Terisi otomatis dari peta. Contoh: Depan SDN 060, Jl. Jamin Ginting"
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                        @error('address') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- 3. FOTO --}}
                    <script>
                        window.unggahFoto = function () {
                            return {
                                files: [],
                                error: '',
                                maks: 5,

                                init() {
                                    const form = this.$el.closest('form');
                                    if (!form) return;

                                    form.addEventListener('submit', (e) => {
                                        this.sinkron();

                                        if (this.files.length < 2) {
                                            e.preventDefault();
                                            this.error = 'Harap unggah minimal 2 foto bukti kerusakan (1 tampak luas dan 1 jarak dekat).';
                                            this.$el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            return;
                                        }

                                        // Cegah kirim dobel
                                        const tombol = form.querySelector('[type=submit]');
                                        if (tombol) {
                                            setTimeout(() => {
                                                tombol.disabled = true;
                                                tombol.querySelector('span').textContent = 'Mengirim...';
                                            }, 0);
                                        }
                                    });

                                    // Tombol aktif lagi bila pengguna kembali ke halaman ini
                                    window.addEventListener('pageshow', () => {
                                        const tombol = form.querySelector('[type=submit]');
                                        if (tombol) {
                                            tombol.disabled = false;
                                            tombol.querySelector('span').textContent = 'Kirim Laporan Resmi';
                                        }
                                    });
                                },

                                tambah(daftar) {
                                    this.error = '';

                                    for (const f of Array.from(daftar)) {
                                        if (!f.type.startsWith('image/')) {
                                            this.error = 'Hanya berkas gambar (JPG, PNG, WEBP) yang diizinkan.';
                                            continue;
                                        }
                                        if (f.size > 5 * 1024 * 1024) {
                                            this.error = 'Ukuran foto "' + f.name + '" melebihi 5 MB. Harap perkecil ukuran foto.';
                                            continue;
                                        }
                                        if (this.files.length >= this.maks) {
                                            this.error = 'Maksimal ' + this.maks + ' foto.';
                                            break;
                                        }
                                        this.files.push({ file: f, url: URL.createObjectURL(f), name: f.name });
                                    }

                                    this.sinkron();
                                },

                                hapus(i) {
                                    if (this.files[i]) URL.revokeObjectURL(this.files[i].url);
                                    this.files.splice(i, 1);
                                    this.error = '';
                                    this.sinkron();
                                },

                                // Salin foto yang dipilih ke input yang benar-benar dikirim ke server
                                sinkron() {
                                    try {
                                        const dt = new DataTransfer();
                                        this.files.forEach((x) => dt.items.add(x.file));
                                        if (this.$refs.kirim) this.$refs.kirim.files = dt.files;
                                    } catch (err) {
                                        console.error('Sinkron foto gagal:', err);
                                    }
                                },
                            };
                        };
                    </script>

                    <div x-data="unggahFoto()">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800">Unggah Bukti Foto Kerusakan <span class="text-rose-500"></span></label>
                            <span class="text-[11px] font-semibold" :class="files.length >= 2 ? 'text-emerald-600' : 'text-amber-600'">
                                <span x-text="files.length"></span> dari maks 5 foto (wajib minimal 2)
                            </span>
                        </div>

                        {{-- Input yang dikirim ke server --}}
                        <input type="file" name="photos[]" multiple x-ref="kirim" class="hidden" tabindex="-1">
                        {{-- Input pemilih berkas (tidak ikut terkirim) --}}
                        <input type="file" accept="image/*" multiple x-ref="pilih" class="hidden"
                            @change="tambah($event.target.files); $event.target.value = ''">

                        <div class="group mt-2 cursor-pointer rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-6 text-center transition hover:border-[#0066FF] hover:bg-blue-50/20"
                            @click="$refs.pilih.click()" @dragover.prevent @drop.prevent="tambah($event.dataTransfer.files)">

                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-[#0066FF] transition group-hover:scale-110">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                            </div>
                            <p class="mt-3 text-xs font-bold text-slate-800 group-hover:text-[#0066FF]">Pilih Foto atau Tarik ke Sini</p>
                            <p class="mt-1 text-[11px] text-slate-400">1 foto tampak luas (konteks jalan) dan 1 foto jarak dekat. JPG/PNG/WEBP, maks 5 MB per foto. Bisa ditambah satu per satu.</p>

                            <template x-if="files.length">
                                <div class="mt-5 flex flex-wrap justify-center gap-3" @click.stop>
                                    <template x-for="(f, i) in files" :key="f.url">
                                        <div class="relative">
                                            <img :src="f.url" alt="Pratinjau" class="h-24 w-28 rounded-xl border border-slate-200 object-cover shadow-sm">
                                            <button type="button" @click="hapus(i)" title="Hapus foto ini"
                                                class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-[11px] font-bold text-white shadow transition hover:bg-rose-700 active:scale-90">✕</button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <p x-show="error" x-text="error" x-cloak class="mt-2 text-xs font-semibold text-rose-600"></p>

                        @php
                            $errFoto = collect($errors->get('photos'))
                                ->merge(collect($errors->get('photos.*'))->flatten())
                                ->unique();
                        @endphp
                        @foreach ($errFoto as $pesan)
                            <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $pesan }}</p>
                        @endforeach
                    </div>

                    {{-- 4. IDENTITAS PELAPOR --}}
                    <div class="grid grid-cols-1 gap-6 border-t border-slate-100 pt-6 md:grid-cols-2">
                        <div>
                            <label for="reporter_name" class="block text-xs font-bold text-slate-800">Nama Lengkap Pelapor <span class="text-rose-500"></span></label>
                            <input type="text" id="reporter_name" name="reporter_name" value="{{ old('reporter_name', auth()->user()->name ?? '') }}" required maxlength="150"
                                placeholder="Masukkan nama Anda..."
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                            @error('reporter_name') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="reporter_phone" class="block text-xs font-bold text-slate-800">Nomor WhatsApp Aktif <span class="text-rose-500"></span></label>
                            <input type="tel" id="reporter_phone" name="reporter_phone" value="{{ old('reporter_phone') }}" required maxlength="25"
                                placeholder="08xxxxxxxxxx (untuk pembaruan status pengerjaan)"
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                            @error('reporter_phone') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- FOOTER & TOMBOL KIRIM --}}
                    <div class="flex flex-col gap-4 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <svg class="h-4 w-4 shrink-0 text-[#0066FF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Data pelapor dilindungi kerahasiaannya untuk keperluan konfirmasi dinas.</span>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0066FF] px-8 py-3.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span>Kirim Laporan Resmi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</x-public-layout>