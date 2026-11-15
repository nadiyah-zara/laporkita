@php
    $selesai = in_array($report->status, ['resolved', 'done']);
    $field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none';

    $stepLabel = fn ($c) => $c->rejected_at ? ['Ditolak', 'bg-rose-50 text-rose-600'] : match (true) {
        $c->current_step >= 6 => ['Selesai', 'bg-emerald-50 text-emerald-600'],
        $c->current_step == 5 => ['Jadwal pengambilan', 'bg-indigo-50 text-indigo-600'],
        $c->current_step >= 3 => ['Diverifikasi', 'bg-blue-50 text-blue-600'],
        default               => ['Menunggu verifikasi', 'bg-amber-50 text-amber-600'],
    };
@endphp

<x-admin-layout title="{{ $report->ticket_code }} - Admin LaporKita">

    <a href="{{ route('admin.barang.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">← Kembali</a>

    @if (session('success'))
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    <div class="mb-6 mt-5 flex flex-wrap items-center gap-3">
        <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">{{ $report->ticket_code }}</h1>
        @if ($report->item_kind === 'penemuan')
            <span class="rounded-md bg-blue-50 px-3 py-1 text-[11px] font-bold text-blue-700">Ditemukan</span>
        @else
            <span class="rounded-md bg-rose-50 px-3 py-1 text-[11px] font-bold text-rose-600">Dicari</span>
        @endif
        @if ($report->hidden_at)
            <span class="rounded-md bg-rose-100 px-3 py-1 text-[11px] font-bold text-rose-700">DISEMBUNYIKAN</span>
        @elseif ($selesai)
            <span class="rounded-md bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-600">Selesai</span>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-5">

        {{-- KIRI --}}
        <div class="space-y-6 lg:col-span-3">
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-[#0c203b]">{{ $report->title }}</h2>
                <p class="mt-1 text-[11px] text-slate-400">
                    {{ $report->category?->name }} · Kejadian {{ \Carbon\Carbon::parse($report->event_date)->translatedFormat('d F Y') }}{{ $report->event_time ? ', ' . $report->event_time : '' }}
                    · Masuk {{ $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                </p>
                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $report->description }}</p>

                @if ($report->photo)
                    <a href="{{ asset('storage/' . $report->photo) }}" target="_blank" class="mt-5 block overflow-hidden rounded-xl bg-slate-100">
                        <img src="{{ asset('storage/' . $report->photo) }}" alt="Foto barang" loading="lazy" class="max-h-80 w-full object-cover">
                    </a>
                @endif
            </section>

            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0c203b]">Lokasi</h3>
                <p class="mt-2 text-xs text-slate-600">📍 {{ $report->address }}</p>
                @if ($report->latitude && $report->longitude)
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank"
                        class="mt-2 inline-block text-xs font-bold text-blue-600 hover:underline">Buka di Google Maps →</a>
                @endif
            </section>

            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0c203b]">Pelapor</h3>
                <p class="mt-2 text-xs font-semibold text-slate-800">{{ $report->user?->name ?? $report->reporter_name }}</p>
                <p class="text-xs text-slate-500">{{ $report->user?->email }}</p>
                <p class="mt-2 text-[10px] text-slate-400">Nomor WhatsApp pelapor sengaja tidak ditampilkan karena hanya dibuka antar pihak yang saling terverifikasi.</p>
            </section>
        </div>

        {{-- KANAN --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- MODERASI --}}
            @if ($report->hidden_at)
                <section class="rounded-2xl border border-rose-100 bg-rose-50/50 p-6">
                    <h3 class="text-sm font-bold text-rose-700">Laporan Disembunyikan</h3>
                    <p class="mt-1 text-[11px] text-rose-500">{{ $report->hidden_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
                    <p class="mt-2 text-xs text-rose-700">Alasan: {{ $report->hidden_reason }}</p>

                    <form method="POST" action="{{ route('admin.barang.pulihkan', $report) }}" class="mt-4"
                        onsubmit="return confirm('Tampilkan kembali laporan ini ke publik?')">
                        @csrf
                        <button class="w-full rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white hover:bg-emerald-700">↺ Pulihkan Laporan</button>
                    </form>
                    <p class="mt-2 text-[10px] text-slate-500">Klaim yang sudah ditolak otomatis tidak ikut pulih. Pengklaim perlu mengajukan ulang.</p>
                </section>
            @else
                <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm" x-data="{ buka: false }">
                    <h3 class="text-sm font-bold text-[#0c203b]">Moderasi</h3>
                    <p class="mt-1 text-[11px] text-slate-500">Sembunyikan laporan yang spam, melanggar aturan, atau memuat data pribadi (misalnya foto KTP/ATM).</p>

                    <button type="button" @click="buka = !buka" class="mt-4 w-full rounded-xl border border-rose-200 bg-rose-50 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-100">
                        🚫 Sembunyikan Laporan
                    </button>

                    <form x-show="buka" x-cloak method="POST" action="{{ route('admin.barang.sembunyikan', $report) }}" class="mt-3 space-y-3"
                        onsubmit="return confirm('Sembunyikan laporan ini? Klaim yang masih berjalan akan ditolak otomatis.')">
                        @csrf
                        <textarea name="hidden_reason" rows="3" required maxlength="500" placeholder="Alasan (akan dilihat pelapor)" class="{{ $field }}"></textarea>
                        <button class="w-full rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi</button>
                        <p class="text-[10px] text-slate-400">Laporan hilang dari feed, peta, dan Dokumentasi. Datanya tetap tersimpan dan bisa dipulihkan.</p>
                    </form>
                </section>
            @endif

            {{-- TANGGAPAN --}}
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-[#0c203b]">Tanggapan Masuk</h3>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">{{ $claims->count() }}</span>
                </div>

                <div class="mt-4 divide-y divide-slate-100">
                    @forelse ($claims as $c)
                        @php [$lbl, $warna] = $stepLabel($c); @endphp
                        <div class="flex items-start justify-between gap-3 py-3 text-xs">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-800">{{ $c->claim_code }}</p>
                                <p class="truncate text-[11px] text-slate-400">{{ $c->user?->name }} · {{ $c->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="shrink-0 rounded-md px-2.5 py-1 text-[10px] font-bold {{ $warna }}">{{ $lbl }}</span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs text-slate-400">Belum ada yang menanggapi.</p>
                    @endforelse
                </div>
                <p class="mt-3 text-[10px] text-slate-400">Jawaban rahasia dan bukti kepemilikan hanya dilihat pihak terkait.</p>
            </section>
        </div>
    </div>
</x-admin-layout>