<x-public-layout title="Histori Laporan - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            <a href="{{ route('beranda') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">← Kembali</a>

            <div class="mb-2 mt-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">Histori Laporan</h1>
            </div>
            <p class="mb-6 ml-5 text-xs text-slate-500">Arsip laporan dan tanggapan Anda yang sudah selesai atau ditutup.</p>

            {{-- RINGKASAN --}}
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                @foreach ([
                    ['Total Selesai', $ringkasan['selesai'], 'text-emerald-600'],
                    ['Infrastruktur', $ringkasan['infrastruktur'], 'text-blue-600'],
                    ['Barang Dikembalikan', $ringkasan['barang'], 'text-blue-600'],
                    ['Ditolak', $ringkasan['ditolak'], 'text-rose-600'],
                ] as [$label, $angka, $warna])
                    <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                        <p class="text-2xl font-extrabold {{ $warna }}">{{ $angka }}</p>
                        <p class="mt-0.5 text-[11px] font-semibold text-slate-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            {{-- FILTER --}}
            <form method="GET" class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto]">
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau kode tiket..."
                    class="rounded-xl border border-slate-200 bg-[#f0f4ff]/60 px-4 py-3 text-xs focus:border-blue-600 focus:outline-none">
                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs font-semibold">
                    <option value="semua" @selected($status === 'semua')>Semua Status</option>
                    <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                    <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
                </select>
            </form>

            {{-- TAB JENIS --}}
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @foreach (['semua' => 'Semua', 'infrastruktur' => 'Infrastruktur', 'barang' => 'Barang Hilang'] as $key => $label)
                    <a href="{{ route('histori.index', array_filter(['jenis' => $key, 'status' => $status !== 'semua' ? $status : null, 'search' => request('search')])) }}"
                        class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[11px] font-bold {{ $jenis === $key ? 'bg-[#0c203b] text-white' : 'bg-blue-50 text-blue-700' }}">
                        {{ $label }}
                        <span class="rounded-full bg-white/90 px-1.5 text-[10px] text-slate-700">{{ $counts[$key] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- DAFTAR --}}
            <div class="mt-5 space-y-4">
                @forelse ($items as $item)
                    <a href="{{ $item['url'] }}" class="block overflow-hidden rounded-2xl border-t-4 {{ $item['status'] === 'selesai' ? 'border-emerald-500' : 'border-rose-400' }} bg-white p-5 shadow-sm transition hover:shadow-md">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-base font-extrabold text-[#0c203b]">{{ $item['kode'] }}</span>
                                    <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                                        {{ $item['peran'] === 'milik' ? 'Laporan Saya' : 'Menanggapi' }}
                                    </span>
                                </div>
                                <h3 class="mt-2 text-lg font-extrabold text-[#0c203b]">{{ $item['judul'] }}</h3>
                                @if ($item['kategori'])
                                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $item['kategori'] }}</p>
                                @endif
                            </div>

                            <div class="text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($item['jenis'] === 'infrastruktur')
                                        <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">🏗️ Infrastruktur</span>
                                    @elseif ($item['kind'] === 'penemuan')
                                        <span class="rounded-lg bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">✋ Ditemukan</span>
                                    @else
                                        <span class="rounded-lg bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600">🔍 Dicari</span>
                                    @endif

                                    @if ($item['status'] === 'selesai')
                                        <span class="rounded-lg bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">Selesai</span>
                                    @else
                                        <span class="rounded-lg bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600">Ditolak</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-[10px] text-slate-500">
                                    {{ $item['status'] === 'selesai' ? 'Selesai' : 'Ditutup' }}:
                                    {{ $item['waktu']->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    ({{ $item['waktu']->diffForHumans() }})
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center text-sm text-slate-500">
                        Belum ada histori. Laporan yang sudah selesai atau ditutup akan muncul di sini.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $items->links() }}</div>
        </div>
    </main>
</x-public-layout>