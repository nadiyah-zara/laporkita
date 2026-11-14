<x-public-layout title="Aktivitas Klaim - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI & TOMBOL SIMULASI DEMO DOSEN --}}
            <div class="mb-6 flex items-center justify-between">
                <a href="{{ route('laporan.barang.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>

                {{-- TOMBOL RAHASIA UNTUK PRESENTASI SENIN (Bisa memajukan stepper langsung di depan dosen!) --}}
                <a href="{{ route('barang.klaim.simulasi', $claim->id) }}" class="rounded-xl border border-dashed border-blue-300 bg-white px-3 py-1.5 text-[11px] font-bold text-blue-600 hover:bg-blue-50" title="Klik ini saat demo untuk memajukan status stepper tanpa repot!">
                    ⚡ Simulasi Penemu Menerima (Step Selanjutnya)
                </a>
            </div>

            {{-- HEADER JUDUL KODE KLAIM --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">
                    Klaim Temuan {{ $claim->claim_code }}
                </h1>
            </div>

            {{-- BANNER DITOLAK (tambahkan di sini) --}}
            @if ($claim->rejected_at)
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-bold text-rose-700">
                Klaim Anda ditolak oleh penemu. {{ $claim->rejection_reason }}
            </div>
            @endif

            {{-- KARTU UTAMA STEPPER AKTIVITAS --}}
            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-10">

                {{-- JUDUL AKTIVITAS --}}
                <div>
                    <h3 class="text-lg font-bold text-[#0c203b]">Aktivitas</h3>
                    <p class="text-xs text-slate-500">tunggu dan pantau verifikasi status pengembalian oleh yang bersangkutan.</p>
                </div>

                {{-- STEPPER 6 TAHAP (PERSIS FOTO 2, 3, 4, 5) --}}
                <div class="mt-8 border-b border-slate-100 pb-10">
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-6 text-center text-[11px]">
                        @php
                        $stepperStages = [
                        1 => ['name' => '1. Dikirin', 'time' => '15 Okt 07:15'],
                        2 => ['name' => '2. Terkirim & Diterima', 'time' => '15 Okt 08:30'],
                        3 => ['name' => '3. Diverifikasi', 'time' => '15 Okt 10:30'],
                        4 => ['name' => '4. Kesepakatan', 'time' => '15 Okt 12:30'],
                        5 => ['name' => '5. QR tiket Pengambilan', 'time' => '15 Okt 21:30'],
                        6 => ['name' => '6. Selesai', 'time' => 'Tuntas'],
                        ];
                        @endphp

                        @foreach ($stepperStages as $stepNum => $stepData)
                        <div class="flex flex-col items-center">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full {{ $claim->current_step >= $stepNum ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-400' }}">
                                @if ($claim->current_step >= $stepNum) ✓ @else {{ $stepNum }} @endif
                            </div>
                            <span class="mt-2 font-bold {{ $claim->current_step >= $stepNum ? 'text-slate-900' : 'text-slate-400' }}">
                                {{ $stepData['name'] }}
                            </span>
                            <span class="text-[9px] text-slate-400">{{ $stepData['time'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- KONTEN DINAMIS SESUAI TAHAP AKTIF --}}
                <div class="mt-8">

                    {{-- ======================================================= --}}
                    {{-- TAMPILAN JIKA STEP 2: BELUM DIVERIFIKASI (SESUAI FOTO 2) --}}
                    {{-- ======================================================= --}}
                    @if ($claim->current_step == 2 && !$claim->rejected_at)
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">3</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Status Verifikasi</h4>
                                <p class="text-[11px] text-slate-400">Orang yang menemukan akan mengonfirmasi apakah barang tersebut benar milik anda atau tidak</p>
                            </div>
                        </div>

                        <div class="pt-4">
                            <h3 class="text-xl font-bold text-[#0c203b]">Berkas Klaim Temuan {{ $claim->claim_code }}</h3>
                            <p class="text-2xl font-black text-[#0c203b] mt-1">Belum Diverifikasi</p>
                        </div>
                    </div>
                    @endif

                    {{-- ======================================================= --}}
                    {{-- TAMPILAN JIKA STEP 3: DIVERIFIKASI (SESUAI FOTO 3)        --}}
                    {{-- ======================================================= --}}
                    @if ($claim->current_step == 3 && !$claim->rejected_at)
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">4</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Lakukan Kesepakatan</h4>
                                <p class="text-[11px] text-slate-400">Hubungi nomor berikut, lalu buat kesepakatan terkait waktu, tanggal, jam, dan tempat untuk pengambilan barang</p>
                            </div>
                        </div>

                        <div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $claim->report->reporter_phone) }}?text=Halo,%20saya%20pemilik%20klaim%20{{ $claim->claim_code }}%20untuk%20{{ urlencode($claim->report->title) }}." target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-md hover:bg-emerald-700">
                                <span class="text-base">💬</span>
                                <span>Hubungi & Buat Kesepakatan</span>
                            </a>
                        </div>

                        {{-- Tombol Lanjut ke Step 4 --}}
                        <div class="border-t border-slate-100 pt-4">
                            <a href="{{ route('barang.klaim.simulasi', $claim->id) }}" class="text-xs font-bold text-blue-600 hover:underline">
                                Sudah selesai WhatsApp? Klik di sini untuk menginput hasil kesepakatan &rarr;
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- ======================================================= --}}
                    {{-- TAMPILAN JIKA STEP 4: FORM KESEPAKATAN COD (SESUAI FOTO 4) --}}
                    {{-- ======================================================= --}}
                    @if ($claim->current_step == 4 && !$claim->rejected_at)
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">4</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Lakukan Kesepakatan</h4>
                                <p class="text-[11px] text-slate-400">Hubungi nomor berikut, lalu buat kesepakatan terkait waktu, tanggal, jam, dan tempat untuk pengambilan barang</p>
                            </div>
                        </div>

                        <div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $claim->report->reporter_phone) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-md hover:bg-emerald-700">
                                <span class="text-base">💬</span>
                                <span>Hubungi & Buat Kesepakatan</span>
                            </a>
                        </div>

                        {{-- FORM INPUT KESEPAKATAN --}}
                        <div class="border-t border-slate-100 pt-6">
                            <h4 class="text-sm font-bold text-[#0c203b]">Apakah Kesepakatan Sudah Selesai?</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Masukkan Informasi Kesepakatan :</p>

                            <form method="POST" action="{{ route('barang.klaim.kesepakatan', $claim->id) }}" class="mt-4 space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-800">1. Lokasi Pengambilan Barang <span class="text-rose-500">*</span></label>
                                    <input type="text" name="pickup_location" required placeholder="Contoh: Kopi Kenangan Dr. Mansyur" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-800">2. Tanggal <span class="text-rose-500">*</span></label>
                                        <input type="date" name="pickup_date" required value="{{ date('Y-m-d') }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-800">2. Jam <span class="text-rose-500">*</span></label>
                                        <input type="time" name="pickup_time" required value="12:00" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
                                    </div>
                                </div>

                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700">
                                    <span>✔ Konfirmasi</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- ======================================================= --}}
                    {{-- TAMPILAN JIKA STEP 5: QR TIKET PENGAMBILAN (SESUAI FOTO 5) --}}
                    {{-- ======================================================= --}}
                    @if ($claim->current_step >= 5 && !$claim->rejected_at)
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">5</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Pengambilan</h4>
                                <p class="text-[11px] text-slate-400">Kamu akan diberikan Notifikasi pengingat dan jangan lupa lakukan Scan QR saat proses transaksi dilakukan</p>
                            </div>
                        </div>

                        {{-- TIGA KARTU INFORMASI PENGAMBILAN --}}
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 rounded-2xl bg-[#f0f4ff]/50 p-4 text-xs font-bold text-slate-800 border border-blue-50">
                                <span class="text-blue-600">📍</span>
                                <span>{{ $claim->pickup_location ?? 'SPBU Sebelah kopi Kenangan Dr.Mansyur' }}</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-2xl bg-[#f0f4ff]/50 p-4 text-xs font-bold text-slate-800 border border-blue-50">
                                <span class="text-blue-600">📅</span>
                                <span>{{ $claim->pickup_date ? $claim->pickup_date->translatedFormat('l, d F Y') : 'Kamis, 12 November 2026' }}</span>
                            </div>
                            <div class="flex items-center gap-3 rounded-2xl bg-[#f0f4ff]/50 p-4 text-xs font-bold text-slate-800 border border-blue-50">
                                <span class="text-blue-600">⏰</span>
                                <span>{{ $claim->pickup_time ?? '12.00' }}</span>
                            </div>
                        </div>

                        {{-- Script QR Code Generator Mandiri --}}
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

                        {{-- TOMBOL SCAN QR & MODAL --}}
                        <div class="pt-2"
                            x-data="{ 
                                qrOpen: false,
                                qrUrl: '{{ route('barang.klaim.konfirmasi', $claim->id) }}',
                                renderQR() {
                                    this.qrOpen = true;
                                    this.$nextTick(() => {
                                        const container = document.getElementById('local-qrcode');
                                        if (container) {
                                            container.innerHTML = '';
                                            if (typeof QRCode !== 'undefined') {
                                                new QRCode(container, {
                                                    text: this.qrUrl,
                                                    width: 176,
                                                    height: 176,
                                                    colorDark : '#0c203b',
                                                    colorLight : '#ffffff',
                                                    correctLevel : QRCode.CorrectLevel.H
                                                });
                                            } else {
                                                container.innerHTML = `<img src='https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(this.qrUrl)}' class='h-44 w-44 rounded-xl border border-slate-200 p-2' alt='QR Code'>`;
                                            }
                                        }
                                    });
                                }
                            }">

                            @if ($claim->current_step < 6)
                                <button type="button"
                                @click="renderQR()"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-xs font-bold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-700 transition-all">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>SCAN QR</span>
                                </button>
                                <p class="mt-2 text-[10px] font-bold text-rose-600">
                                    *QR HANYA DAPAT TAMPIL SEKALI, PASTIKAN ANDA BENAR BENAR SUDAH BERTEMU
                                </p>
                                @endif

                                {{-- POPUP MODAL QR CODE MANDIRI --}}
                                <div x-show="qrOpen"
                                    x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 transition-all">
                                    <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl animate-in fade-in zoom-in-95 duration-200"
                                        @click.outside="qrOpen = false">

                                        <h3 class="text-base font-bold text-[#0c203b]">QR Verifikasi Serah Terima</h3>
                                        <p class="text-xs text-slate-400 mt-1">Tunjukkan ke penemu barang saat bertemu</p>

                                        {{-- Kotak QR Code --}}
                                        <div class="my-6 flex justify-center">
                                            <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-inner">
                                                <div id="local-qrcode" class="flex h-44 w-44 items-center justify-center">
                                                    <span class="text-xs text-slate-400">Membuat QR...</span>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="font-mono text-sm font-bold tracking-wider text-blue-600">{{ $claim->claim_code }}</p>

                                        {{-- Tombol Langsung untuk Demo di Laptop --}}
                                        <div class="mt-4 rounded-xl bg-blue-50 p-2.5">
                                            <a href="{{ route('barang.klaim.konfirmasi', $claim->id) }}"
                                                class="text-[11px] font-bold text-blue-600 hover:underline">
                                                ⚡ Klik di Sini untuk Uji Coba Konfirmasi Selesai (Simulasi Scan)
                                            </a>
                                        </div>

                                        <button type="button"
                                            @click="qrOpen = false"
                                            class="mt-4 w-full rounded-xl bg-slate-900 py-3 text-xs font-bold text-white hover:bg-slate-800 transition-colors">
                                            Tutup QR
                                        </button>
                                    </div>
                                </div>
                        </div>
                    </div>
                    @endif

                    {{-- ======================================================= --}}
                    {{-- TAMPILAN JIKA STEP 6: SELESAI --}}
                    {{-- ======================================================= --}}
                    @if ($claim->current_step >= 6 && !$claim->rejected_at)
                    <div class="mt-8 rounded-3xl border border-emerald-100 bg-emerald-50/50 p-8 text-center shadow-sm">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 shadow-sm">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-extrabold text-[#0c203b]">Barang Berhasil Dikembalikan!</h3>
                        <p class="mt-1.5 text-xs text-slate-500 max-w-md mx-auto">
                            Proses verifikasi dan serah terima fisik barang telah tuntas dilakukan. Laporan ini resmi diselesaikan dan ditutup.
                        </p>
                        <div class="mt-6 flex flex-wrap justify-center gap-3">
                            <a href="{{ route('laporan.barang.index') }}" class="rounded-xl bg-[#0c203b] px-6 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition-colors">
                                Kembali ke Lapor Barang Hilang
                            </a>
                        </div>
                    </div>
                    @endif

                </div>

            </div>

        </div>
    </main>
</x-public-layout>