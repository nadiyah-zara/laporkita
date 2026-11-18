<x-public-layout title="{{ $report->ticket_code }} - Rincian Laporan">
    <main class="min-h-screen bg-[#f8fafc] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            {{-- TOMBOL KEMBALI --}}
            <div class="mb-5">
                <a href="{{ route('laporan.barang.saya') }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Laporan Saya</span>
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
                    <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-800 shadow-sm">
                    <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($report->hidden_at)
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50/70 p-4 text-xs shadow-sm">
                    <p class="font-bold text-rose-700">Laporan ini disembunyikan oleh admin</p>
                    <p class="mt-1 text-rose-600">Alasan: {{ $report->hidden_reason }}</p>
                </div>
            @endif

            {{-- HEADER LAPORAN + HAPUS --}}
            <div class="flex items-stretch gap-3">
                <form method="POST" action="{{ route('laporan.barang.saya.destroy', $report) }}"
                    onsubmit="return confirm('Hapus laporan ini beserta seluruh tanggapan yang terhubung?')">
                    @csrf @method('DELETE')
                    <button type="submit" 
                            class="flex h-full w-20 flex-col items-center justify-center gap-1.5 rounded-2xl border border-rose-200 bg-rose-50/60 p-3 text-[10px] font-bold text-rose-600 transition hover:bg-rose-100 active:scale-95 shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>HAPUS</span>
                    </button>
                </form>

                <div class="flex-1 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md">{{ $report->ticket_code }}</span>
                                <span class="rounded-md border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-[#0066FF]">Laporan Saya</span>
                            </div>
                            <h1 class="mt-2 text-xl font-bold text-slate-900">{{ $report->title }}</h1>
                            <p class="mt-1 text-xs text-slate-500">{{ $report->address }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-xs font-bold {{ $report->item_kind === 'penemuan' ? 'border-blue-200 bg-blue-50 text-[#0066FF]' : 'border-rose-200 bg-rose-50 text-rose-600' }}">
                                {{ $report->item_kind === 'penemuan' ? 'Ditemukan' : 'Dicari' }}
                            </span>
                            <p class="mt-2 text-[11px] text-slate-400">
                                {{ $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DAFTAR TANGGAPAN MASUK --}}
            <div class="mt-6 space-y-3.5">
                <h2 class="text-sm font-bold text-slate-900">Daftar Tanggapan / Klaim Masuk</h2>

                @forelse ($claims as $claim)
                    @php [$label, $badge] = $claim->reporter_label; @endphp
                    <a href="{{ route('laporan.barang.tanggapan', [$report, $claim]) }}"
                       class="group block rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <h3 class="text-base font-bold text-slate-800 group-hover:text-[#0066FF] transition-colors">
                                    Tanggapan {{ $claim->nomor }}
                                </h3>
                                <span class="rounded-md px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] text-slate-400">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $claim->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500">
                        <p class="text-sm font-semibold text-slate-700">Belum Ada Tanggapan</p>
                        <p class="mt-0.5 text-xs text-slate-400">Belum ada warga yang mengajukan klaim untuk laporan ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</x-public-layout>