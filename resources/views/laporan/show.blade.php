<x-public-layout title="Detail Laporan {{ $report->ticket_code }} - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-6">
                <a href="{{ url()->previous() ?? route('laporan.infrastruktur.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- HEADER JUDUL KODE TIKET --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">
                    Detail {{ $report->ticket_code }}
                </h1>
            </div>

            {{-- KARTU DETAIL UTAMA --}}
            <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm sm:p-10">
                {{-- BADGE STATUS & WAKTU --}}
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-md {{ $report->status == 'in_progress' ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-800' }} px-3 py-1 text-xs font-bold">
                        {{ $report->status == 'in_progress' ? 'Sedang Diperbaiki' : 'Perlu Diperbaiki' }}
                    </span>
                    <span class="text-xs font-medium text-slate-400">{{ $report->created_at->diffForHumans() }}</span>
                </div>

                <h2 class="mt-4 text-2xl font-bold tracking-tight text-[#0c203b]">{{ $report->title }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $report->description }}</p>
                <p class="mt-2 font-mono text-xs text-slate-500">📍 {{ $report->address }}</p>

                {{-- SECTION PROGRES DENGAN SLIDER STACKED INTERAKTIF (ALPINE.JS) --}}
                @php
                $progresList = [];
                $photos = $report->photos ?? [];
                if (empty($photos) && $report->photo) {
                $photos = [$report->photo];
                }

                // 1. FOTO BUKTI 1 DARI WARGA (TAMPAK LUAS)
                if (!empty($photos[0])) {
                $progresList[] = [
                'foto' => asset('storage/' . $photos[0]),
                'tahap' => 'Bukti Warga (Tampak Luas)',
                'judul' => $report->title,
                'tanggal' => $report->created_at->translatedFormat('d F Y'),
                'catatan' => $report->description,
                ];
                }

                // 2. FOTO BUKTI 2 DARI WARGA (JARAK DEKAT)
                if (!empty($photos[1])) {
                $progresList[] = [
                'foto' => asset('storage/' . $photos[1]),
                'tahap' => 'Bukti Warga (Detail Kerusakan)',
                'judul' => 'Kondisi Fisik Jarak Dekat',
                'tanggal' => $report->created_at->translatedFormat('d F Y'),
                'catatan' => 'Foto bukti visual jarak dekat untuk penaksiran volume kerusakan dan spesifikasi penanganan teknis oleh dinas.',
                ];
                }

                // 3. FOTO UPDATE PENGERJAAN DARI DINAS (JIKA STATUS SEDANG DIKERJAKAN)
                if ($report->supervisor_notes || $report->status === 'in_progress') {
                $fotoDinas = !empty($photos[2])
                ? asset('storage/' . $photos[2])
                : 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?w=600';

                $progresList[] = [
                'foto' => $fotoDinas,
                'tahap' => 'Update Pengawas Lapangan',
                'judul' => 'Tindak Lanjut & Pengerjaan Fisik',
                'tanggal' => ($report->supervisor_updated_at ?? $report->updated_at)->translatedFormat('d F Y'),
                'catatan' => $report->supervisor_notes ?? 'Pekerjaan lapis aus AC-WC dan pemadatan struktur sedang berlangsung di lokasi aduan.',
                ];
                }

                // 4. FOTO HASIL PERBAIKAN SELESAI (JIKA SUDAH TUNTAS)
                if (!empty($report->photo_after) || in_array($report->status, ['resolved', 'done'])) {
                $progresList[] = [
                'foto' => $report->photo_after ? asset('storage/' . $report->photo_after) : 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'tahap' => 'Hasil Akhir Tuntas',
                'judul' => 'Perbaikan Telah Selesai 100%',
                'tanggal' => $report->updated_at->translatedFormat('d F Y'),
                'catatan' => $report->completion_notes ?? 'Konstruksi fasilitas telah rampung secara menyeluruh. Dokumen BAST resmi telah diverifikasi dinas.',
                ];
                }

                // Fallback jika database masih kosong
                if (empty($progresList)) {
                $progresList[] = [
                'foto' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'tahap' => 'Laporan Masuk',
                'judul' => $report->title,
                'tanggal' => $report->created_at->translatedFormat('d F Y'),
                'catatan' => $report->description ?? 'Menunggu tinjauan tim teknis dinas terkait di lokasi.',
                ];
                }
                @endphp

                <div class="mt-8 border-t border-slate-100 pt-6"
                    x-data="{
            activeIdx: 0,
            items: {{ json_encode($progresList) }},
            next() {
                this.activeIdx = (this.activeIdx + 1) % this.items.length;
            },
            prev() {
                this.activeIdx = (this.activeIdx - 1 + this.items.length) % this.items.length;
            }
         }">

                    {{-- PROGRESS HEADER & BAR --}}
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-extrabold text-[#0c203b]">Progres</h3>
                        <div class="flex items-center gap-1 text-xs font-bold text-emerald-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $report->progress_percentage ?? 0 }}%</span>
                        </div>
                    </div>

                    <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-blue-100/60">
                        <div class="h-full rounded-full bg-[#0066FF] transition-all duration-500"
                            style="width: {{ $report->progress_percentage ?? 0 }}%">
                        </div>
                    </div>

                    {{-- AREA INTERAKTIF: FOTO BERTUMPUK (KIRI) & TEKS DINAMIS (KANAN) --}}
                    <div class="mt-8 grid grid-cols-1 items-center gap-8 md:grid-cols-12">

                        {{-- KOLOM KIRI: STACKED PHOTOS YANG BISA DI-KLIK/GESER --}}
                        <div class="flex flex-col items-center md:col-span-5 select-none">
                            {{-- Container Foto Bertumpuk --}}
                            <div class="relative h-64 w-52 cursor-pointer group" @click="next()" title="Klik foto untuk melihat progres berikutnya">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="absolute inset-0 transition-all duration-300 ease-out"
                                        :style="activeIdx === idx 
                                ? 'transform: rotate(2deg) scale(1) translate(0px, 0px); z-index: 20; opacity: 1;' 
                                : ((idx === (activeIdx + 1) % items.length) 
                                    ? 'transform: rotate(-6deg) scale(0.96) translate(-10px, 8px); z-index: 10; opacity: 0.9;' 
                                    : 'transform: rotate(6deg) scale(0.92) translate(10px, 12px); z-index: 5; opacity: 0;')">
                                        <img :src="item.foto"
                                            class="h-60 w-48 rounded-2xl border-2 border-white object-cover shadow-xl group-hover:scale-105 transition-transform" />
                                    </div>
                                </template>
                            </div>

                            {{-- Indikator & Tombol Geser Kecil di Bawah Foto --}}
                            <div class="mt-3 flex items-center gap-3">
                                <button type="button" @click="prev()" class="rounded-full bg-slate-100 p-1.5 text-slate-600 hover:bg-slate-200">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <span class="text-[11px] font-bold text-slate-400">
                                    <span x-text="activeIdx + 1"></span> / <span x-text="items.length"></span> Foto
                                </span>
                                <button type="button" @click="next()" class="rounded-full bg-slate-100 p-1.5 text-slate-600 hover:bg-slate-200">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">(Klik foto untuk berganti)</p>
                        </div>

                        {{-- KOLOM KANAN: KATA-KATA & CATATAN YANG BERGANTI OTOMATIS SESUAI FOTO --}}
                        <div class="md:col-span-7">
                            {{-- Tag Tahap --}}
                            <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600 uppercase tracking-wider"
                                x-text="items[activeIdx].tahap">
                            </span>

                            {{-- Judul Progres --}}
                            <h4 class="text-xl font-bold text-[#0c203b] mt-1.5 transition-all"
                                x-text="items[activeIdx].judul">
                            </h4>

                            {{-- Tanggal Progres --}}
                            <p class="text-xs font-medium text-slate-400 mt-0.5"
                                x-text="items[activeIdx].tanggal">
                            </p>

                            {{-- Kotak Catatan Pengawas / Warga --}}
                            <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-600 border border-slate-100 shadow-inner">
                                <p class="italic leading-relaxed transition-all"
                                    x-text="items[activeIdx].catatan">
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
    </main>
</x-public-layout>