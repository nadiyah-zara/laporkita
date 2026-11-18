@php
    $step = $claim->current_step;
    $waFinder = \App\Models\ItemClaim::waNumber($claim->claimant_phone);
    $field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20';
@endphp

<x-public-layout title="Tanggapan {{ $nomor }} - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5">
                <a href="{{ route('laporan.barang.saya.show', $report) }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Laporan</span>
                </a>
            </div>

            {{-- HEADER HALAMAN --}}
            <div class="mb-6 flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Tanggapan {{ $nomor }} · {{ $report->ticket_code }}
                    </h1>
                    <p class="text-xs text-slate-500">Proses pencocokan dan penyelesaian barang temuan antarwarga.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
                    <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-9">
                <div class="border-b border-slate-100 pb-5">
                    <h2 class="text-base font-bold text-slate-900">Aktivitas Verifikasi</h2>
                    <p class="text-xs text-slate-400">Pantau tahapan verifikasi berkas dan jadwal pengambilan barang.</p>
                </div>

                @include('barang.partials.stepper', ['stages' => $claim->stages('1. Tanggapan Diterima')])

                <div class="mt-8 space-y-6">

                    {{-- DITOLAK --}}
                    @if ($claim->rejected_at)
                        <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-5">
                            <p class="text-sm font-bold text-rose-700">Tanggapan ini telah ditolak</p>
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
                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">1</span>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Form Bukti Kepemilikan</h3>
                                        <p class="text-xs text-slate-400">Sebelum melihat bukti penemu, sebutkan ciri rahasia barang yang Anda ingat:</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('laporan.barang.tanggapan.berkas', [$report, $claim]) }}" enctype="multipart/form-data" class="mt-5 space-y-5">
                                    @csrf
                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-800">1. Identitas unik yang hanya Anda ketahui <span class="text-rose-500">*</span></label>
                                            <input type="text" name="owner_unique_feature" value="{{ old('owner_unique_feature') }}" required placeholder="Contoh: Ada goresan di sudut kanan" class="{{ $field }}">
                                            @error('owner_unique_feature') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-800">2. Warna lapisan bagian dalam <span class="text-rose-500">*</span></label>
                                            <input type="text" name="owner_inner_color" value="{{ old('owner_inner_color') }}" required placeholder="Contoh: Kain satin biru dongker" class="{{ $field }}">
                                            @error('owner_inner_color') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-800">3. Uraikan deskripsi mendalam <span class="text-rose-500">*</span></label>
                                        <textarea name="owner_full_detail" rows="3" required placeholder="Jelaskan secara rinci barang yang hilang..." class="{{ $field }}">{{ old('owner_full_detail') }}</textarea>
                                        @error('owner_full_detail') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5">
                                        <span class="rounded-md bg-[#0066FF] px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white">Wajib</span>
                                        <h4 class="mt-2 text-xs font-bold text-slate-800">Foto Lama Bersama Barang / Bukti Kepemilikan</h4>
                                        <input type="file" name="owner_proof_photo" accept="image/*,application/pdf" required
                                            @change="const f = $event.target.files[0]; preview = (f && f.type.startsWith('image/')) ? URL.createObjectURL(f) : null"
                                            class="mt-3 block w-full text-xs file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-[#0066FF]">
                                        <template x-if="preview">
                                            <img :src="preview" class="mt-3 h-32 w-auto rounded-xl border border-slate-200 object-cover" alt="Preview">
                                        </template>
                                        @error('owner_proof_photo') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                                        <button type="button" @click="tolak = !tolak" class="text-xs font-bold text-rose-600 hover:underline">Tolak Tanggapan Ini</button>
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-7 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">Kirim Berkas</button>
                                    </div>
                                </form>

                                <form x-show="tolak" x-cloak method="POST" action="{{ route('laporan.barang.tanggapan.tolak', [$report, $claim]) }}" class="mt-4 space-y-3">
                                    @csrf
                                    <input type="text" name="rejection_reason" maxlength="255" placeholder="Alasan penolakan (opsional)" class="{{ $field }}">
                                    <button type="submit" class="rounded-xl bg-rose-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Penolakan</button>
                                </form>
                            </div>

                        {{-- 2b. Sudah isi berkas --}}
                        @else
                            <div x-data="{ buka: false, tolak: false }">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">2</span>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Periksa Berkas Penemu</h3>
                                        <p class="text-xs text-slate-400">Cocokkan foto dan jawaban yang dikirim penemu dengan barang Anda.</p>
                                    </div>
                                </div>

                                <button type="button" @click="buka = !buka" 
                                        class="mt-4 inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50">
                                    <span x-text="buka ? 'Sembunyikan Berkas Penemu' : 'Buka Berkas Penemu'"></span>
                                </button>

                                <div x-show="buka" x-cloak class="mt-4">
                                    @include('barang.partials.berkas', [
                                        'feature' => $claim->secret_unique_feature,
                                        'color' => $claim->secret_inner_color,
                                        'detail' => $claim->secret_full_detail,
                                        'photo' => $claim->proof_photo,
                                        'photoLabel' => 'Foto barang yang ada pada penemu',
                                    ])
                                </div>

                                @if (!$claim->owner_verified_at)
                                    <div class="mt-5 flex flex-wrap items-center gap-4 text-xs font-bold border-t border-slate-100 pt-4">
                                        <form method="POST" action="{{ route('laporan.barang.tanggapan.verifikasi', [$report, $claim]) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Verifikasi Tanggapan Cocok</span>
                                            </button>
                                        </form>
                                        <button type="button" @click="tolak = !tolak" class="text-rose-600 hover:underline">Tolak Tanggapan</button>
                                    </div>

                                    <form x-show="tolak" x-cloak method="POST" action="{{ route('laporan.barang.tanggapan.tolak', [$report, $claim]) }}" class="mt-3 space-y-3">
                                        @csrf
                                        <input type="text" name="rejection_reason" maxlength="255" placeholder="Alasan penolakan (opsional)" class="{{ $field }}">
                                        <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Penolakan</button>
                                    </form>
                                @else
                                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800">
                                        Anda sudah memverifikasi tanggapan ini. Menunggu penemu memeriksa berkas Anda.
                                    </div>
                                @endif
                            </div>
                        @endif

                    {{-- STEP 3-4: KESEPAKATAN --}}
                    @elseif (in_array($step, [3, 4]))
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">4</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Buat Kesepakatan Pertemuan</h3>
                                    <p class="text-xs text-slate-400">Hubungi penemu via WhatsApp untuk menentukan jadwal dan lokasi pertemuan aman.</p>
                                </div>
                            </div>

                            <a href="https://wa.me/{{ $waFinder }}?text={{ urlencode('Halo, saya pemilik barang untuk laporan ' . $report->ticket_code . '.') }}" target="_blank"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.115.549 4.101 1.509 5.829l-1.602 5.856 6.004-1.574c1.655.905 3.551 1.42 5.568 1.42 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/>
                                </svg>
                                <span>Hubungi Penemu via WhatsApp</span>
                            </a>

                            <div class="mt-6 border-t border-slate-100 pt-6">
                                <h4 class="text-sm font-bold text-slate-900">Konfirmasi Kesepakatan</h4>
                                <p class="mt-0.5 text-xs text-slate-400">Masukkan informasi pertemuan yang telah disepakati:</p>
                                @include('barang.partials.form-kesepakatan')
                            </div>
                        </div>

                    {{-- STEP 5: PENGAMBILAN --}}
                    @elseif ($step == 5)
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-blue-50 text-xs font-bold text-[#0066FF]">5</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Serah Terima & Scan QR</h3>
                                    <p class="text-xs text-slate-400">Tunjukkan QR ke penemu saat bertemu langsung untuk menyelesaikan serah terima.</p>
                                </div>
                            </div>

                            <div class="mt-4">@include('barang.partials.info-pengambilan')</div>
                            <div class="mt-5"><x-qr-ticket :claim="$claim" /></div>

                            @if (app()->environment('local') || config('app.demo_mode'))
                                <a href="{{ route('barang.klaim.konfirmasi', $claim) }}" class="mt-4 inline-block rounded-xl border border-dashed border-blue-300 bg-white px-3 py-1.5 text-[11px] font-bold text-[#0066FF] hover:bg-blue-50">
                                    Simulasi Scan QR (Mode Pengujian Lokal)
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