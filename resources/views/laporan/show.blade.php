@php
$badge = [
'pending' => ['Menunggu Verifikasi', 'bg-slate-100 text-slate-600'],
'verified' => ['Perlu Diperbaiki', 'bg-amber-100 text-amber-800'],
'in_progress' => ['Sedang Diperbaiki', 'bg-amber-500 text-white'],
'done' => ['Selesai', 'bg-emerald-100 text-emerald-700'],
'resolved' => ['Selesai', 'bg-emerald-100 text-emerald-700'],
'rejected' => ['Ditolak', 'bg-rose-100 text-rose-700'],
];
[$statusLabel, $statusWarna] = $badge[$report->status] ?? [$report->status, 'bg-slate-100 text-slate-600'];

$url = fn ($path) => $path ? asset('storage/' . $path) : null;
$selesai = in_array($report->status, ['done', 'resolved']);
$persen = (int) ($selesai ? 100 : ($report->progress_percentage ?? 0));

$photos = is_array($report->photos) && count($report->photos) ? $report->photos : array_filter([$report->photo]);

$progresList = [];

// 1. Foto bukti dari warga
foreach (array_values($photos) as $i => $p) {
$progresList[] = [
'foto' => $url($p),
'tahap' => 'Bukti Warga (' . ($i + 1) . ')',
'judul' => $i === 0 ? $report->title : 'Kondisi Kerusakan',
'tanggal' => $report->created_at->translatedFormat('d F Y'),
'catatan' => $i === 0 ? $report->description : 'Foto bukti visual dari pelapor untuk membantu penaksiran penanganan.',
];
}

// 2. Tahap pengerjaan (tanpa foto contoh)
if (in_array($report->status, ['in_progress', 'done', 'resolved']) && $report->supervisor_name) {
$progresList[] = [
'foto' => null,
'ikon' => '🛠️',
'tahap' => 'Penanganan Lapangan',
'judul' => 'Pengerjaan oleh ' . $report->supervisor_name,
'tanggal' => \Carbon\Carbon::parse($report->scheduled_date ?? $report->updated_at)->translatedFormat('d F Y'),
'catatan' => $selesai
? 'Pengerjaan di lokasi telah dilakukan oleh petugas.'
: 'Pengerjaan sedang berlangsung. Progres saat ini ' . $persen . '%.',
];
}

// 3. Hasil akhir
if ($selesai) {
$progresList[] = [
'foto' => $url($report->photo_after),
'ikon' => '✅',
'tahap' => 'Hasil Akhir Tuntas',
'judul' => 'Perbaikan Telah Selesai 100%',
'tanggal' => $report->updated_at->translatedFormat('d F Y'),
'catatan' => $report->completion_notes ?: 'Perbaikan telah selesai dilakukan.',
];
}

// Cadangan bila belum ada satu pun
if (empty($progresList)) {
$progresList[] = [
'foto' => null,
'ikon' => '📋',
'tahap' => 'Laporan Masuk',
'judul' => $report->title,
'tanggal' => $report->created_at->translatedFormat('d F Y'),
'catatan' => $report->description ?: 'Menunggu tinjauan tim.',
];
}
@endphp

<x-public-layout title="Detail Laporan {{ $report->ticket_code }} - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-4xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-6">
                <a href="{{ url()->previous() ?: route('laporan.infrastruktur.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>

            {{-- HEADER --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">Detail {{ $report->ticket_code }}</h1>
            </div>

            {{-- KARTU DETAIL --}}
            <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm sm:p-10">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-md px-3 py-1 text-xs font-bold {{ $statusWarna }}">{{ $statusLabel }}</span>
                    <span class="text-xs font-medium text-slate-400">{{ $report->created_at->diffForHumans() }}</span>
                </div>

                <h2 class="mt-4 text-2xl font-bold tracking-tight text-[#0c203b]">{{ $report->title }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $report->description }}</p>
                <p class="mt-2 font-mono text-xs text-slate-500">📍 {{ $report->address }}</p>

                {{-- ALASAN PENOLAKAN (hanya terlihat pemilik/admin) --}}
                @if ($report->status === 'rejected')
                <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs">
                    <p class="font-bold text-rose-700">Laporan ini ditolak</p>
                    <p class="mt-1 text-rose-600">Alasan: {{ $report->admin_note ?? '-' }}</p>
                </div>
                @endif

                {{-- KARTU PETUGAS (hanya pelapor & admin) --}}
                <div class="mt-5">
                    @include('laporan.partials.petugas', ['report' => $report])
                </div>

                {{-- PROGRES --}}
                <div class="mt-8 border-t border-slate-100 pt-6"
                    x-data="{
                        activeIdx: 0,
                        items: @js($progresList),
                        next() { this.activeIdx = (this.activeIdx + 1) % this.items.length },
                        prev() { this.activeIdx = (this.activeIdx - 1 + this.items.length) % this.items.length },
                    }">

                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-extrabold text-[#0c203b]">Progres</h3>
                        <div class="flex items-center gap-1 text-xs font-bold {{ $persen >= 100 ? 'text-emerald-600' : 'text-blue-600' }}">
                            @if ($persen >= 100)
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            @endif
                            <span>{{ $persen }}%</span>
                        </div>
                    </div>

                    {{-- TIMELINE & WORKLOG --}}
                    <div id="timeline" class="mt-8 border-t border-slate-100 pt-6">
                        <h3 class="text-xl font-extrabold text-[#0c203b]">Timeline & Worklog</h3>

                        <ol class="mt-5 space-y-6 border-l-2 border-blue-100 pl-6">
                            @forelse ($report->logs->sortByDesc('created_at') as $log)
                            <li class="relative">
                                <span class="absolute -left-[31px] top-1 h-3 w-3 rounded-full bg-blue-600 ring-4 ring-white"></span>
                                <p class="text-[11px] font-medium text-slate-400">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
                                <p class="text-sm font-bold text-[#0c203b]">{{ $log->title }}</p>
                                @if ($log->note)
                                <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $log->note }}</p>
                                @endif
                                @if ($log->photo)
                                <a href="{{ asset('storage/' . $log->photo) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $log->photo) }}" alt="Foto" loading="lazy" class="mt-2 h-32 w-auto rounded-xl border border-slate-200 object-cover">
                                </a>
                                @endif
                            </li>
                            @empty
                            <li class="text-xs text-slate-400">Belum ada catatan.</li>
                            @endforelse
                        </ol>
                    </div>

                    <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-blue-100/60">
                        <div class="h-full rounded-full {{ $persen >= 100 ? 'bg-emerald-500' : 'bg-[#0066FF]' }} transition-all duration-500" style="width: {{ $persen }}%"></div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 items-center gap-8 md:grid-cols-12">

                        {{-- FOTO BERTUMPUK --}}
                        <div class="flex select-none flex-col items-center md:col-span-5">
                            <div class="group relative h-64 w-52 cursor-pointer" @click="next()" title="Klik untuk melihat progres berikutnya">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="absolute inset-0 transition-all duration-300 ease-out"
                                        :style="activeIdx === idx
                                            ? 'transform: rotate(2deg) scale(1) translate(0px, 0px); z-index: 20; opacity: 1;'
                                            : ((idx === (activeIdx + 1) % items.length)
                                                ? 'transform: rotate(-6deg) scale(0.96) translate(-10px, 8px); z-index: 10; opacity: 0.9;'
                                                : 'transform: rotate(6deg) scale(0.92) translate(10px, 12px); z-index: 5; opacity: 0;')">

                                        <template x-if="item.foto">
                                            <img :src="item.foto" alt="Foto progres"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                                                class="h-60 w-48 rounded-2xl border-2 border-white object-cover shadow-xl transition-transform group-hover:scale-105">
                                        </template>

                                        {{-- Placeholder: dipakai bila tahap tanpa foto atau gambar gagal dimuat --}}
                                        <div class="h-60 w-48 flex-col items-center justify-center rounded-2xl border-2 border-white bg-gradient-to-br from-blue-50 to-slate-100 text-center shadow-xl"
                                            :style="item.foto ? 'display:none' : 'display:flex'">
                                            <span class="text-5xl" x-text="item.ikon || '📷'"></span>
                                            <span class="mt-2 px-3 text-[11px] font-bold text-slate-500" x-text="item.tahap"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-3 flex items-center gap-3">
                                <button type="button" @click="prev()" class="rounded-full bg-slate-100 p-1.5 text-slate-600 hover:bg-slate-200">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <span class="text-[11px] font-bold text-slate-400"><span x-text="activeIdx + 1"></span> / <span x-text="items.length"></span> Tahap</span>
                                <button type="button" @click="next()" class="rounded-full bg-slate-100 p-1.5 text-slate-600 hover:bg-slate-200">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-0.5 text-[10px] text-slate-400">(Klik foto untuk berganti)</p>
                        </div>

                        {{-- TEKS --}}
                        <div class="md:col-span-7">
                            <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-600" x-text="items[activeIdx].tahap"></span>
                            <h4 class="mt-1.5 text-xl font-bold text-[#0c203b]" x-text="items[activeIdx].judul"></h4>
                            <p class="mt-0.5 text-xs font-medium text-slate-400" x-text="items[activeIdx].tanggal"></p>
                            <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm text-slate-600 shadow-inner">
                                <p class="italic leading-relaxed" x-text="items[activeIdx].catatan"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-public-layout>