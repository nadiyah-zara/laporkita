<x-public-layout title="Aktivitas Klaim - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5 flex items-center justify-between">
                <a href="{{ route('laporan.barang.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Laporan Barang</span>
                </a>
            </div>

            {{-- HEADER HALAMAN --}}
            <div class="mb-6 flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-mono text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Klaim Temuan {{ $claim->claim_code }}
                    </h1>
                    <p class="text-xs text-slate-500">Pantau proses verifikasi kepemilikan dan serah terima barang secara transparan.</p>
                </div>
            </div>

            {{-- BANNER DITOLAK --}}
            @if ($claim->rejected_at)
                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-xs font-semibold text-rose-700 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Klaim Anda ditolak oleh penemu. {{ $claim->rejection_reason }}</span>
                </div>
            @endif

            {{-- KARTU UTAMA STEPPER AKTIVITAS --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-9">

                {{-- Header Aktivitas --}}
                <div class="border-b border-slate-100 pb-5">
                    <h2 class="text-base font-bold text-slate-900">Alur Penyelesaian Klaim</h2>
                    <p class="mt-0.5 text-xs text-slate-400">Pantau setiap tahapan verifikasi hingga barang berhasil diserahterimakan.</p>
                </div>

                {{-- STEPPER 6 TAHAP --}}
                <div class="mt-6 border-b border-slate-100 pb-8">
                    <div class="grid grid-cols-2 gap-3 text-center sm:grid-cols-6">
                        @php
                            $stepperStages = [
                                1 => ['name' => '1. Dikirim', 'time' => '15 Okt 07:15'],
                                2 => ['name' => '2. Diterima', 'time' => '15 Okt 08:30'],
                                3 => ['name' => '3. Diverifikasi', 'time' => '15 Okt 10:30'],
                                4 => ['name' => '4. Kesepakatan', 'time' => '15 Okt 12:30'],
                                5 => ['name' => '5. Tiket QR', 'time' => '15 Okt 21:30'],
                                6 => ['name' => '6. Selesai', 'time' => 'Tuntas'],
                            ];
                        @endphp

                        @foreach ($stepperStages as $stepNum => $stepData)
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl text-xs font-bold transition-all {{ $claim->current_step >= $stepNum ? 'bg-[#0066FF] text-white shadow-xs' : 'bg-slate-100 text-slate-400' }}">
                                    @if ($claim->current_step >= $stepNum)
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        {{ $stepNum }}
                                    @endif
                                </div>
                                <span class="mt-2 text-xs font-bold {{ $claim->current_step >= $stepNum ? 'text-slate-800' : 'text-slate-400' }}">
                                    {{ $stepData['name'] }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $stepData['time'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- KONTEN DINAMIS BERDASARKAN STEP --}}
                <div class="mt-8">

                    {{-- STEP 2: BELUM DIVERIFIKASI --}}
                    @if ($claim->current_step == 2 && !$claim->rejected_at)
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">2</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Menunggu Tinjauan Verifikasi</h3>
                                    <p class="text-xs text-slate-400">Penemu sedang mencocokkan ciri khusus yang Anda kirimkan dengan barang fisik yang ditemukannya.</p>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-blue-100 bg-blue-50/40 p-5 mt-4">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#0066FF]">Status Pengajuan</span>
                                <h4 class="mt-1 font-mono text-base font-bold text-slate-900">Berkas Klaim {{ $claim->claim_code }}</h4>
                                <p class="mt-2 text-2xl font-black text-amber-600">Sedang Diverifikasi Penemu</p>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 3 & 4: KESEPAKATAN --}}
                    @if (in_array($claim->current_step, [3, 4]) && !$claim->rejected_at)
                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">4</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Buat Kesepakatan Serah Terima</h3>
                                    <p class="text-xs text-slate-400">Hubungi penemu barang, tentukan tempat pertemuan yang aman dan waktu yang disepakati.</p>
                                </div>
                            </div>

                            <div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $claim->report->reporter_phone) }}" target="_blank" 
                                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.115.549 4.101 1.509 5.829l-1.602 5.856 6.004-1.574c1.655.905 3.551 1.42 5.568 1.42 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/>
                                    </svg>
                                    <span>Hubungi Penemu via WhatsApp</span>
                                </a>
                            </div>

                            {{-- Form Kesepakatan --}}
                            <div class="border-t border-slate-100 pt-6">
                                <h4 class="text-sm font-bold text-slate-900">Formulir Rencana Serah Terima</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Isi detail lokasi dan jadwal setelah bersepakat dengan penemu:</p>

                                <form method="POST" action="{{ route('barang.klaim.kesepakatan', $claim->id) }}" class="mt-4 space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700">1. Titik / Lokasi Pertemuan <span class="text-rose-500">*</span></label>
                                        <input type="text" name="pickup_location" required placeholder="Contoh: Pos Satpam Gedung Z, Politeknik Negeri Medan" 
                                            class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700">2. Tanggal Pengambilan <span class="text-rose-500">*</span></label>
                                            <input type="date" name="pickup_date" required value="{{ date('Y-m-d') }}" 
                                                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700">3. Jam Janji Temu <span class="text-rose-500">*</span></label>
                                            <input type="time" name="pickup_time" required value="12:00" 
                                                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none">
                                        </div>
                                    </div>

                                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Konfirmasi Jadwal Temu</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 5: TIKET QR PENGAMBILAN --}}
                    @if ($claim->current_step >= 5 && !$claim->rejected_at)
                        <div class="space-y-6">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">5</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Tiket Digital Pengambilan</h3>
                                    <p class="text-xs text-slate-400">Tunjukkan kode QR kepada pihak yang bersangkutan saat bertemu langsung di lokasi.</p>
                                </div>
                            </div>

                            {{-- Kartu Info Jadwal --}}
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Lokasi Penyerahan</span>
                                    <p class="mt-1 text-xs font-bold text-slate-800">{{ $claim->pickup_location ?? 'Lokasi yang disepakati' }}</p>
                                </div>
                                <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Pertemuan</span>
                                    <p class="mt-1 text-xs font-bold text-slate-800">{{ $claim->pickup_date ? $claim->pickup_date->translatedFormat('l, d F Y') : 'Sesuai kesepakatan' }}</p>
                                </div>
                                <div class="rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Waktu Janji</span>
                                    <p class="mt-1 text-xs font-bold text-slate-800">{{ $claim->pickup_time ?? '12.00' }} WIB</p>
                                </div>
                            </div>

                            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

                            <x-qr-ticket :claim="$claim" />
                            @include('barang.partials.qr-reset', ['claim' => $claim])
                        </div>
                    @endif

                    {{-- STEP 6: SELESAI --}}
                    @if ($claim->current_step >= 6 && !$claim->rejected_at)
                        <div class="mt-6 rounded-3xl border border-emerald-200 bg-emerald-50/60 p-8 text-center shadow-sm">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 shadow-xs">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-xl font-bold text-slate-900">Barang Berhasil Dikembalikan!</h3>
                            <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                                Proses serah terima fisik barang telah terverifikasi secara sah melalui kode QR. Laporan kini telah ditutup secara resmi.
                            </p>
                            <div class="mt-6 flex justify-center">
                                <a href="{{ route('laporan.barang.index') }}" 
                                   class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                                    <span>Kembali ke Lapor Barang Hilang</span>
                                </a>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </main>
</x-public-layout>