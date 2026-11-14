@php
$step = $claim->current_step;
$waFinder = \App\Models\ItemClaim::waNumber($claim->claimant_phone);
$field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none';
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
                <p class="text-xs text-slate-500">Tunggu dan pantau verifikasi status pengembalian oleh yang bersangkutan.</p>

                @include('barang.partials.stepper', ['stages' => $claim->stages('1. Tanggapan Diterima')])

                <div class="mt-8 space-y-6">

                    {{-- DITOLAK --}}
                    @if ($claim->rejected_at)
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                        <p class="text-sm font-bold text-rose-700">Tanggapan ini ditolak</p>
                        @if ($claim->rejection_reason)
                        <p class="mt-1 text-xs text-rose-600">Alasan: {{ $claim->rejection_reason }}</p>
                        @endif
                    </div>

                    {{-- STEP 2 --}}
                    @elseif ($step == 2)

                    {{-- 2a. Belum isi berkas kepemilikan --}}
                    @if (!$claim->owner_submitted_at)
                    <div x-data="{ tolak: false, preview: null }">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">1</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Tanggapan</h4>
                                <p class="text-[11px] text-slate-400">Sebelum melihat tanggapan, isi form berikut dengan ini:</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('laporan.barang.tanggapan.berkas', [$report, $claim]) }}" enctype="multipart/form-data" class="mt-5 space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-bold text-slate-800">1. Apa identitas unik barang yang hanya anda ketahui <span class="text-rose-500">*</span></label>
                                    <p class="text-[10px] text-slate-400">Sebutkan ciri khas yang hanya ada pada barang anda</p>
                                    <input type="text" name="owner_unique_feature" value="{{ old('owner_unique_feature') }}" required placeholder="Contoh: ada goresan pada bagian ujung kanan dompet" class="{{ $field }}">
                                    @error('owner_unique_feature') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-800">2. Apa warna furing atau lapisan kain di balik ritsleting uang kertas? <span class="text-rose-500">*</span></label>
                                    <p class="text-[10px] text-slate-400">Warna tekstil interior bagian dalam dompet.</p>
                                    <input type="text" name="owner_inner_color" value="{{ old('owner_inner_color') }}" required placeholder="Contoh: Lapisan kain satin bermotif garis biru dongker" class="{{ $field }}">
                                    @error('owner_inner_color') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-800">3. Jelaskan secara perinci tentang barang <span class="text-rose-500">*</span></label>
                                <textarea name="owner_full_detail" rows="3" required placeholder="Jelaskan secara rinci sampai kami dapat percaya mengapa kamu adalah pemiliknya!" class="{{ $field }}">{{ old('owner_full_detail') }}</textarea>
                                @error('owner_full_detail') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-[#f0f4ff]/30 p-5">
                                <span class="rounded bg-blue-600 px-1.5 py-0.5 text-[9px] font-bold uppercase text-white">Wajib</span>
                                <h5 class="mt-2 text-xs font-bold text-[#0c203b]">Foto Lama Bersama Barang</h5>
                                <p class="text-[11px] text-slate-400">Foto saat Anda menggunakan barang tersebut di masa lalu.</p>
                                <input type="file" name="owner_proof_photo" accept="image/*,application/pdf" required
                                    @change="const f = $event.target.files[0]; preview = (f && f.type.startsWith('image/')) ? URL.createObjectURL(f) : null"
                                    class="mt-3 block w-full text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-blue-600">
                                <template x-if="preview">
                                    <img :src="preview" class="mt-3 h-32 w-auto rounded-xl border border-slate-200 object-cover" alt="Preview">
                                </template>
                                @error('owner_proof_photo') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                                <button type="button" @click="tolak = !tolak" class="text-xs font-bold text-rose-600 hover:underline">✕ Tolak Tanggapan Ini</button>
                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-8 py-3 text-xs font-bold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-700">Kirim Berkas</button>
                            </div>
                        </form>

                        <form x-show="tolak" x-cloak method="POST" action="{{ route('laporan.barang.tanggapan.tolak', [$report, $claim]) }}" class="mt-3 space-y-3">
                            @csrf
                            <input type="text" name="rejection_reason" maxlength="255" placeholder="Alasan penolakan (opsional)" class="{{ $field }}">
                            <button type="submit" class="rounded-xl bg-rose-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Penolakan</button>
                        </form>
                    </div>

                    {{-- 2b. Sudah isi berkas: bandingkan dengan tanggapan penemu --}}
                    @else
                    <div x-data="{ buka: false, tolak: false }">
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600">2</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#0c203b]">Tanggapan</h4>
                                <p class="text-[11px] text-slate-400">Bandingkan jawaban penemu dengan barang Anda.</p>
                            </div>
                        </div>

                        <button type="button" @click="buka = !buka" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700">
                            <span x-text="buka ? 'Tutup Tanggapan' : 'Buka Tanggapan'"></span>
                        </button>

                        <div x-show="buka" x-cloak>
                            @include('barang.partials.berkas', [
                            'feature' => $claim->secret_unique_feature,
                            'color' => $claim->secret_inner_color,
                            'detail' => $claim->secret_full_detail,
                            'photo' => $claim->proof_photo,
                            'photoLabel' => 'Foto barang yang ada pada penemu',
                            ])
                        </div>

                        @if (!$claim->owner_verified_at)
                        <div class="mt-5 flex flex-wrap items-center gap-5 text-xs font-bold">
                            <form method="POST" action="{{ route('laporan.barang.tanggapan.verifikasi', [$report, $claim]) }}">
                                @csrf
                                <button type="submit" class="text-emerald-600 hover:underline">✓ Verifikasi Tanggapan</button>
                            </form>
                            <button type="button" @click="tolak = !tolak" class="text-rose-600 hover:underline">✕ Tolak Tanggapan</button>
                        </div>

                        <form x-show="tolak" x-cloak method="POST" action="{{ route('laporan.barang.tanggapan.tolak', [$report, $claim]) }}" class="mt-3 space-y-3">
                            @csrf
                            <input type="text" name="rejection_reason" maxlength="255" placeholder="Alasan penolakan (opsional)" class="{{ $field }}">
                            <button type="submit" class="rounded-xl bg-rose-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Penolakan</button>
                        </form>
                        @else
                        <p class="mt-5 rounded-xl bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                            ✓ Anda sudah memverifikasi tanggapan ini. Menunggu penemu memeriksa berkas Anda.
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
                                <h4 class="text-sm font-bold text-[#0c203b]">Lakukan Kesepakatan</h4>
                                <p class="text-[11px] text-slate-400">Hubungi penemu, lalu buat kesepakatan terkait waktu, tanggal, jam, dan tempat pengambilan barang.</p>
                            </div>
                        </div>

                        <a href="https://wa.me/{{ $waFinder }}?text={{ urlencode('Halo, saya pemilik barang untuk laporan ' . $report->ticket_code . '.') }}" target="_blank"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-md hover:bg-emerald-700">
                            💬 Hubungi & Buat Kesepakatan
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
                                <p class="text-[11px] text-slate-400">Kamu akan diberi notifikasi pengingat. Tunjukkan QR ke penemu saat bertemu untuk menyelesaikan transaksi.</p>
                            </div>
                        </div>

                        <div class="mt-4">@include('barang.partials.info-pengambilan')</div>
                        <div class="mt-5"><x-qr-ticket :claim="$claim" /></div>

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