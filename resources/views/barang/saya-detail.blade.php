<x-public-layout title="{{ $report->ticket_code }} - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            <a href="{{ route('laporan.barang.saya') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">← Kembali</a>

            @if (session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">{{ session('error') }}</div>
            @endif
            @if ($report->hidden_at)
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs">
                <p class="font-bold text-rose-700">Laporan ini disembunyikan oleh admin</p>
                <p class="mt-1 text-rose-600">Alasan: {{ $report->hidden_reason }}</p>
            </div>
            @endif

            {{-- HEADER LAPORAN + HAPUS --}}
            <div class="mt-6 flex items-stretch gap-3">
                <form method="POST" action="{{ route('laporan.barang.saya.destroy', $report) }}"
                    onsubmit="return confirm('Hapus laporan ini beserta semua tanggapannya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="flex h-full w-20 flex-col items-center justify-center gap-1 rounded-2xl border border-red-300 bg-red-50 text-[9px] font-bold text-red-600 hover:bg-red-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        HAPUS<br>LAPORAN
                    </button>
                </form>

                <div class="flex-1 overflow-hidden rounded-2xl border-t-4 border-blue-500 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-extrabold text-[#0c203b]">{{ $report->ticket_code }}</span>
                                <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">Laporan Saya</span>
                            </div>
                            <h2 class="mt-2 text-xl font-extrabold text-[#0c203b]">{{ $report->title }}</h2>
                            <p class="mt-1 text-xs text-slate-500">{{ $report->address }}</p>
                        </div>
                        <div class="text-right">
                            <span class="rounded-lg bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                {{ $report->item_kind === 'penemuan' ? '✋ Ditemukan' : '🔍 Dicari' }}
                            </span>
                            <p class="mt-2 text-[10px] text-slate-500">
                                Diajukan: {{ $report->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB ({{ $report->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DAFTAR TANGGAPAN --}}
            <div class="mt-5 space-y-4">
                @forelse ($claims as $claim)
                @php [$label, $badge] = $claim->reporter_label; @endphp
                <a href="{{ route('laporan.barang.tanggapan', [$report, $claim]) }}"
                    class="block overflow-hidden rounded-2xl border-t-4 border-blue-500 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-extrabold text-[#0c203b]">Tanggapan {{ $claim->nomor }}</h3>
                            <span class="rounded-md px-2 py-0.5 text-[9px] font-bold uppercase {{ $badge }}">{{ $label }}</span>
                        </div>
                        <p class="text-[10px] text-slate-500">
                            Diajukan: {{ $claim->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB ({{ $claim->created_at->diffForHumans() }})
                        </p>
                    </div>
                </a>
                @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center text-sm text-slate-500">
                    Belum ada yang menanggapi laporan ini. Anda akan mendapat notifikasi saat ada yang mengklaim.
                </div>
                @endforelse
            </div>
        </div>
    </main>
</x-public-layout>