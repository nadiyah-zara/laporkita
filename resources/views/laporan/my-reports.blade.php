<x-public-layout title="Laporan Saya - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-6">
                <a href="{{ route('laporan.infrastruktur.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- HEADER JUDUL --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">
                    Laporan Saya
                </h1>
            </div>


            {{-- FILTER & SEARCH BAR LAPORAN SAYA --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                    <div class="relative sm:col-span-6">
                        <svg class="absolute top-3 left-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" placeholder="Cari Laporan" class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2 pr-3 pl-9 text-xs text-slate-700 focus:outline-none">
                    </div>
                    <div class="sm:col-span-3">
                        <select class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2 px-3 text-xs font-semibold text-slate-700">
                            <option>Semua Kategori</option>
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <select class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-2 px-3 text-xs font-semibold text-slate-700">
                            <option>Paling Terbaru</option>
                        </select>
                    </div>
                </div>

                {{-- Status Badges Tabs --}}
                <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('laporan.saya') }}" class="rounded-full bg-[#0c203b] px-3.5 py-1 text-xs font-bold text-white">Semua {{ $stats['total'] }}</a>
                        <a href="{{ route('laporan.saya', ['status' => 'in_progress']) }}" class="rounded-full bg-blue-50 px-3.5 py-1 text-xs font-bold text-blue-700">Sedang Dikerjakan {{ $stats['in_progress'] }}</a>
                        <a href="{{ route('laporan.saya', ['status' => 'pending']) }}" class="rounded-full bg-blue-50 px-3.5 py-1 text-xs font-bold text-blue-700">Belum Diverifikasi {{ $stats['pending'] }}</a>
                        <a href="{{ route('laporan.saya', ['status' => 'verified']) }}" class="rounded-full bg-blue-50 px-3.5 py-1 text-xs font-bold text-blue-700">Diverifikasi {{ $stats['verified'] }}</a>
                        <a href="{{ route('laporan.saya', ['status' => 'resolved']) }}" class="rounded-full bg-blue-50 px-3.5 py-1 text-xs font-bold text-blue-700">Selesai {{ $stats['resolved'] }}</a>
                    </div>
                    <span class="text-[11px] font-medium text-emerald-600">● Sinkronisasi Satelit Real-time Aktif</span>
                </div>
            </div>

            {{-- LIST LAPORAN TRACKING CARD DENGAN STEPPER --}}
            <div class="mt-6 space-y-6">
                @foreach ($reports as $report)
                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                    {{-- Header Card --}}
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-base font-extrabold text-[#0c203b]">{{ $report->ticket_code }}</span>
                            <span class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">{{ $report->category->name ?? 'Fasilitas' }}</span>
                            @if ($report->urgency == 'kritis')
                            <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-600">
                                ⚠ Prioritas Kritis
                            </span>
                            @else
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">Prioritas Standar</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                {{ $report->status_label }}
                            </span>
                            <span class="text-xs text-slate-400">Diajukan: {{ $report->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>

                    {{-- Judul & Lokasi --}}
                    <div class="mt-3">
                        <h3 class="text-xl font-bold text-[#0c203b]">{{ $report->title }}</h3>
                        <div class="mt-1 flex items-center gap-3 text-xs font-mono text-slate-500">
                            <span>📍 {{ $report->address }}</span>
                            <span>• Koordinat ({{ $report->latitude }}, {{ $report->longitude }})</span>
                        </div>
                    </div>

                    {{-- Update Lapangan Box (Jika Status Sedang Dikerjakan) --}}
                    @if ($report->status == 'in_progress')
                    <div class="mt-5 rounded-2xl bg-blue-50/50 p-4 border border-blue-100">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 items-center">
                            <div class="sm:col-span-5 relative">
                                <img src="{{ !empty($report->photos) ? asset('storage/' . $report->photos[0]) : 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600' }}"
                                    class="h-36 w-full rounded-xl object-cover shadow-sm">
                                <span class="absolute bottom-2 left-2 rounded-md bg-blue-600/90 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur">
                                    Update Lapangan Hari Ini
                                </span>
                            </div>
                            <div class="sm:col-span-7 text-xs text-slate-700">
                                <span class="text-[10px] text-slate-400">35 menit yang lalu</span>
                                <p class="font-bold text-slate-900 mt-1">Catatan Pengawas Lapangan: {{ $report->supervisor_name ?? 'dr. Bambang Trihatmojo' }}</p>
                                <p class="mt-1 leading-relaxed text-slate-600">"{{ $report->supervisor_notes ?? 'Pekerjaan lapis aus AC-WC seluas 18 m² telah rampung dipadatkan dengan tandem roller. Saat ini sedang masa pendinginan aspal agar struktur kokoh.' }}"</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Before - After Box (Jika Status Selesai) --}}
                    @if ($report->status == 'resolved')
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="relative">
                            <img src="{{ $report->photo_before ? asset('storage/' . $report->photo_before) : 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600' }}" class="h-44 w-full rounded-2xl object-cover">
                            <span class="absolute top-2 left-2 rounded-md bg-slate-900/80 px-2 py-0.5 text-[10px] font-bold text-white">Kondisi Sebelum (10 Sep 2026)</span>
                        </div>
                        <div class="relative">
                            <img src="{{ $report->photo_after ? asset('storage/' . $report->photo_after) : 'https://images.unsplash.com/photo-1541888946425-d0fbb186f5f8?w=600' }}" class="h-44 w-full rounded-2xl object-cover">
                            <span class="absolute top-2 left-2 rounded-md bg-blue-600/90 px-2 py-0.5 text-[10px] font-bold text-white">Hasil Perbaikan (11 Okt 2026)</span>
                        </div>
                    </div>
                    <div class="mt-3 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                        <strong>Rincian Hasil Pengerjaan Lapangan:</strong> {{ $report->completion_notes ?? 'Perbaikan konstruksi telah rampung 100%. Dokumen BAST resmi telah ditandatangani digital oleh Pengawas Dinas dan Warga Pelapor.' }}
                    </div>
                    @endif

                    {{-- TAHAPAN PENGERJAAN LAPANGAN (STEPPER 6 TAHAP) --}}
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-800 mb-4">
                            <span class="uppercase tracking-wider">Tahapan Pengerjaan Lapangan</span>
                            <span class="text-blue-600">{{ $report->current_step }} dari 6 Tahap Selesai</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-6 text-center text-[11px]">
                            @php
                            $steps = [
                            1 => ['name' => '1. Dilaporkan', 'time' => '10 Okt 07:15'],
                            2 => ['name' => '2. Survey Tim', 'time' => '10 Okt 09:30'],
                            3 => ['name' => '3. Diverifikasi', 'time' => '10 Okt 11:15'],
                            4 => ['name' => '4. Persiapan', 'time' => '10 Okt 14:00'],
                            5 => ['name' => '5. Pengerjaan', 'time' => 'Hari Ini 14:45'],
                            6 => ['name' => '6. Selesai', 'time' => 'Estimasi 18:00'],
                            ];
                            @endphp

                            @foreach ($steps as $stepNum => $stepData)
                            <div class="flex flex-col items-center">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full {{ $report->current_step >= $stepNum ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-400' }}">
                                    @if ($report->current_step >= $stepNum) ✓ @else {{ $stepNum }} @endif
                                </div>
                                <span class="mt-1 font-bold {{ $report->current_step >= $stepNum ? 'text-slate-900' : 'text-slate-400' }}">{{ $stepData['name'] }}</span>
                                <span class="text-[9px] text-slate-400">{{ $stepData['time'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-50 pt-4">
                        <a href="{{ route('laporan.infrastruktur.detail', $report->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-[#0c203b] px-4 py-2 text-xs font-bold text-white hover:bg-slate-800">
                            <span>Lihat Timeline & Worklog Harian</span>
                        </a>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                            <span>Bagikan Aduan</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </main>
</x-public-layout>