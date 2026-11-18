@php
    $selesai = in_array($report->status, ['resolved', 'done']);
    $field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20';

    $stepLabel = fn ($c) => $c->rejected_at 
        ? ['Ditolak', 'bg-rose-50 text-rose-700 border-rose-200', 'bg-rose-500'] 
        : match (true) {
            $c->current_step >= 6 => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500'],
            $c->current_step == 5 => ['Jadwal Pengambilan', 'bg-indigo-50 text-indigo-700 border-indigo-200', 'bg-indigo-500'],
            $c->current_step >= 3 => ['Diverifikasi', 'bg-blue-50 text-[#0066FF] border-blue-200', 'bg-[#0066FF]'],
            default               => ['Menunggu Verifikasi', 'bg-amber-50 text-amber-700 border-amber-200', 'bg-amber-500'],
        };
@endphp

<x-admin-layout title="{{ $report->ticket_code }} - Detail Barang">

    {{-- TOMBOL KEMBALI --}}
    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.barang.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Daftar Barang</span>
        </a>
    </div>

    {{-- NOTIFIKASI SUKSES / ERROR --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-700 shadow-sm">
            <div class="flex items-center gap-2 mb-1.5">
                <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Terjadi kendala pada formulir:</span>
            </div>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER KODE TIKET & BADGE STATUS --}}
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <h1 class="font-mono text-2xl font-black tracking-tight text-slate-900">{{ $report->ticket_code }}</h1>
        
        @if ($report->item_kind === 'penemuan')
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold text-[#0066FF]">
                <span class="h-2 w-2 rounded-full bg-[#0066FF]"></span>
                Barang Ditemukan
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600">
                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                Barang Dicari
            </span>
        @endif

        @if ($report->hidden_at)
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-rose-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-rose-800">
                <svg class="h-3.5 w-3.5 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
                Disembunyikan
            </span>
        @elseif ($selesai)
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Telah Dikembalikan
            </span>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-5">

        {{-- KOLOM KIRI: RINCIAN BARANG --}}
        <div class="space-y-6 lg:col-span-3">
            
            {{-- Detail Barang --}}
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">{{ $report->title }}</h2>
                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-400">
                    <span class="font-semibold text-slate-600">{{ $report->category?->name ?? 'Kategori Umum' }}</span>
                    <span>·</span>
                    <span>Kejadian {{ \Carbon\Carbon::parse($report->event_date)->translatedFormat('d F Y') }}{{ $report->event_time ? ', ' . $report->event_time : '' }}</span>
                    <span>·</span>
                    <span>Masuk {{ $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi Barang</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $report->description }}</p>
                </div>

                @if ($report->photo)
                    <div class="mt-5 border-t border-slate-100 pt-4">
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">Foto Barang</h3>
                        <a href="{{ asset('storage/' . $report->photo) }}" target="_blank" 
                           class="group relative block overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-xs">
                            <img src="{{ asset('storage/' . $report->photo) }}" alt="Foto barang" loading="lazy" class="max-h-80 w-full object-cover transition duration-300 group-hover:scale-102">
                            <div class="absolute inset-0 flex items-center justify-center bg-slate-900/30 opacity-0 transition-opacity group-hover:opacity-100">
                                <span class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-bold text-slate-800 shadow">Buka Gambar Penuh</span>
                            </div>
                        </a>
                    </div>
                @endif
            </section>

            {{-- Lokasi Kejadian --}}
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 text-slate-900">
                    <svg class="h-5 w-5 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-sm font-bold">Lokasi Kejadian</h3>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-700">{{ $report->address }}</p>

                @if ($report->latitude && $report->longitude)
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0066FF] hover:underline">
                        <span>Buka Penunjuk Arah Google Maps</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
                @endif
            </section>

            {{-- Identitas Pembuat Laporan --}}
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 text-slate-900">
                    <svg class="h-5 w-5 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <h3 class="text-sm font-bold">Data Pelapor</h3>
                </div>

                <div class="mt-3.5 rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                    <p class="text-xs font-bold text-slate-800">{{ $report->user?->name ?? $report->reporter_name }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $report->user?->email }}</p>
                </div>
                
                <div class="mt-3 flex items-start gap-2 text-[11px] text-slate-400">
                    <svg class="h-4 w-4 shrink-0 text-slate-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p>Kontak pelapor dilindungi privasinya dan hanya dipertukarkan langsung antar pengguna yang klaimnya telah terverifikasi.</p>
                </div>
            </section>
        </div>

        {{-- KOLOM KANAN: PANEL MODERASI & TANGGAPAN --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- PANEL MODERASI ADMIN --}}
            @if ($report->hidden_at)
                <section class="rounded-2xl border border-rose-200 bg-rose-50/60 p-6 shadow-sm">
                    <div class="flex items-center gap-2 text-rose-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <h3 class="text-sm font-bold">Laporan Disembunyikan</h3>
                    </div>
                    
                    <p class="mt-1 text-[11px] text-rose-500">Waktu tindakan: {{ $report->hidden_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
                    
                    <div class="mt-3 rounded-xl bg-white p-3 border border-rose-200 text-xs">
                        <span class="block font-bold text-slate-700">Alasan Penindakan:</span>
                        <p class="mt-1 text-slate-600 leading-relaxed">{{ $report->hidden_reason }}</p>
                    </div>

                    <form method="POST" action="{{ route('admin.barang.pulihkan', $report) }}" class="mt-4"
                        onsubmit="return confirm('Tampilkan kembali laporan ini ke publik?')">
                        @csrf
                        <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Pulihkan Kembali Laporan</span>
                        </button>
                    </form>
                    <p class="mt-2.5 text-[10px] text-slate-400 text-center">Catatan: Klaim yang otomatis tertolak saat laporan disembunyikan tidak akan aktif kembali.</p>
                </section>
            @else
                <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm" x-data="{ buka: false }">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Moderasi Konten</h3>
                    </div>

                    <p class="mt-2 text-xs text-slate-500">Turunkan laporan bila terbukti merupakan spam, memuat kata kasar, atau mempublikasikan data pribadi terlarang.</p>

                    <button type="button" @click="buka = !buka" 
                            class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50/70 py-2.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                        <span>Sembunyikan Laporan Ini</span>
                    </button>

                    <form x-show="buka" x-cloak method="POST" action="{{ route('admin.barang.sembunyikan', $report) }}" class="mt-4 space-y-3"
                        onsubmit="return confirm('Sembunyikan laporan ini? Seluruh klaim yang sedang berjalan akan otomatis dibatalkan.')">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700">Alasan Tindakan Moderasi <span class="text-rose-500">*</span></label>
                            <textarea name="hidden_reason" rows="3" required maxlength="500" placeholder="Tuliskan alasan penonaktifan laporan yang akan dibaca oleh pelapor..." class="{{ $field }}"></textarea>
                        </div>
                        <button class="w-full rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700 active:scale-95">
                            Konfirmasi Sembunyikan
                        </button>
                        <p class="text-[10px] text-slate-400 text-center">Laporan akan ditarik dari feed publik dan penelusuran peta, namun datanya tetap tersimpan aman.</p>
                    </form>
                </section>
            @endif

            {{-- DAFTAR TANGGAPAN / KLAIM DARI WARGA --}}
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-[#0066FF]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Tanggapan / Klaim Masuk</h3>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                        {{ $claims->count() }}
                    </span>
                </div>

                <div class="mt-3 divide-y divide-slate-100">
                    @forelse ($claims as $c)
                        @php 
                            [$lbl, $warnaBadge, $warnaDot] = $stepLabel($c); 
                        @endphp
                        <div class="flex items-start justify-between gap-3 py-3.5 text-xs">
                            <div class="min-w-0">
                                <p class="font-mono font-bold text-slate-900">{{ $c->claim_code }}</p>
                                <div class="mt-0.5 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <span class="font-medium text-slate-600 truncate">{{ $c->user?->name }}</span>
                                    <span>·</span>
                                    <span>{{ $c->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border px-2.5 py-1 text-[10px] font-bold {{ $warnaBadge }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $warnaDot }}"></span>
                                {{ $lbl }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="mt-2 text-xs font-semibold text-slate-600">Belum Ada Tanggapan</p>
                            <p class="text-[11px] text-slate-400">Belum ada warga yang mengajukan klaim untuk barang ini.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                    <p class="text-[11px] text-slate-400">Jawaban rahasia dan bukti foto hanya dibuka khusus antar pihak yang saling terhubung.</p>
                </div>
            </section>
        </div>
    </div>
</x-admin-layout>