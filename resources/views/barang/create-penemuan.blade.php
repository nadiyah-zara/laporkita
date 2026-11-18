<x-public-layout title="Isi Form Penemuan - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-6">
                <a href="{{ route('laporan.barang.pilih') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- HEADER JUDUL --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">
                    Isi Form Penemuan
                </h1>
            </div>

            {{-- KARTU FORM UTAMA --}}
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-10">
                <form method="POST" action="{{ route('laporan.barang.store') }}" class="space-y-8">
                    @csrf
                    <input type="hidden" name="item_kind" value="penemuan">

                    {{-- 1. DETAIL BARANG & KARAKTERISTIK --}}
                    <div>
                        <div class="flex items-center gap-2 text-sm font-bold text-[#0c203b]">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>1. Detail Barang & Karakteristik</span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-800">Nama Barang <span class="text-rose-500"></span></label>
                                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Sebutkan nama barang secara umum" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                                <p class="mt-1 text-[10px] text-slate-400">Tuliskan nama benda tanpa memberi tahu bentuk secara spesifik.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-800">Kategori Barang <span class="text-rose-500"></span></label>
                                <select name="category_id" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                                    <option value="">Pilih Kategori Barang</option>
                                    @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-800">Deskripsi & Ciri-Ciri Khusus <span class="text-rose-500"></span></label>
                            <textarea name="description" rows="3" required placeholder="Sebutkan ciri spesifik seperti warna khas, stiker, nomor seri (jika ada), goresan/cacat unik, atau isi di dalam dompet/tas..." class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">{{ old('description') }}</textarea>
                            <p class="mt-1 text-[10px] text-amber-600">Tips: Jangan sebutkan PIN, kode keamanan, atau nominal uang tunai spesifik demi menjaga keabsahan verifikasi pemilik sah nantinya.</p>
                        </div>
                    </div>

                    {{-- 2. WAKTU & LOKASI DENGAN GPS REAL-TIME (SESUAI GAMBAR) --}}
                    <div class="border-t border-slate-100 pt-6"
                        >
                        <div class="flex items-center gap-2 text-sm font-bold text-[#0c203b]">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                            <span>2. Waktu & Lokasi</span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-800">Tanggal Kejadian <span class="text-rose-500"></span></label>
                                <input type="date" name="event_date" value="{{ old('event_date', date('Y-m-d')) }}" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-800">Perkiraan Jam Kejadian <span class="text-rose-500"></span></label>
                                <input type="time" name="event_time" value="{{ old('event_time') }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-800">Alamat Patokan / Landmark Detail <span class="text-rose-500"></span></label>
                            <input type="text" name="address" value="{{ old('address') }}" required placeholder="Misal: Di bangku tunggu Halte Transjakarta Harmoni Koridor 1, dekat pintu masuk barat" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                        </div>

                        {{-- MAPS SATELIT INTERAKTIF DENGAN TOMBOL DETEKSI GPS --}}
                        <x-location-picker :lat="old('latitude')" :lng="old('longitude')" />
                    </div>

                    {{-- 3. WHATSAPP TERVERIFIKASI --}}
                    <div class="border-t border-slate-100 pt-6">
                        <div class="flex items-center gap-2 text-sm font-bold text-[#0c203b]">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>3. WhatsApp Terverifikasi</span>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-800">Nomor WhatsApp Aktif Pelapor <span class="text-rose-500"></span></label>
                            <div class="mt-1.5 flex rounded-xl border border-slate-200 bg-[#f0f4ff]/40 overflow-hidden">
                                <span class="flex items-center px-4 font-bold text-xs text-slate-600 bg-slate-100">+62</span>
                                <input type="text" name="reporter_phone" value="{{ old('reporter_phone') }}" required placeholder="812-3456-7890" class="w-full p-3.5 text-xs text-slate-800 bg-transparent focus:outline-none">
                            </div>
                            <p class="mt-1.5 flex items-center gap-1 text-[10px] text-blue-600">
                                Nomor Anda tidak akan ditampilkan ke publik. Sistem menggunakan link terproteksi untuk menghubungkan warga yang mengklaim.
                            </p>
                        </div>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="flex justify-end border-t border-slate-100 pt-6">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-8 py-3.5 text-xs font-bold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-700">
                            <span>▷ Kirim Laporan Resmi (Terbit di Peta)</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</x-public-layout>