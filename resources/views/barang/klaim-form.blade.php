@php $hilang = ($report->item_kind === 'kehilangan'); @endphp

<x-public-layout title="Klaim Temuan - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5">
                <a href="{{ route('laporan.barang.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>

            {{-- HEADER HALAMAN --}}
            <div class="mb-6 flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-mono text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Klaim Tiket {{ $report->ticket_code ?? '#BRGT261211001' }}
                    </h1>
                    <p class="text-xs text-slate-500">Kirimkan ciri rahasia kepemilikan untuk diverifikasi oleh pihak terkait.</p>
                </div>
            </div>

            {{-- INFO NOTIFIKASI PANDUAN --}}
            <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50/40 p-5 shadow-xs">
                <div class="flex items-start gap-3.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0066FF] text-white shadow-xs">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900">
                                {{ $hilang ? 'Saya Menemukan Barang Ini' : 'Saya Ingin Mengklaim Barang Temuan' }}
                            </h2>
                            <span class="rounded-md border border-blue-100 bg-white px-2 py-0.5 text-[10px] font-bold text-[#0066FF]">Alur Resmi</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $hilang ? 'Untuk penemu: sebutkan karakteristik unik barang yang Anda amankan agar pemilik dapat memverifikasinya sebelum janjian bertemu.' : 'Untuk pemilik sah: Buktikan kepemilikan melalui ciri unik dan rincian bagian dalam yang hanya Anda ketahui.' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- FORM KLAIM UTAMA --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-9">
                <form method="POST" action="{{ route('barang.klaim.store', $report->id) }}" enctype="multipart/form-data" class="space-y-7">
                    @csrf

                    {{-- BAGIAN 1: CIRI KHUSUS RAHASIA --}}
                    <div>
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">1</span>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Ciri Spesifik & Rahasia</h3>
                                <p class="text-xs text-slate-400">Jawab pertanyaan berikut dengan rincian yang hanya diketahui oleh pemilik asli.</p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-800">1. Identitas unik yang hanya Anda ketahui <span class="text-rose-500">*</span></label>
                                <p class="text-[10px] text-slate-400">Sebutkan ciri khas/goresan/stiker khusus.</p>
                                <input type="text" name="secret_unique_feature" value="{{ old('secret_unique_feature') }}" required placeholder="Contoh: Ada stiker kecil di sudut atau sedikit sobekan di tepi" 
                                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-800">2. Warna bagian dalam / lapisan interior <span class="text-rose-500">*</span></label>
                                <p class="text-[10px] text-slate-400">Warna furing atau isi bagian tersembunyi.</p>
                                <input type="text" name="secret_inner_color" value="{{ old('secret_inner_color') }}" required placeholder="Contoh: Kain satin motif bergaris biru gelap" 
                                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-xs font-bold text-slate-800">3. Uraikan secara rinci tentang barang tersebut <span class="text-rose-500">*</span></label>
                            <textarea name="secret_full_detail" rows="3" required placeholder="Jelaskan secara mendalam agar pihak pemeriksa yakin bahwa Anda adalah pemilik sahnya..." 
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">{{ old('secret_full_detail') }}</textarea>
                        </div>
                    </div>

                    {{-- BAGIAN 2: UNGGAH DOKUMEN BUKTI --}}
                    <div class="border-t border-slate-100 pt-7"
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
                                    reader.onload = (e) => { this.previewUrl = e.target.result; };
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
                            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">2</span>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Lampiran Bukti Pendukung</h3>
                                <p class="text-xs text-slate-400">{{ $hilang ? 'Lampirkan foto fisik barang yang saat ini Anda amankan.' : 'Lampirkan foto lama bersama barang atau nota pembelian.' }}</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50/50 p-6">
                            <div class="flex items-center justify-between">
                                <span class="rounded-md bg-[#0066FF] px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white">Wajib Diisi</span>
                                <span x-show="fileName" x-cloak class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Berkas Siap Diunggah
                                </span>
                            </div>

                            <h4 class="mt-2 text-xs font-bold text-slate-800">{{ $hilang ? 'Foto Barang Saat Ini' : 'Foto Dokumen / Foto Lama' }}</h4>

                            <input type="file" name="proof_photo" id="proof_photo" x-ref="proofInput" @change="handleFile($event)" class="hidden" accept="image/*,application/pdf" required>

                            {{-- Dropzone Area --}}
                            <div x-show="!fileName" @click="$refs.proofInput.click()"
                                class="group mt-4 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white p-7 text-center transition hover:border-[#0066FF] hover:bg-blue-50/20">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-[#0066FF] transition group-hover:scale-110">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-xs font-bold text-slate-800 group-hover:text-[#0066FF]">Pilih File Bukti (JPG, PNG, atau PDF)</p>
                                <p class="mt-0.5 text-[10px] text-slate-400">Ukuran berkas maksimal 5 MB</p>
                            </div>

                            {{-- File Preview --}}
                            <div x-show="fileName" x-cloak class="mt-4 rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                                <div class="flex items-center gap-4">
                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" alt="Preview" class="h-16 w-16 rounded-xl object-cover border border-slate-100 flex-shrink-0">
                                    </template>
                                    <template x-if="!previewUrl && isPdf">
                                        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-500 border border-rose-100">
                                            <span class="text-xs font-bold">PDF</span>
                                        </div>
                                    </template>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-bold text-slate-800" x-text="fileName"></p>
                                        <p class="text-[11px] text-slate-400" x-text="fileSize"></p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <button type="button" @click="$refs.proofInput.click()" class="rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-[#0066FF] hover:bg-blue-100">Ganti Berkas</button>
                                            <button type="button" @click="clearFile()" class="rounded-lg bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:bg-rose-100">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- WHATSAPP PENEMU --}}
                    @if ($hilang)
                        <div class="border-t border-slate-100 pt-7">
                            <label class="block text-xs font-bold text-slate-800">Nomor WhatsApp Aktif Anda <span class="text-rose-500">*</span></label>
                            <div class="mt-1.5 flex overflow-hidden rounded-xl border border-slate-200 bg-slate-50/70 focus-within:border-[#0066FF] focus-within:bg-white">
                                <span class="flex items-center bg-slate-100 px-4 text-xs font-bold text-slate-600 border-r border-slate-200">+62</span>
                                <input type="text" name="claimant_phone" value="{{ old('claimant_phone') }}" required placeholder="812-3456-7890"
                                    class="w-full bg-transparent p-3 text-xs text-slate-800 focus:outline-none">
                            </div>
                            @error('claimant_phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-500">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Nomor hanya akan dibagikan ke pemilik setelah kedua belah pihak saling memverifikasi.</span>
                            </div>
                        </div>
                    @endif

                    {{-- SUBMIT --}}
                    <div class="flex justify-end border-t border-slate-100 pt-6">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-7 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $hilang ? 'Kirim Berkas Temuan' : 'Kirim Berkas Kepemilikan' }}</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>
</x-public-layout>