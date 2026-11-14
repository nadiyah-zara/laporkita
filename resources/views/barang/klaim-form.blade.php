<x-public-layout title="Klaim Temuan - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-6">
                <a href="{{ route('laporan.barang.index') }}" 
                class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- HEADER TITLE --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">
                    Klaim Temuan {{ $report->ticket_code ?? '#BRGT261211001' }}
                </h1>
            </div>

            {{-- BLUE NOTIFICATION CARD --}}
            <div class="mb-8 rounded-2xl border border-blue-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-md shadow-blue-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-[#0c203b]">Saya Ingin Mengklaim Barang</h3>
                            <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600">Alur Aktif</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                            Untuk pemilik sah: Buktikan kepemilikan melalui nomor seri, deskripsi rahasia, dan jadwalkan pengambilan fisik di posko berwenang.
                        </p>
                        <span class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-blue-600">
                            Mulai Verifikasi 4 Langkah &rarr;
                        </span>
                    </div>
                </div>
            </div>

            {{-- FORM KLAIM UTAMA --}}
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-10">
                <form method="POST" action="{{ route('barang.klaim.store', $report->id) }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    {{-- BAGIAN 1: VERIFIKASI CIRI SPESIFIK RAHASIA --}}
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">1</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Verifikasi Ciri Spesifik Rahasia</h4>
                                <p class="text-[11px] text-slate-400">Jawab rincian yang hanya diketahui oleh pemilik sejati.</p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-800">1. Apa identitas unik barang yang hanya anda ketahui <span class="text-rose-500">*</span></label>
                                <p class="text-[10px] text-slate-400">Sebutkan ciri khas yang hanya ada pada barang anda</p>
                                <input type="text" name="secret_unique_feature" value="{{ old('secret_unique_feature') }}" required placeholder="Contoh: ada goresan pada bagian ujung kanan dompet" class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-800">2. Apa warna furing atau lapisan kain di balik ritsleting uang kertas? <span class="text-rose-500">*</span></label>
                                <p class="text-[10px] text-slate-400">Warna tekstil interior bagian dalam dompet.</p>
                                <input type="text" name="secret_inner_color" value="{{ old('secret_inner_color') }}" required placeholder="Contoh: Lapisan kain satin bermotif garis biru dongker" class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-xs font-bold text-slate-800">3. Jelaskan secara perinci tentang barang <span class="text-rose-500">*</span></label>
                            <textarea name="secret_full_detail" rows="3" required placeholder="Jelaskan secara rinci sampai kami dapat percaya mengapa kamu adalah pemiliknya!" class="mt-2 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">{{ old('secret_full_detail') }}</textarea>
                        </div>
                    </div>

                    {{-- BAGIAN 2: UNGGAH DOKUMEN BUKTI PENDUKUNG --}}
                    <div class="border-t border-slate-100 pt-8"
                        x-data="{
         previewUrl: null,
         fileName: '',
         fileSize: '',
         isPdf: false,
         handleFile(event) {
             const file = event.target.files[0];
             if (!file) return;
             this.fileName = file.name;
             this.fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
             this.isPdf = file.type === 'application/pdf';
             
             if (file.type.startsWith('image/')) {
                 const reader = new FileReader();
                 reader.onload = (e) => {
                     this.previewUrl = e.target.result;
                 };
                 reader.readAsDataURL(file);
             } else {
                 this.previewUrl = null;
             }
         },
         clearFile() {
             this.previewUrl = null;
             this.fileName = '';
             this.fileSize = '';
             this.isPdf = false;
             this.$refs.proofInput.value = '';
         }
     }">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">2</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Unggah Dokumen Bukti Pendukung</h4>
                                <p class="text-[11px] text-slate-400">Bantu petugas mempercepat verifikasi dengan bukti kepemilikan terdahulu.</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-slate-100 bg-[#f0f4ff]/30 p-6">
                            <div class="flex items-center justify-between">
                                <span class="rounded bg-blue-600 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white">WAJIB</span>
                                <span x-show="fileName" x-cloak class="flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    File Siap Diunggah
                                </span>
                            </div>
                            <h5 class="mt-2 text-xs font-bold text-[#0c203b]">Foto Lama Bersama Barang</h5>
                            <p class="text-[11px] text-slate-400">Foto saat Anda menggunakan barang tersebut di masa lalu.</p>

                            {{-- Input File Asli (Tersembunyi) --}}
                            <input type="file"
                                name="proof_photo"
                                id="proof_photo"
                                x-ref="proofInput"
                                @change="handleFile($event)"
                                class="hidden"
                                accept="image/*,application/pdf"
                                required>

                            {{-- 1. Tampilan Box Upload (Jika Belum Memilih File) --}}
                            <div x-show="!fileName"
                                @click="$refs.proofInput.click()"
                                class="mt-4 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white p-8 text-center transition-all hover:border-blue-500 hover:bg-blue-50/20 group">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-xs font-bold text-slate-800 group-hover:text-blue-600">
                                    Pilih File (PDF/JPG)
                                </p>
                                <p class="mt-0.5 text-[10px] text-slate-400">Maksimal 5 MB</p>
                            </div>

                            {{-- 2. Tampilan Live Preview (Jika File Sudah Dipilih) --}}
                            <div x-show="fileName" x-cloak class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-center gap-4">
                                    {{-- Preview Gambar --}}
                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" alt="Preview Bukti" class="h-20 w-20 rounded-xl object-cover border border-slate-100 shadow-sm flex-shrink-0">
                                    </template>

                                    {{-- Preview Ikon PDF jika yang dipilih dokumen PDF --}}
                                    <template x-if="!previewUrl && isPdf">
                                        <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-500 border border-red-100">
                                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                    </template>

                                    {{-- Detail File & Tombol Ganti / Hapus --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-bold text-[#0c203b]" x-text="fileName"></p>
                                        <p class="text-[11px] text-slate-400" x-text="fileSize"></p>
                                        <div class="mt-2.5 flex items-center gap-2">
                                            <button type="button"
                                                @click="$refs.proofInput.click()"
                                                class="rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600 hover:bg-blue-100 transition-colors">
                                                Ganti Berkas
                                            </button>
                                            <button type="button"
                                                @click="clearFile()"
                                                class="rounded-lg bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-600 hover:bg-red-100 transition-colors">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- TOMBOL SUBMIT --}}
                    <div class="flex justify-end border-t border-slate-100 pt-6">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-8 py-3.5 text-xs font-bold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>Kirim Berkas & Tunggu Verifikasi untuk mendapatkan barcode</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>
</x-public-layout>