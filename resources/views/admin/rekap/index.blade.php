@php
    $namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $persen = fn ($a, $b) => $b > 0 ? round($a / $b * 100) . '%' : '-';
@endphp

<x-admin-layout title="Rekapitulasi Laporan {{ $tahun }} - Admin LaporKita">

    {{-- HEADER & AKSI EKSPOR --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekapitulasi Laporan {{ $tahun }}</h1>
                <p class="text-xs text-slate-500">Data terupdate dicetak pada {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 print:hidden">
            {{-- Pilihan Tahun --}}
            <form method="GET">
                <select name="tahun" onchange="this.form.submit()" 
                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition focus:border-[#0066FF] focus:outline-none">
                    @foreach ($tahunList as $t)
                        <option value="{{ $t }}" @selected($t === $tahun)>Tahun {{ $t }}</option>
                    @endforeach
                </select>
            </form>

            {{-- Ekspor CSV --}}
            <a href="{{ route('admin.rekap.ekspor', ['tahun' => $tahun]) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Ekspor Excel (CSV)</span>
            </a>

            {{-- Cetak / PDF --}}
            <button type="button" onclick="window.print()" 
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Dokumen</span>
            </button>
        </div>
    </div>

    {{-- KARTU RINGKASAN METRIK --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        {{-- Card 1 --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Masuk</span>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-slate-900">{{ $total['infra_masuk'] + $total['barang_masuk'] }}</span>
                <span class="text-xs font-medium text-slate-400">laporan</span>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Infra Selesai</span>
                <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                    {{ $persen($total['infra_selesai'], $total['infra_masuk']) }}
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-extrabold text-emerald-600">{{ $total['infra_selesai'] }}</span>
                <span class="text-xs font-semibold text-slate-400">/ {{ $total['infra_masuk'] }} total</span>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Durasi Rata-rata</span>
            <div class="mt-4 flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-indigo-600">{{ $rataHari !== null ? round($rataHari, 1) : '-' }}</span>
                <span class="text-xs font-medium text-slate-400">hari penanganan</span>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Barang Kembali</span>
                <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-[#0066FF]">
                    {{ $persen($total['barang_selesai'], $total['barang_masuk']) }}
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-extrabold text-[#0066FF]">{{ $total['barang_selesai'] }}</span>
                <span class="text-xs font-semibold text-slate-400">/ {{ $total['barang_masuk'] }} total</span>
            </div>
        </div>
    </div>

    {{-- TABEL LAPORAN PER BULAN --}}
    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h2 class="text-sm font-bold text-slate-900">Statistik Bulanan</h2>
            <p class="text-[11px] text-slate-400">Rincian masuk dan penyelesaian laporan tiap bulan pada tahun {{ $tahun }}.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-xs">
                <thead class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Bulan</th>
                        <th class="px-5 py-3.5 text-right">Infrastruktur Masuk</th>
                        <th class="px-5 py-3.5 text-right">Infrastruktur Selesai</th>
                        <th class="px-5 py-3.5 text-right">Barang Masuk</th>
                        <th class="px-5 py-3.5 text-right">Barang Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($bulan as $i => $b)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $namaBulan[$i] }}</td>
                            <td class="px-5 py-3 text-right font-medium text-slate-700">{{ $b['infra_masuk'] }}</td>
                            <td class="px-5 py-3 text-right font-bold text-emerald-600">{{ $b['infra_selesai'] }}</td>
                            <td class="px-5 py-3 text-right font-medium text-slate-700">{{ $b['barang_masuk'] }}</td>
                            <td class="px-5 py-3 text-right font-bold text-emerald-600">{{ $b['barang_selesai'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-200 bg-slate-50/60 font-extrabold text-slate-900">
                    <tr>
                        <td class="px-5 py-3.5">Total Keseluruhan</td>
                        <td class="px-5 py-3.5 text-right">{{ $total['infra_masuk'] }}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-600">{{ $total['infra_selesai'] }}</td>
                        <td class="px-5 py-3.5 text-right">{{ $total['barang_masuk'] }}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-600">{{ $total['barang_selesai'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="p-4 bg-slate-50/40 border-t border-slate-100">
            <p class="text-[11px] text-slate-400">Catatan: Kolom "Selesai" dihitung dari laporan yang masuk di bulan tersebut dan telah tuntas. Laporan yang disembunyikan/dibatalkan tidak dikalkulasikan.</p>
        </div>
    </section>

    {{-- TABEL LAPORAN PER KATEGORI --}}
    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h2 class="text-sm font-bold text-slate-900">Statistik Berdasarkan Kategori</h2>
            <p class="text-[11px] text-slate-400">Efektivitas penyelesaian berdasarkan jenis keluhan masyarakat.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px] text-left text-xs">
                <thead class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Nama Kategori</th>
                        <th class="px-5 py-3.5">Jenis Layanan</th>
                        <th class="px-5 py-3.5 text-right">Total Masuk</th>
                        <th class="px-5 py-3.5 text-right">Total Selesai</th>
                        <th class="px-5 py-3.5 text-right">Persentase Sukses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($perKategori as $k)
                        <tr class="transition-colors hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $k->nama }}</td>
                            <td class="px-5 py-3 text-slate-500">
                                <span class="inline-block rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                                    {{ $k->tipe === 'infrastruktur' ? 'Infrastruktur' : 'Barang Hilang' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-slate-700">{{ $k->total }}</td>
                            <td class="px-5 py-3 text-right font-bold text-emerald-600">{{ $k->selesai }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-900">{{ $persen($k->selesai, $k->total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">Belum ada data pada tahun ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-admin-layout>