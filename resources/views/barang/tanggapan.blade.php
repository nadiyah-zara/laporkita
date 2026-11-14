@php
    $step = $claim->current_step;
    $stages = [
        1 => ['1. Dikirim', $claim->created_at->timezone('Asia/Jakarta')->format('d M H:i')],
        2 => ['2. Terkirim & Diterima', $claim->created_at->timezone('Asia/Jakarta')->format('d M H:i')],
        3 => ['3. Diverifikasi', null],
        4 => ['4. Kesepakatan', null],
        5 => ['5. QR Tiket Pengambilan', $claim->pickup_date ? $claim->pickup_date->format('d M') . ' ' . $claim->pickup_time : null],
        6 => ['6. Selesai', null],
    ];
    $isPdf = $claim->proof_photo && str_ends_with(strtolower($claim->proof_photo), '.pdf');
@endphp

<x-public-layout title="Tanggapan {{ $nomor }} - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            <a href="{{ route('laporan.barang.saya.show', $report) }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">← Kembali</a>

            <div class="mb-6 mt-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">Tanggapan {{ $nomor }} {{ $report->ticket_code }}</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-10">
                <h3 class="text-lg font-bold text-[#0c203b]">Aktivitas</h3>
                <p class="text-xs text-slate-500">Periksa jawaban pengklaim, lalu tentukan apakah barang ini benar miliknya.</p>

                {{-- STEPPER --}}
                <div class="mt-8 border-b border-slate-100 pb-10">
                    <div class="grid grid-cols-2 gap-3 text-center text-[11px] sm:grid-cols-6">
                        @foreach ($stages as $n => [$name, $time])
                            @php $done = !$claim->rejected_at && $step >= $n; @endphp
                            <div class="flex flex-col items-center">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full {{ $done ? 'bg-blue-600 font-bold text-white' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $done ? '✓' : $n }}
                                </div>
                                <span class="mt-2 font-bold {{ $done ? 'text-slate-900' : 'text-slate-400' }}">{{ $name }}</span>
                                <span class="text-[9px] text-slate-400">{{ $time }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 space-y-6">

                    {{-- DITOLAK --}}
                    @if ($claim->rejected_at)
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                            <p class="text-sm font-bold text-rose-700">Tanggapan ini ditolak</p>
                            @if ($claim->rejection_reason)
                                <p class="mt-1 text-xs text-rose-600">Alasan: {{ $claim->rejection_reason }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- DATA PENGKLAIM (selalu tampil sebagai bahan tinjauan) --}}
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">3</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Tanggapan</h4>
                                <p class="text-[11px] text-slate-400">Bandingkan jawaban di bawah dengan barang yang Anda temukan.</p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3 text-xs">
                            <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
                                <p class="font-bold text-slate-500">Identitas unik yang hanya diketahui pemilik</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $claim->secret_unique_feature }}</p>
                            </div>
                            <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
                                <p class="font-bold text-slate-500">Warna furing / lapisan dalam</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $claim->secret_inner_color }}</p>
                            </div>
                            <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
                                <p class="font-bold text-slate-500">Penjelasan rinci</p>
                                <p class="mt-1 whitespace-pre-line font-semibold text-slate-900">{{ $claim->secret_full_detail }}</p>
                            </div>
                            @if ($claim->proof_photo)
                                <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
                                    <p class="font-bold text-slate-500">Bukti foto lama bersama barang</p>
                                    @if ($isPdf)
                                        <a href="{{ asset('storage/' . $claim->proof_photo) }}" target="_blank" class="mt-2 inline-block font-bold text-blue-600 hover:underline">Buka berkas PDF</a>
                                    @else
                                        <a href="{{ asset('storage/' . $claim->proof_photo) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $claim->proof_photo) }}" class="mt-2 h-48 w-auto rounded-xl border border-slate-200 object-cover" alt="Bukti">
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 2: KEPUTUSAN --}}
                    @if ($step == 2 && !$claim->rejected_at)
                        <div class="border-t border-slate-100 pt-6" x-data="{ tolak: false }">
                            <h4 class="text-sm font-bold text-[#0c203b]">Apakah barang ini milik pengklaim?</h4>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('laporan.barang.tanggapan.verifikasi', [$report, $claim]) }}">
                                    @csrf
                                    <button class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700">✔ Ya, Verifikasi</button>
                                </form>
                                <button type="button" @click="tolak = !tolak" class="rounded-xl border border-rose-200 bg-rose-50 px-6 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-100">✕ Tolak</button>
                            </div>

                            <form x-show="tolak" x-cloak method="POST" action="{{ route('laporan.barang.tanggapan.tolak', [$report, $claim]) }}" class="mt-4 space-y-3">
                                @csrf
                                <input type="text" name="rejection_reason" maxlength="255" placeholder="Alasan penolakan (opsional)"
                                    class="block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs focus:border-blue-600 focus:bg-white focus:outline-none">
                                <button class="rounded-xl bg-rose-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Penolakan</button>
                            </form>

                            <p class="mt-3 text-[11px] font-semibold text-rose-600">*Pastikan jawaban benar-benar sesuai sebelum memverifikasi.</p>
                        </div>
                    @endif

                    {{-- STEP 3-4: MENUNGGU KESEPAKATAN --}}
                    @if (in_array($step, [3, 4]) && !$claim->rejected_at)
                        <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5 text-xs text-slate-600">
                            <p class="font-bold text-[#0c203b]">Menunggu kesepakatan</p>
                            <p class="mt-1">Pengklaim akan menghubungi WhatsApp Anda untuk menentukan lokasi, tanggal, dan jam pengambilan.</p>
                        </div>
                    @endif

                    {{-- STEP 5: JADWAL PENGAMBILAN --}}
                    @if ($step == 5 && !$claim->rejected_at)
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">5</span>
                                <div>
                                    <h4 class="text-sm font-bold text-[#0c203b]">Pengambilan</h4>
                                    <p class="text-[11px] text-slate-400">Anda akan mendapat notifikasi pengingat. Scan QR milik pengklaim saat bertemu untuk menyelesaikan transaksi.</p>
                                </div>
                            </div>

                            <div class="mt-4 space-y-3 text-xs font-bold text-slate-800">
                                <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">{{ $claim->pickup_location }}</div>
                                <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">{{ $claim->pickup_date?->translatedFormat('l, d F Y') }}</div>
                                <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">{{ $claim->pickup_time }}</div>
                            </div>

                            @if (app()->environment('local'))
                                <a href="{{ route('barang.klaim.konfirmasi', $claim) }}" class="mt-4 inline-block rounded-xl border border-dashed border-blue-300 bg-white px-3 py-1.5 text-[11px] font-bold text-blue-600 hover:bg-blue-50">
                                    Simulasi scan QR (hanya mode local)
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- STEP 6: SELESAI --}}
                    @if ($step >= 6 && !$claim->rejected_at)
                        <div class="rounded-3xl border border-emerald-100 bg-emerald-50/50 p-8 text-center">
                            <h3 class="text-xl font-extrabold text-[#0c203b]">Barang Berhasil Dikembalikan!</h3>
                            <p class="mx-auto mt-1.5 max-w-md text-xs text-slate-500">Terima kasih sudah membantu. Laporan ini resmi diselesaikan dan ditutup.</p>
                            <a href="{{ route('laporan.barang.saya') }}" class="mt-5 inline-block rounded-xl bg-[#0c203b] px-6 py-2.5 text-xs font-bold text-white hover:bg-slate-800">Kembali ke Laporan Saya</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</x-public-layout>