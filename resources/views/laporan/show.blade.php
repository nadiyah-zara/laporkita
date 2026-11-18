@php
$badge = [
'pending' => ['Menunggu Verifikasi', 'border-slate-200 bg-slate-100 text-slate-600'],
'verified' => ['Perlu Diperbaiki', 'border-amber-200 bg-amber-50 text-amber-800'],
'in_progress' => ['Sedang Diperbaiki', 'border-amber-300 bg-amber-500 text-white'],
'done' => ['Selesai Ditangani', 'border-emerald-200 bg-emerald-50 text-emerald-700'],
'resolved' => ['Selesai Ditangani', 'border-emerald-200 bg-emerald-50 text-emerald-700'],
'rejected' => ['Ditolak', 'border-rose-200 bg-rose-50 text-rose-700'],
];
[$statusLabel, $statusWarna] = $badge[$report->status] ?? [$report->status, 'border-slate-200 bg-slate-100 text-slate-600'];

$url = fn ($path) => $path ? asset('storage/' . $path) : null;
$selesai = in_array($report->status, ['done', 'resolved']);
$persen = (int) ($selesai ? 100 : ($report->progress_percentage ?? 0));

$photos = is_array($report->photos) && count($report->photos) ? $report->photos : array_filter([$report->photo]);

$progresList = [];

// 1. Foto bukti dari warga
foreach (array_values($photos) as $i => $p) {
$progresList[] = [
'foto' => $url($p),
'ikon' => 'laporan',
'tahap' => 'Bukti Warga (' . ($i + 1) . ')',
'judul' => $i === 0 ? $report->title : 'Kondisi Kerusakan',
'tanggal' => $report->created_at->translatedFormat('d F Y'),
'catatan' => $i === 0 ? $report->description : 'Foto visual dari pelapor untuk membantu taksiran penanganan.',
];
}

// 2. Tahap pengerjaan
if (in_array($report->status, ['in_progress', 'done', 'resolved']) && $report->supervisor_name) {
$progresList[] = [
'foto' => null,
'ikon' => 'penanganan',
'tahap' => 'Penanganan Lapangan',
'judul' => 'Pengerjaan oleh ' . $report->supervisor_name,
'tanggal' => \Carbon\Carbon::parse($report->scheduled_date ?? $report->updated_at)->translatedFormat('d F Y'),
'catatan' => $selesai
? 'Pengerjaan fisik di lokasi telah selesai dilaksanakan oleh dinas terkait.'
: 'Pengerjaan sedang berlangsung dengan capaian saat ini ' . $persen . '%.',
];
}

// 3. Hasil akhir
if ($selesai) {
$progresList[] = [
'foto' => $url($report->photo_after),
'ikon' => 'selesai',
'tahap' => 'Hasil Akhir Tuntas',
'judul' => 'Perbaikan Rampung 100%',
'tanggal' => $report->updated_at->translatedFormat('d F Y'),
'catatan' => $report->completion_notes ?: 'Perbaikan infrastruktur telah rampung secara menyeluruh.',
];
}

// Fallback jika belum ada progres
if (empty($progresList)) {
$progresList[] = [
'foto' => null,
'ikon' => 'laporan',
'tahap' => 'Laporan Masuk',
'judul' => $report->title,
'tanggal' => $report->created_at->translatedFormat('d F Y'),
'catatan' => $report->description ?: 'Menunggu tinjauan tim verifikasi.',
];
}
@endphp

<x-public-layout title="Detail Laporan {{ $report->ticket_code }} - LaporKita">
    <main class="min-h-screen bg-[#f8fafc] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5">
                <a href="{{ url()->previous() ?: route('laporan.infrastruktur.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>

            {{-- HEADER HALAMAN --}}
            <div class="mb-6 flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-mono text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Tiket {{ $report->ticket_code }}
                    </h1>
                    <p class="text-xs text-slate-500">Rincian status dan tahapan pengerjaan fasilitas publik.</p>
                </div>
            </div>

            {{-- KARTU DETAIL UTAMA --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8">

                {{-- Status & Waktu --}}
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1 text-xs font-bold {{ $statusWarna }}">
                        {{ $statusLabel }}
                    </span>
                    <div class="flex items-center gap-1 text-xs text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $report->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <h2 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">{{ $report->title }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $report->description }}</p>

                <div class="mt-3 flex items-center gap-1.5 text-xs text-slate-500">
                    <svg class="h-4 w-4 text-[#0066FF] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $report->address }}</span>
                </div>

                {{-- Notifikasi Ditolak --}}
                @if ($report->status === 'rejected')
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50/70 p-4 text-xs">
                    <div class="flex items-center gap-2 text-rose-700 font-bold">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Laporan Ini Ditolak</span>
                    </div>
                    <p class="mt-1 text-rose-600 pl-6">Alasan: {{ $report->admin_note ?? 'Tidak ada catatan penolakan.' }}</p>
                </div>
                @endif

                {{-- KARTU PETUGAS --}}
                <div class="mt-5">
                    @include('laporan.partials.petugas', ['report' => $report])
                </div>

                {{-- INTERAKTIF PROGRES DOKUMENTASI (FOTO STACK & WORKLOG) --}}
                <div class="mt-8 border-t border-slate-100 pt-6"
                    x-data="{
                        activeIdx: 0,
                        items: @js($progresList),
                        next() { this.activeIdx = (this.activeIdx + 1) % this.items.length },
                        prev() { this.activeIdx = (this.activeIdx - 1 + this.items.length) % this.items.length },
                    }">

                    <div class="flex items-center gap-2" title="{{ $persen }}%">
                        <span class="relative flex h-3 w-3">
                            @if ($persen < 100)
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#0066FF] opacity-60"></span>
                        @endif
                        <span class="relative inline-flex h-3 w-3 rounded-full {{ $persen >= 100 ? 'bg-emerald-500' : 'bg-[#0066FF]' }}"></span>
                        </span>
                        <span class="sr-only">{{ $persen }}%</span>
                        <span class="text-xs font-bold {{ $persen >= 100 ? 'text-emerald-600' : 'text-[#0066FF]' }}">
                            {{ $persen >= 100 ? 'Selesai' : 'Diproses' }}
                        </span>
                    </div>
                    {{-- Bar Progres --}}
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full {{ $persen >= 100 ? 'bg-emerald-500' : 'bg-[#0066FF]' }} transition-all duration-500" style="width: {{ $persen }}%"></div>
                    </div>

                    {{-- Foto Stack Interaktif --}}
                    <div class="mt-7 grid grid-cols-1 items-center gap-8 md:grid-cols-12">
                        <div class="flex select-none flex-col items-center md:col-span-5">
                            <div class="group relative h-64 w-52 cursor-pointer" @click="next()" title="Klik untuk beralih gambar">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="absolute inset-0 transition-all duration-300 ease-out"
                                        :style="activeIdx === idx
                                            ? 'transform: rotate(2deg) scale(1); z-index: 20; opacity: 1;'
                                            : ((idx === (activeIdx + 1) % items.length)
                                                ? 'transform: rotate(-5deg) scale(0.96) translate(-8px, 6px); z-index: 10; opacity: 0.9;'
                                                : 'transform: rotate(5deg) scale(0.92) translate(8px, 10px); z-index: 5; opacity: 0;')">

                                        <template x-if="item.foto">
                                            <img :src="item.foto" alt="Foto progres"
                                                class="h-60 w-48 rounded-2xl border-2 border-white object-cover shadow-lg transition-transform group-hover:scale-103">
                                        </template>

                                        {{-- Placeholder Ilustratif Elegan --}}
                                        <div class="flex h-60 w-48 flex-col items-center justify-center rounded-2xl border-2 border-white bg-gradient-to-br from-blue-50 to-slate-100 p-4 text-center shadow-lg"
                                            :style="item.foto ? 'display:none' : 'display:flex'">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-[#0066FF] shadow-xs">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            </div>
                                            <span class="mt-3 text-xs font-bold text-slate-700" x-text="item.tahap"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-3 flex items-center gap-3">
                                <button type="button" @click="prev()" class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-xs hover:bg-slate-50">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <span class="text-xs font-semibold text-slate-400">
                                    <span x-text="activeIdx + 1"></span> / <span x-text="items.length"></span>
                                </span>
                                <button type="button" @click="next()" class="flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-xs hover:bg-slate-50">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Keterangan Teks Progres --}}
                        <div class="md:col-span-7">
                            <span class="rounded-md border border-blue-100 bg-blue-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-[#0066FF]" x-text="items[activeIdx].tahap"></span>
                            <h4 class="mt-2 text-xl font-bold text-slate-900" x-text="items[activeIdx].judul"></h4>
                            <p class="mt-0.5 text-xs font-medium text-slate-400" x-text="items[activeIdx].tanggal"></p>
                            <div class="mt-3.5 rounded-2xl border border-slate-100 bg-slate-50/80 p-4 text-xs text-slate-600">
                                <p class="leading-relaxed" x-text="items[activeIdx].catatan"></p>
                            </div>
                        </div>
                    </div>

                    {{-- TIMELINE & WORKLOG RESMI --}}
                    <div id="timeline" class="mt-10 border-t border-slate-100 pt-7">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-[#0066FF]">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">Timeline & Riwayat Kerja Harian</h3>
                        </div>

                        <ol class="space-y-6 border-l-2 border-blue-100 pl-6">
                            @forelse ($report->logs->sortByDesc('created_at') as $log)
                            <li class="relative">
                                <span class="absolute -left-[31px] top-1 h-3 w-3 rounded-full bg-[#0066FF] ring-4 ring-white shadow-xs"></span>
                                <p class="text-[11px] font-medium text-slate-400">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
                                <p class="text-sm font-bold text-slate-900">{{ $log->title }}</p>
                                @if ($log->note)
                                <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $log->note }}</p>
                                @endif
                                @if ($log->photo)
                                <a href="{{ asset('storage/' . $log->photo) }}" target="_blank" class="mt-2.5 inline-block overflow-hidden rounded-xl border border-slate-200">
                                    <img src="{{ asset('storage/' . $log->photo) }}" alt="Foto pengerjaan" loading="lazy" class="h-32 w-auto object-cover transition hover:scale-105">
                                </a>
                                @endif
                            </li>
                            @empty
                            <li class="text-xs text-slate-400">Belum ada catatan aktivitas tambahan dari petugas lapangan.</li>
                            @endforelse
                        </ol>
                    </div>

                </div>
            </div>
        </div>
    </main>
</x-public-layout>