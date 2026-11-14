<x-public-layout title="Laporan Saya - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">

            <a href="{{ route('laporan.barang.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">← Kembali</a>

            <div class="mb-6 mt-6 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">Laporan Saya</h1>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
            @endif

            {{-- FILTER --}}
            <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto_auto]">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Laporan"
                    class="rounded-xl border border-slate-200 bg-[#f0f4ff]/60 px-4 py-3 text-xs focus:border-blue-600 focus:outline-none">
                <select name="category_id" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs font-semibold">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs font-semibold">
                    <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>Paling Terbaru</option>
                    <option value="terlama" @selected(request('sort') === 'terlama')>Paling Lama</option>
                </select>
            </form>

            {{-- TAB --}}
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @foreach ([['semua', 'Semua'], ['milik', 'Laporan Saya'], ['menanggapi', 'Menanggapi']] as [$key, $label])
                    <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                        class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[11px] font-bold {{ $tab === $key ? 'bg-[#0c203b] text-white' : 'bg-blue-50 text-blue-700' }}">
                        {{ $label }}
                        <span class="rounded-full bg-white/90 px-1.5 text-[10px] text-slate-700">{{ $counts[$key] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- LIST --}}
            <div class="mt-5 space-y-4">
                @forelse ($items as $item)
                    <a href="{{ $item['url'] }}" class="block overflow-hidden rounded-2xl border-t-4 border-blue-500 bg-white p-5 shadow-sm transition hover:shadow-md">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-base font-extrabold text-[#0c203b]">{{ $item['kode'] }}</span>
                                    <span class="rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">
                                        {{ $item['jenis'] === 'milik' ? 'Laporan Saya' : 'Menanggapi' }}
                                    </span>
                                    @if ($item['baru'] > 0)
                                        <span class="rounded-md bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-600">{{ $item['baru'] }} tanggapan baru</span>
                                    @endif
                                </div>
                                <h3 class="mt-2 text-xl font-extrabold text-[#0c203b]">{{ $item['judul'] }}</h3>
                            </div>
                            <div class="text-right">
                                @if ($item['kind'] === 'penemuan')
                                    <span class="rounded-lg bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">✋ Ditemukan</span>
                                @else
                                    <span class="rounded-lg bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600">🔍 Dicari</span>
                                @endif
                                <p class="mt-2 text-[10px] text-slate-500">
                                    Diajukan: {{ $item['waktu']->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB ({{ $item['waktu']->diffForHumans() }})
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center text-sm text-slate-500">
                        Belum ada laporan.
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</x-public-layout>