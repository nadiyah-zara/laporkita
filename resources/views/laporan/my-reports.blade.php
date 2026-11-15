@php
$tabs = [
'' => ['Semua', $stats['total']],
'pending' => ['Belum Diverifikasi', $stats['pending']],
'verified' => ['Diverifikasi', $stats['verified']],
'in_progress' => ['Sedang Dikerjakan', $stats['in_progress']],
'resolved' => ['Selesai', $stats['resolved']],
'rejected' => ['Ditolak', $stats['rejected'] ?? 0],
];
$aktif = request('status', '');
$url = fn ($p) => $p ? asset('storage/' . $p) : null;
@endphp

<x-public-layout title="Laporan Saya - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6">
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

            {{-- HEADER --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">Laporan Saya</h1>
            </div>

            @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            {{-- TAB STATUS --}}
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($tabs as $key => [$label, $jumlah])
                        <a href="{{ $key === '' ? route('laporan.saya') : route('laporan.saya', ['status' => $key]) }}"
                            class="rounded-full px-3.5 py-1 text-xs font-bold {{ (string) $aktif === (string) $key ? 'bg-[#0c203b] text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                            {{ $label }} {{ $jumlah }}
                        </a>
                        @endforeach
                    </div>
                    <span class="text-[11px] font-medium text-emerald-600">● Sinkronisasi Real-time Aktif</span>
                </div>
            </div>

            {{-- DAFTAR LAPORAN --}}
            <div class="mt-6 space-y-6">
                @forelse ($reports as $report)
                @php
                $selesai = in_array($report->status, ['done', 'resolved']);
                $ditolak = $report->status === 'rejected';
                $step = $selesai ? 6 : (int) ($report->current_step ?? 1);
                $persen = $selesai ? 100 : (int) ($report->progress_percentage ?? 0);
                $fotoAwal = $url(is_array($report->photos) && count($report->photos) ? $report->photos[0] : $report->photo);
                $fotoAkhir = $url($report->photo_after);
                $logWaktu = function ($n) use ($report) {
                $l = $report->logs->firstWhere('step', $n);
                return $l?->created_at->timezone('Asia/Jakarta')->format('d M H:i');
                };

                // Hanya tampilkan waktu yang benar-benar tercatat
                $steps = [
                1 => ['1. Dilaporkan', $report->created_at->timezone('Asia/Jakarta')->format('d M H:i')],
                2 => ['2. Survey Tim', $logWaktu(2)],
                3 => ['3. Diverifikasi', $logWaktu(3)],
                4 => ['4. Persiapan', $logWaktu(4)],
                5 => ['5. Pengerjaan', $selesai ? $logWaktu(5) : ($step >= 5 ? $persen . '%' : null)],
                6 => ['6. Selesai', $logWaktu(6)],
                ];
                @endphp

                <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">

                    {{-- HEADER KARTU --}}
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-base font-extrabold text-[#0c203b]">{{ $report->ticket_code }}</span>
                            <span class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">{{ $report->category->name ?? 'Fasilitas' }}</span>
                            @if ($report->urgency == 'kritis')
                            <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-600">⚠ Prioritas Kritis</span>
                            @else
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">Prioritas Standar</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $selesai ? 'bg-emerald-50 text-emerald-700' : ($ditolak ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-700') }}">
                                {{ $report->status_label }}
                            </span>
                            <span class="text-xs text-slate-400">Diajukan: {{ $report->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>

                    {{-- JUDUL & LOKASI --}}
                    <div class="mt-3">
                        <h3 class="text-xl font-bold text-[#0c203b]">{{ $report->title }}</h3>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 text-xs font-mono text-slate-500">
                            <span>📍 {{ $report->address }}</span>
                            @if ($report->latitude && $report->longitude)
                            <span>• Koordinat ({{ $report->latitude }}, {{ $report->longitude }})</span>
                            @endif
                        </div>
                    </div>

                    {{-- DITOLAK --}}
                    @if ($ditolak)
                    <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs">
                        <p class="font-bold text-rose-700">Laporan ini ditolak oleh admin</p>
                        <p class="mt-1 text-rose-600">Alasan: {{ $report->admin_note ?: 'Tidak ada keterangan.' }}</p>
                    </div>
                    @endif

                    {{-- SEDANG DIKERJAKAN --}}
                    @if ($report->status == 'in_progress')
                    <div class="mt-5 rounded-2xl border border-blue-100 bg-blue-50/50 p-4">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                            <span>Sedang dikerjakan petugas</span>
                            <span class="text-blue-600">{{ $persen }}%</span>
                        </div>
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-blue-100">
                            <div class="h-full rounded-full bg-[#0066FF] transition-all" style="width: {{ $persen }}%"></div>
                        </div>
                        <p class="mt-2 text-[11px] text-slate-500">Pembaruan terakhir {{ $report->updated_at->diffForHumans() }}</p>
                    </div>
                    @endif

                    {{-- SELESAI: SEBELUM & SESUDAH --}}
                    @if ($selesai)
                    <div class="mt-5 grid grid-cols-1 gap-4 {{ $fotoAwal && $fotoAkhir ? 'sm:grid-cols-2' : '' }}">
                        @if ($fotoAwal)
                        <div class="relative">
                            <img src="{{ $fotoAwal }}" alt="Kondisi sebelum" loading="lazy" class="h-44 w-full rounded-2xl object-cover">
                            <span class="absolute left-2 top-2 rounded-md bg-slate-900/80 px-2 py-0.5 text-[10px] font-bold text-white">
                                Kondisi Sebelum ({{ $report->created_at->translatedFormat('d M Y') }})
                            </span>
                        </div>
                        @endif
                        @if ($fotoAkhir)
                        <div class="relative">
                            <img src="{{ $fotoAkhir }}" alt="Hasil perbaikan" loading="lazy" class="h-44 w-full rounded-2xl object-cover">
                            <span class="absolute left-2 top-2 rounded-md bg-blue-600/90 px-2 py-0.5 text-[10px] font-bold text-white">
                                Hasil Perbaikan ({{ $report->updated_at->translatedFormat('d M Y') }})
                            </span>
                        </div>
                        @endif
                    </div>
                    @if ($report->completion_notes)
                    <div class="mt-3 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                        <strong>Rincian Hasil Pengerjaan:</strong> {{ $report->completion_notes }}
                    </div>
                    @endif
                    @endif

                    {{-- STEPPER 6 TAHAP --}}
                    @unless ($ditolak)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <div class="mb-4 flex items-center justify-between text-xs font-bold text-slate-800">
                            <span class="uppercase tracking-wider">Tahapan Pengerjaan Lapangan</span>
                            <span class="{{ $selesai ? 'text-emerald-600' : 'text-blue-600' }}">{{ $step }} dari 6 Tahap {{ $selesai ? 'Selesai' : 'Dilalui' }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-center text-[11px] sm:grid-cols-6">
                            @foreach ($steps as $n => [$nama, $waktu])
                            <div class="flex flex-col items-center">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full {{ $step >= $n ? 'bg-blue-600 font-bold text-white' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $step >= $n ? '✓' : $n }}
                                </div>
                                <span class="mt-1 font-bold {{ $step >= $n ? 'text-slate-900' : 'text-slate-400' }}">{{ $nama }}</span>
                                <span class="text-[9px] text-slate-400">{{ $waktu }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endunless

                    {{-- KARTU PETUGAS --}}
                    <div class="mt-5">
                        @include('laporan.partials.petugas', ['report' => $report])
                    </div>

                    {{-- TOMBOL --}}
                    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-50 pt-4">
                        <a href="{{ route('laporan.infrastruktur.detail', $report->id) }}#timeline" class="inline-flex items-center gap-1.5 rounded-xl bg-[#0c203b] px-4 py-2 text-xs font-bold text-white hover:bg-slate-800">
                            <span>Lihat Timeline & Worklog Harian</span>
                        </a>
                    </div>
                </div>
                @empty
                <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-14 text-center text-sm text-slate-500">
                    Belum ada laporan pada filter ini.
                </div>
                @endforelse
            </div>

        </div>
    </main>
</x-public-layout>