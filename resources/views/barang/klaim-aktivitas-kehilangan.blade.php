@php
    $step = $claim->current_step;
    $report = $claim->report;
    $wa = \App\Models\ItemClaim::waNumber($report->reporter_phone);
@endphp

<x-public-layout title="Klaim Temuan {{ $claim->claim_code }} - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            <a href="{{ route('laporan.barang.saya') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">← Kembali</a>

            <div class="mb-6 mt-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">Klaim Temuan {{ $claim->claim_code }}</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-10">
                <h3 class="text-lg font-bold text-[#0c203b]">Aktivitas</h3>
                <p class="text-xs text-slate-500">Tunggu dan pantau verifikasi status pengembalian oleh yang bersangkutan.</p>

                @include('barang.partials.stepper', ['stages' => $claim->stages()])

                <div class="mt-8 space-y-6">

                    {{-- DITOLAK --}}
                    @if ($claim->rejected_at)
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                            <p class="text-sm font-bold text-rose-700">Tanggapan ini ditolak</p>
                            @if ($claim->rejection_reason)
                                <p class="mt-1 text-xs text-rose-600">Alasan: {{ $claim->rejection_reason }}</p>
                            @endif
                        </div>

                    {{-- STEP 2: BALASAN --}}
                    @elseif ($step == 2)
                        @if (!$claim->owner_submitted_at)
                            <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5 text-xs text-slate-600">
                                <p class="font-bold text-[#0c203b]">Menunggu pemilik barang</p>
                                <p class="mt-1">Berkas Anda sudah terkirim. Pemilik perlu mengisi berkas kepemilikannya dulu. Anda akan mendapat notifikasi saat berkasnya masuk.</p>
                            </div>
                        @else
                            <div x-data="{ buka: false, tolak: false }">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">2</span>
                                    <div>
                                        <h4 class="text-sm font-bold text-[#0c203b]">Berkas Milik Pelapor</h4>
                                        <p class="text-[11px] text-slate-400">Sesuaikan berkas dengan barang yang ada pada Anda.</p>
                                    </div>
                                </div>

                                <button type="button" @click="buka = !buka" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700">
                                    <span x-text="buka ? 'Tutup Tanggapan' : 'Buka Tanggapan'"></span>
                                </button>

                                <div x-show="buka" x-cloak>
                                    @include('barang.partials.berkas', [
                                        'feature' => $claim->owner_unique_feature,
                                        'color' => $claim->owner_inner_color,
                                        'detail' => $claim->owner_full_detail,
                                        'photo' => $claim->owner_proof_photo,
                                        'photoLabel' => 'Foto lama pemilik bersama barang',
                                    ])
                                </div>

                                @if (!$claim->finder_verified_at)
                                    <div class="mt-5 flex flex-wrap items-center gap-5 text-xs font-bold">
                                        <form method="POST" action="{{ route('barang.klaim.verifikasi', $claim->id) }}">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:underline">✓ Verifikasi Tanggapan</button>
                                        </form>
                                        <button type="button" @click="tolak = !tolak" class="text-rose-600 hover:underline">✕ Tolak Tanggapan</button>
                                    </div>

                                    <form x-show="tolak" x-cloak method="POST" action="{{ route('barang.klaim.tolak', $claim->id) }}" class="mt-3 space-y-3">
                                        @csrf
                                        <input type="text" name="rejection_reason" maxlength="255" placeholder="Alasan penolakan (opsional)"
                                            class="block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs focus:border-blue-600 focus:bg-white focus:outline-none">
                                        <button type="submit" class="rounded-xl bg-rose-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Penolakan</button>
                                    </form>
                                @else
                                    <p class="mt-5 rounded-xl bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                                        ✓ Anda sudah memverifikasi berkas pelapor. Menunggu pemilik memverifikasi berkas Anda.
                                    </p>
                                @endif
                            </div>
                        @endif

                    {{-- STEP 3-4: KESEPAKATAN --}}
                    @elseif (in_array($step, [3, 4]))
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">4</span>
                                <div>
                                    <h4 class="text-sm font-bold text-[#0c203b]">Kesepakatan</h4>
                                    <p class="text-[11px] text-slate-400">Kamu akan dihubungi pemilik barang. Isi form berikut sesuai dengan yang sudah disepakati.</p>
                                </div>
                            </div>

                            <a href="https://wa.me/{{ $wa }}?text={{ urlencode('Halo, saya penemu barang untuk laporan ' . $report->ticket_code . '.') }}" target="_blank"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-md hover:bg-emerald-700">
                                💬 Hubungi Pemilik
                            </a>

                            <div class="mt-6 border-t border-slate-100 pt-6">
                                <h4 class="text-sm font-bold text-[#0c203b]">Apakah Kesepakatan Sudah Selesai?</h4>
                                <p class="mt-0.5 text-xs text-slate-400">Masukkan informasi kesepakatan:</p>
                                @include('barang.partials.form-kesepakatan')
                            </div>
                        </div>

                    {{-- STEP 5: PENGAMBILAN --}}
                    @elseif ($step == 5)
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">5</span>
                                <div>
                                    <h4 class="text-sm font-bold text-[#0c203b]">Pengambilan</h4>
                                    <p class="text-[11px] text-slate-400">Kamu akan diberi notifikasi pengingat. Jangan lupa scan QR milik pemilik saat transaksi dilakukan.</p>
                                </div>
                            </div>

                            <div class="mt-4">@include('barang.partials.info-pengambilan')</div>

                            <div class="mt-5">
                                <x-qr-scanner :expected="route('barang.klaim.konfirmasi', $claim)" />
                            </div>

                            @if (app()->environment('local'))
                                <a href="{{ route('barang.klaim.konfirmasi', $claim) }}" class="mt-4 inline-block rounded-xl border border-dashed border-blue-300 bg-white px-3 py-1.5 text-[11px] font-bold text-blue-600 hover:bg-blue-50">
                                    ⚡ Simulasi scan QR (hanya mode local)
                                </a>
                            @endif
                        </div>

                    {{-- STEP 6: SELESAI --}}
                    @else
                        @include('barang.partials.selesai', ['backUrl' => route('laporan.barang.saya'), 'backLabel' => 'Kembali ke Laporan Saya'])
                    @endif
                </div>
            </div>
        </div>
    </main>
</x-public-layout>