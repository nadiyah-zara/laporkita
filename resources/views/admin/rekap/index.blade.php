@php
    $namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $persen = fn ($a, $b) => $b > 0 ? round($a / $b * 100) . '%' : '-';
@endphp

<x-admin-layout title="Rekap {{ $tahun }} - Admin LaporKita">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Rekap Laporan {{ $tahun }}</h1>
                <p class="text-xs text-slate-500">LaporKita · dicetak {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 print:hidden">
            <form method="GET">
                <select name="tahun" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold">
                    @foreach ($tahunList as $t)
                        <option value="{{ $t }}" @selected($t === $tahun)>{{ $t }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.rekap.ekspor', ['tahun' => $tahun]) }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">⬇ Ekspor CSV (Excel)</a>
            <button type="button" onclick="window.print()" class="rounded-xl bg-[#0c203b] px-4 py-2 text-xs font-bold text-white hover:bg-slate-800">🖨 Cetak / Simpan PDF</button>
        </div>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Total Laporan Masuk</p>
            <p class="mt-2 text-3xl font-extrabold text-[#0c203b]">{{ $total['infra_masuk'] + $total['barang_masuk'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Infrastruktur Selesai</p>
            <p class="mt-2 text-3xl font-extrabold text-emerald-600">{{ $total['infra_selesai'] }}<span class="text-sm font-bold text-slate-400"> / {{ $total['infra_masuk'] }}</span></p>
            <p class="text-[11px] text-slate-400">{{ $persen($total['infra_selesai'], $total['infra_masuk']) }} tuntas</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Rata-rata Penyelesaian</p>
            <p class="mt-2 text-3xl font-extrabold text-indigo-600">{{ $rataHari !== null ? round($rataHari, 1) : '-' }}<span class="text-sm font-bold text-slate-400"> hari</span></p>
            <p class="text-[11px] text-slate-400">laporan infrastruktur</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Barang Dikembalikan</p>
            <p class="mt-2 text-3xl font-extrabold text-blue-600">{{ $total['barang_selesai'] }}<span class="text-sm font-bold text-slate-400"> / {{ $total['barang_masuk'] }}</span></p>
            <p class="text-[11px] text-slate-400">{{ $persen($total['barang_selesai'], $total['barang_masuk']) }} berhasil</p>
        </div>
    </div>

    {{-- PER BULAN --}}
    <section class="mt-6 overflow-x-auto rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-bold text-[#0c203b]">Per Bulan</h2>
        <table class="mt-4 w-full min-w-[640px] text-left text-xs">
            <thead class="border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="py-2 pr-4">Bulan</th>
                    <th class="py-2 pr-4 text-right">Infrastruktur Masuk</th>
                    <th class="py-2 pr-4 text-right">Infrastruktur Selesai</th>
                    <th class="py-2 pr-4 text-right">Barang Masuk</th>
                    <th class="py-2 text-right">Barang Selesai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($bulan as $i => $b)
                    <tr>
                        <td class="py-2.5 pr-4 font-semibold text-slate-700">{{ $namaBulan[$i] }}</td>
                        <td class="py-2.5 pr-4 text-right">{{ $b['infra_masuk'] }}</td>
                        <td class="py-2.5 pr-4 text-right text-emerald-600">{{ $b['infra_selesai'] }}</td>
                        <td class="py-2.5 pr-4 text-right">{{ $b['barang_masuk'] }}</td>
                        <td class="py-2.5 text-right text-emerald-600">{{ $b['barang_selesai'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t-2 border-slate-200 font-extrabold text-[#0c203b]">
                <tr>
                    <td class="py-2.5 pr-4">Total</td>
                    <td class="py-2.5 pr-4 text-right">{{ $total['infra_masuk'] }}</td>
                    <td class="py-2.5 pr-4 text-right">{{ $total['infra_selesai'] }}</td>
                    <td class="py-2.5 pr-4 text-right">{{ $total['barang_masuk'] }}</td>
                    <td class="py-2.5 text-right">{{ $total['barang_selesai'] }}</td>
                </tr>
            </tfoot>
        </table>
        <p class="mt-3 text-[10px] text-slate-400">"Selesai" dihitung dari laporan yang masuk pada bulan itu dan kini sudah tuntas. Laporan yang disembunyikan admin tidak dihitung.</p>
    </section>

    {{-- PER KATEGORI --}}
    <section class="mt-6 overflow-x-auto rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-bold text-[#0c203b]">Per Kategori</h2>
        <table class="mt-4 w-full min-w-[480px] text-left text-xs">
            <thead class="border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="py-2 pr-4">Kategori</th>
                    <th class="py-2 pr-4">Jenis</th>
                    <th class="py-2 pr-4 text-right">Masuk</th>
                    <th class="py-2 pr-4 text-right">Selesai</th>
                    <th class="py-2 text-right">Tingkat Selesai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($perKategori as $k)
                    <tr>
                        <td class="py-2.5 pr-4 font-semibold text-slate-700">{{ $k->nama }}</td>
                        <td class="py-2.5 pr-4 text-slate-500">{{ $k->tipe === 'infrastruktur' ? 'Infrastruktur' : 'Barang Hilang' }}</td>
                        <td class="py-2.5 pr-4 text-right">{{ $k->total }}</td>
                        <td class="py-2.5 pr-4 text-right text-emerald-600">{{ $k->selesai }}</td>
                        <td class="py-2.5 text-right font-bold">{{ $persen($k->selesai, $k->total) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-slate-400">Belum ada data pada tahun ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</x-admin-layout>