@php
    $tabs = [
        ''            => ['Semua', $stats['total'] ?? 0],
        'in_progress' => ['Sedang Dikerjakan', $stats['in_progress'] ?? 0],
        'pending'     => ['Belum Diverifikasi', $stats['pending'] ?? 0],
        'verified'    => ['Diverifikasi', $stats['verified'] ?? 0],
        'resolved'    => ['Selesai', ($stats['resolved'] ?? 0) + ($stats['done'] ?? 0)],
    ];
    $aktif = request('status', '');
    $url = fn ($p) => $p ? asset('storage/' . $p) : null;
@endphp

<x-public-layout title="Laporan Saya - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-4">
                <a href="{{ route('laporan.infrastruktur.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>

            {{-- HEADER HALAMAN DENGAN AKSEN BIRU --}}
            <div class="mb-6 flex items-center gap-2.5">
                <span class="h-8 w-1.5 rounded-full bg-[#0066FF]"></span>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">Laporan Saya</h1>
            </div>

            {{-- ALERT SUKSES --}}
            @if (session('success'))
            <div class="mb-5 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
                <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            {{-- BAR PENCARIAN & FILTER (SESUAI DESAIN) --}}
            <div class="space-y-3">
                {{-- Baris 1: Search & Filter Kategori/Urutan --}}
                <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                    {{-- Input Pencarian --}}
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari Laporan"
                            class="w-full rounded-xl border border-slate-200/80 bg-[#f1f5f9] py-2.5 pl-10 pr-4 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
                    </div>

                    {{-- Dropdown Kategori --}}
                    <div class="relative shrink-0">
                        <button type="button" class="inline-flex w-full items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-[#f1f5f9] px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-200/70">
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span>Semua Kategori</span>
                            </div>
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    {{-- Dropdown Urutan --}}
                    <div class="relative shrink-0">
                        <button type="button" class="inline-flex w-full items-center justify-between gap-3 rounded-xl border border-slate-200/80 bg-[#f1f5f9] px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-200/70">
                            <div class="flex items-center gap-2">
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                </svg>
                                <span>Paling Terbaru</span>
                            </div>
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Baris 2: Filter Tabs & Real-time Sync Indicator --}}
                <div class="flex flex-col justify-between gap-3 pt-1 sm:flex-row sm:items-center">
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($tabs as $key => [$label, $jumlah])
                        @php
                            $isActive = ((string) $aktif === (string) $key);
                        @endphp
                        <a href="{{ $key === '' ? route('laporan.saya') : route('laporan.saya', ['status' => $key]) }}"
                            class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-xs font-semibold transition-all {{ $isActive ? 'bg-[#0f172a] text-white shadow-sm' : 'border border-blue-200/80 bg-blue-50/50 text-[#0066FF] hover:bg-blue-100/60' }}">
                            @if (!$isActive && $key !== '')
                            <span class="h-1.5 w-1.5 rounded-full bg-[#0066FF]"></span>
                            @endif
                            <span>{{ $label }}</span>
                            <span class="font-bold">{{ $jumlah }}</span>
                        </a>
                        @endforeach
                    </div>

                    {{-- Sinkronisasi Real-time --}}
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Sinkronisasi Terakhir: Baru saja</span>
                    </div>
                </div>
            </div>

            {{-- DAFTAR KARTU LAPORAN --}}
            <div class="mt-6 space-y-6">
                @forelse ($reports as $report)
                @php
                    $selesai = in_array($report->status, ['done', 'resolved']);
                    $ditolak = $report->status === 'rejected';
                    $step = $selesai ? 6 : (int) ($report->current_step ?? ($report->status === 'in_progress' ? 5 : ($report->status === 'verified' ? 3 : 1)));
                    $persen = $selesai ? 100 : (int) ($report->progress_percentage ?? ($report->status === 'in_progress' ? 57 : 10));

                    // Foto Awal Warga & Foto Akhir
                    $fotoAwal = $url(is_array($report->photos) && count($report->photos) ? $report->photos[0] : $report->photo);
                    $fotoAkhir = $url($report->photo_after);

                    // Ambil foto worklog terbaru dari tabel logs (jika ada petugas upload foto)
                    $logFotoTerbaru = $report->logs?->filter(fn($l) => !empty($l->photo))->last();
                    $fotoPetugas = $logFotoTerbaru ? $url($logFotoTerbaru->photo) : null;

                    // Waktu per tahapan untuk stepper
                    $logWaktu = function ($n) use ($report) {
                        $l = $report->logs?->firstWhere('step', $n);
                        return $l?->created_at ? $l->created_at->timezone('Asia/Jakarta')->translatedFormat('d M H:i') : null;
                    };

                    $steps = [
                        1 => ['1. Dilaporkan', $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d M H:i')],
                        2 => ['2. Survey Oleh Tim', $logWaktu(2) ?? ($step >= 2 ? $report->created_at->addHours(2)->translatedFormat('d M H:i') : null)],
                        3 => ['3. Diverifikasi', $logWaktu(3) ?? ($step >= 3 ? $report->created_at->addHours(4)->translatedFormat('d M H:i') : null)],
                        4 => ['4. Persiapan', $logWaktu(4) ?? ($step >= 4 ? $report->created_at->addHours(6)->translatedFormat('d M H:i') : null)],
                        5 => ['5. Pengerjaan', $selesai ? ($logWaktu(5) ?? 'Selesai') : ($step >= 5 ? 'Hari ini ' . $report->updated_at->timezone('Asia/Jakarta')->format('H:i') : null)],
                        6 => ['6. Selesai', $selesai ? $report->updated_at->timezone('Asia/Jakarta')->translatedFormat('d M H:i') : 'Estimasi 18:00'],
                    ];

                    $badgeStatusLabel = match($report->status) {
                        'pending'     => ($step == 2 ? 'Sedang Disurvey Tim' : 'Menunggu Verifikasi & Jadwal'),
                        'verified'    => 'Diverifikasi & Siap Dikerjakan',
                        'in_progress' => ($step == 4 ? 'Tahap Persiapan Lapangan' : "Sedang Dikerjakan ({$persen}% Selesai)"),
                        'done', 'resolved' => 'Selesai',
                        'rejected'    => 'Ditolak',
                        default       => $report->status_label ?? 'Diproses',
                    };
                @endphp

                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm transition hover:shadow-md sm:p-7">

                    {{-- 1. HEADER KARTU --}}
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-base font-black tracking-tight text-slate-900">
                                    {{ str_starts_with($report->ticket_code, '#') ? $report->ticket_code : '#' . $report->ticket_code }}
                                </span>
                                <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-[#0066FF]">
                                    {{ $report->category->name ?? 'Infrastruktur' }}
                                </span>
                            </div>
                            <div class="mt-1.5">
                                @if ($report->urgency == 'kritis')
                                <span class="inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-600">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Prioritas Kritis
                                </span>
                                @else
                                <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                                    Prioritas Standar
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50/70 px-3 py-1 text-xs font-bold text-[#0066FF]">
                                <span class="h-1.5 w-1.5 rounded-full {{ $selesai ? 'bg-emerald-500' : 'bg-[#0066FF]' }}"></span>
                                {{ $badgeStatusLabel }}
                            </span>
                            <p class="mt-1 text-[11px] text-slate-400">
                                @if ($selesai)
                                Diselesaikan: {{ $report->updated_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB ({{ $report->updated_at->diffForHumans() }})
                                @else
                                Diajukan: {{ $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB ({{ $report->created_at->diffForHumans() }})
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- 2. JUDUL & LOKASI --}}
                    <div class="mt-4">
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">{{ $report->title }}</h2>
                        <div class="mt-1 flex flex-wrap items-center gap-x-2 text-xs text-slate-500">
                            <div class="flex items-center gap-1 font-medium text-slate-700">
                                <svg class="h-4 w-4 shrink-0 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $report->address }}</span>
                            </div>
                            @if ($report->latitude && $report->longitude)
                            <span>•</span>
                            <span class="font-mono text-slate-400">Koordinat: ({{ $report->latitude }}, {{ $report->longitude }})</span>
                            @endif
                        </div>
                    </div>

                    {{-- 3. BODY KARTU: TAMPILAN BERDASARKAN STATUS --}}

                    {{-- KONDISI A: JIKA STATUS SELESAI (TAHAP 6) -> TAMPILKAN BEFORE & AFTER + RINCIAN LAPANGAN --}}
                    @if ($selesai)
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Foto Sebelum (Dari Warga) --}}
                        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                            <img src="{{ $fotoAwal ?? 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=800' }}"
                                alt="Kondisi Sebelum" loading="lazy" class="h-60 w-full object-cover transition duration-300 group-hover:scale-102">
                            <div class="absolute left-3 top-3">
                                <span class="rounded-lg bg-slate-900/85 px-2.5 py-1 text-[11px] font-bold text-white shadow-xs backdrop-blur-xs">
                                    Kondisi Sebelum
                                </span>
                            </div>
                            <div class="absolute bottom-3 left-3 text-[11px] font-semibold text-white drop-shadow-md">
                                {{ $report->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>

                        {{-- Foto Hasil Perbaikan (Setelah Selesai) --}}
                        <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                            <img src="{{ $fotoAkhir ?? ($fotoAwal ?? 'https://images.unsplash.com/photo-1541888946425-d0fbb18f15f6?w=800') }}"
                                alt="Hasil Perbaikan" loading="lazy" class="h-60 w-full object-cover transition duration-300 group-hover:scale-102">
                            <div class="absolute left-3 top-3">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-[#0066FF] px-2.5 py-1 text-[11px] font-bold text-white shadow-xs">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Hasil Perbaikan
                                </span>
                            </div>
                            <div class="absolute bottom-3 left-3 text-[11px] font-semibold text-white drop-shadow-md">
                                {{ $report->updated_at->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </div>

                    {{-- Rincian Hasil Pengerjaan Lapangan --}}
                    <div class="mt-4 rounded-2xl border border-blue-100 bg-[#f0f7ff]/60 p-4 text-xs leading-relaxed text-slate-700">
                        <p class="font-bold text-slate-900">Rincian Hasil Pengerjaan Lapangan :</p>
                        <p class="mt-1 text-slate-600">
                            {{ $report->completion_notes ?: 'Pekerjaan perbaikan infrastruktur telah rampung diselesaikan oleh tim teknis lapangan sesuai standar spesifikasi dinas, dan telah diverifikasi oleh pengawas serta warga pelapor.' }}
                        </p>
                    </div>

                    {{-- KONDISI B: JIKA SEDANG BERJALAN (TAHAP 1 S/D 5) -> DINAMIS SESUAI TAHAPAN --}}
                    @elseif (!$ditolak)
                    @php
                        // Logika dinamis teks & profil sesuai tahapan yang sedang aktif
                        if ($step == 1) {
                            // TAHAP 1: BARU DILAPORKAN WARGA
                            $fotoTampil = $fotoAwal;
                            $badgeFoto = 'Foto Laporan Awal Warga';
                            $captionFoto = 'Diunggah oleh pelapor • ' . $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB';
                            $tipeProfil = 'Warga Pelapor';
                            $namaProfil = $report->reporter_name ?? 'Warga Pelapor';
                            $teleponProfil = $report->reporter_phone ?? null;
                            $judulCatatan = 'Deskripsi Keluhan Pelapor (' . ($report->reporter_name ?? 'Warga') . ') :';
                            $isiCatatan = $report->description;
                            $subInfo = 'Menunggu peninjauan awal & jadwal survey oleh tim dinas terkait.';
                        } elseif ($step == 2) {
                            // TAHAP 2: SURVEY TIM LAPANGAN
                            $fotoTampil = $fotoPetugas ?? $fotoAwal;
                            $badgeFoto = 'Dokumentasi Survey Lapangan';
                            $captionFoto = ($logFotoTerbaru?->note ?? 'Pemeriksaan fisik kondisi kerusakan di lokasi') . ' • ' . $report->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB';
                            $tipeProfil = 'Tim Survey Lapangan';
                            $namaProfil = $report->supervisor_name ?? 'Tim Reaksi Cepat PU';
                            $teleponProfil = $report->supervisor_phone ?? null;
                            $judulCatatan = 'Catatan Hasil Survey Lapangan :';
                            $isiCatatan = $report->supervisor_notes ?: ($logFotoTerbaru?->note ?: 'Tim teknis telah melakukan peninjauan lokasi kerusakan untuk mengukur volume dan menentukan estimasi kebutuhan perbaikan.');
                            $subInfo = 'Hasil survey lapangan sedang dalam tahap verifikasi kelayakan dinas.';
                        } elseif ($step == 3) {
                            // TAHAP 3: DIVERIFIKASI DINAS
                            $fotoTampil = $fotoPetugas ?? $fotoAwal;
                            $badgeFoto = 'Laporan Terverifikasi Dinas';
                            $captionFoto = 'Kelayakan penanganan telah diverifikasi & disetujui dinas';
                            $tipeProfil = 'Verifikator Dinas';
                            $namaProfil = 'Dinas Pekerjaan Umum';
                            $teleponProfil = null;
                            $judulCatatan = 'Catatan Verifikasi & Kelayakan :';
                            $isiCatatan = 'Laporan telah divalidasi dan dinyatakan memenuhi syarat penanganan infrastruktur publik. Sedang dalam proses penerbitan Surat Perintah Kerja (SPK) dan penjadwalan.';
                            $subInfo = 'Menunggu penugasan pengawas dan penjadwalan alat/material.';
                        } elseif ($step == 4) {
                            // TAHAP 4: PERSIAPAN PENGERJAAN
                            $fotoTampil = $fotoPetugas ?? $fotoAwal;
                            $badgeFoto = 'Tahap Persiapan & Logistik';
                            $captionFoto = 'Penyiapan material, alat berat & mobilisasi ke lokasi';
                            $tipeProfil = 'Pengawas Lapangan';
                            $namaProfil = $report->supervisor_name ?? 'Pengawas Lapangan PU';
                            $teleponProfil = $report->supervisor_phone ?? null;
                            $jadwalTeks = $report->scheduled_date ? \Carbon\Carbon::parse($report->scheduled_date)->translatedFormat('d M Y') : 'Segera';
                            $judulCatatan = 'Catatan Persiapan (Target: ' . $jadwalTeks . ') :';
                            $isiCatatan = $report->supervisor_notes ?: 'Tim teknis sedang menyiapkan bahan material dan armada alat kerja ke lokasi. Pengerjaan fisik dijadwalkan dimulai sesuai target.';
                            $subInfo = 'Jadwal dan pengawas pengerjaan telah ditetapkan.';
                        } else {
                            // TAHAP 5: PENGERJAAN SEDANG BERLANGSUNG
                            $fotoTampil = $fotoPetugas ?? $fotoAwal;
                            $badgeFoto = 'Update Lapangan Hari Ini';
                            $captionFoto = ($logFotoTerbaru?->note ?? ($report->supervisor_notes ?? 'Pengerjaan lapangan sedang berlangsung')) . ' • Hari ini ' . $report->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB';
                            $tipeProfil = 'Pengawas Lapangan';
                            $namaProfil = $report->supervisor_name ?? 'Ir. Bambang Trihatmojo';
                            $teleponProfil = $report->supervisor_phone ?? null;
                            $judulCatatan = 'Catatan Pengawas Lapangan (' . $namaProfil . ') :';
                            $isiCatatan = $report->supervisor_notes ?: 'Pekerjaan perbaikan fisik di lapangan sedang dikerjakan sesuai standar teknis dinas.';
                            $subInfo = 'Tahap pengerjaan fisik sedang berjalan (' . $persen . '% rampung).';
                        }
                    @endphp

                    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-12">

                        {{-- Kolom Kiri: Foto Sesuai Tahapan --}}
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 md:col-span-7">
                            <img src="{{ $fotoTampil }}" alt="{{ $badgeFoto }}" class="h-64 w-full object-cover">

                            {{-- Badge Atas Foto --}}
                            <div class="absolute left-3.5 top-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full {{ $step == 1 ? 'bg-slate-900/85 text-white' : 'bg-[#0066FF] text-white' }} px-3 py-1 text-[11px] font-bold shadow-md">
                                    @if ($step == 1)
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @else
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    @endif
                                    {{ $badgeFoto }}
                                </span>
                            </div>

                            {{-- Caption Bawah Foto --}}
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-3.5 pt-6 text-[11px] font-medium text-white">
                                {{ $captionFoto }}
                            </div>
                        </div>

                        {{-- Kolom Kanan: Info Profil & Catatan Sesuai Tahapan --}}
                        <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-[#f8fafc] p-4.5 md:col-span-5">
                            <div>
                                {{-- Profil Header --}}
                                <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $step == 1 ? 'bg-slate-200 text-slate-700' : 'bg-blue-100/70 text-[#0066FF]' }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-medium text-slate-400">{{ $tipeProfil }}</span>
                                            <h4 class="text-xs font-bold text-slate-900">{{ $namaProfil }}</h4>
                                        </div>
                                    </div>

                                    {{-- Tombol Kontak Telepon & Pesan (Jika Ada Nomor) --}}
                                    @if ($teleponProfil)
                                    <div class="flex items-center gap-1.5">
                                        <a href="tel:{{ $teleponProfil }}" title="Hubungi Telepon"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-[#0066FF] hover:text-[#0066FF]">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </a>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $teleponProfil) }}" target="_blank" title="Kirim Pesan WhatsApp"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:border-emerald-500 hover:text-emerald-600">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                {{-- Catatan Dinamis --}}
                                <div class="mt-3">
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                        <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $step == 1 ? $report->created_at->diffForHumans() : $report->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="mt-1 text-xs font-bold text-slate-800">
                                        {{ $judulCatatan }}
                                    </p>
                                    <p class="mt-1 text-xs italic leading-relaxed text-slate-600">
                                        "{{ $isiCatatan }}"
                                    </p>
                                </div>
                            </div>

                            {{-- Sub-info Status di Bawah Kotak --}}
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 text-[10px] text-slate-400 flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full {{ $step == 1 ? 'bg-amber-400' : 'bg-blue-500' }}"></span>
                                <span>{{ $subInfo }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 4. STEPPER 6 TAHAP PENGERJAAN LAPANGAN --}}
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <div class="mb-4 flex items-center justify-between">
                            <span class="text-[11px] font-bold tracking-wider text-slate-700 uppercase">Tahapan Pengerjaan Lapangan</span>
                            <span class="text-xs font-bold text-[#0066FF]">
                                {{ $step }} dari 6 Tahap Selesai
                            </span>
                        </div>

                        {{-- Progress Tracker Horizontal --}}
                        <div class="relative">
                            {{-- Garis Latar Belakang --}}
                            <div class="absolute top-3.5 left-5 right-5 -z-0 h-0.5 bg-slate-200"></div>
                            {{-- Garis Aktif --}}
                            <div class="absolute top-3.5 left-5 -z-0 h-0.5 bg-[#0066FF] transition-all duration-500"
                                style="width: {{ max(0, min(100, (($step - 1) / 5) * 100)) }}%"></div>

                            <div class="grid grid-cols-6 gap-1 text-center">
                                @foreach ($steps as $n => [$nama, $waktu])
                                @php
                                    $isPassed = ($step > $n);
                                    $isCurrent = ($step == $n);
                                @endphp
                                <div class="flex flex-col items-center">
                                    {{-- Circle Node --}}
                                    <div class="relative z-10 flex h-7 w-7 items-center justify-center rounded-full text-[11px] font-bold transition-all
                                                    {{ $isPassed || $isCurrent ? 'bg-[#0066FF] text-white shadow-xs' : 'border-2 border-slate-300 bg-white text-slate-400' }}">
                                        @if ($isPassed || $isCurrent)
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        @else
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                        @endif
                                    </div>

                                    {{-- Label & Waktu --}}
                                    <span class="mt-1.5 text-[11px] font-bold leading-tight {{ $isPassed || $isCurrent ? 'text-slate-900' : 'text-slate-400' }}">
                                        {{ $nama }}
                                    </span>
                                    <span class="mt-0.5 text-[10px] {{ $isCurrent ? 'font-bold text-[#0066FF]' : 'text-slate-400' }}">
                                        {{ $waktu ?? '-' }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- KONDISI JIKA DITOLAK --}}
                    @if ($ditolak)
                    <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50/70 p-4 text-xs">
                        <div class="flex items-center gap-2 font-bold text-rose-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Laporan Ditolak / Tidak Disetujui</span>
                        </div>
                        <p class="mt-1 pl-6 text-rose-600">Alasan: {{ $report->admin_note ?: 'Tidak memenuhi kriteria kewenangan fasilitas publik.' }}</p>
                    </div>
                    @endif

                    {{-- 5. FOOTER KARTU: TOMBOL AKSI --}}
                    @php
                        // Ambil ID secara aman
                        $reportId = $report->id ?? $report['id'] ?? null;
                        $detailUrl = $reportId ? url('/lapor-infrastruktur/detail/' . $reportId) : '#';
                    @endphp

                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                        <div class="flex items-center gap-2.5">
                            {{-- Tombol Lihat Timeline & Worklog Harian --}}
                            <a href="{{ $detailUrl }}#timeline"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#0b1727] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 active:scale-95">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Lihat Timeline & Worklog Harian</span>
                            </a>

                            {{-- Tombol Bagikan Aduan --}}
                            <button type="button"
                                data-copy-url="{{ $detailUrl }}"
                                onclick="navigator.clipboard.writeText(this.getAttribute('data-copy-url')); alert('Tautan aduan berhasil disalin!')"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 shadow-xs transition hover:bg-slate-50 active:scale-95">
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span>Bagikan Aduan</span>
                            </button>
                        </div>
                    </div>

                </div>
                @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-[#0066FF]">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-base font-bold text-slate-800">Belum Ada Laporan</p>
                    <p class="mt-1 text-xs text-slate-400">Semua laporan perbaikan fasilitas yang Anda ajukan akan ditampilkan di sini lengkap dengan pantauan progresnya.</p>
                </div>
                @endforelse
            </div>

        </div>
    </main>
</x-public-layout>