@php
    // Set false kalau tidak ingin foto barang temuan/hilang tampil di publik
    // (misalnya khawatir fotonya berisi KTP, ATM, atau dokumen pribadi).
    $tampilFotoBarang = true;

    $foto = fn ($path) => $path ? asset('storage/' . $path) : null;
@endphp

<x-public-layout title="Dokumentasi Penanganan - LaporKita">
    <main class="min-h-screen bg-[#f8fafc]">

        {{-- HEADER --}}
        <section class="bg-white py-10 md:py-14">
            <div class="mx-auto max-w-5xl px-6">
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b] sm:text-4xl">Dokumentasi Penanganan</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">
                    Lihat berbagai laporan masyarakat yang telah ditangani dan diselesaikan.
                </p>

                {{-- TAB JENIS + PENCARIAN --}}
                <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach (['semua' => 'Semua', 'infrastruktur' => 'Infrastruktur', 'barang' => 'Barang Hilang'] as $key => $label)
                            <a href="{{ route('dokumentasi.index', array_filter(['jenis' => $key, 'search' => request('search')])) }}"
                                class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold transition
                                {{ $jenis === $key ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $label }}
                                <span class="rounded-full px-1.5 text-[10px] {{ $jenis === $key ? 'bg-white/25 text-white' : 'bg-white text-slate-500' }}">{{ $counts[$key] }}</span>
                            </a>
                        @endforeach
                    </div>

                    <form method="GET" class="w-full sm:w-72">
                        <input type="hidden" name="jenis" value="{{ $jenis }}">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau lokasi..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-medium text-slate-700 focus:border-blue-600 focus:bg-white focus:outline-none">
                    </form>
                </div>
            </div>
        </section>

        {{-- DAFTAR DOKUMENTASI --}}
        <section class="mx-auto max-w-5xl px-6 py-8">
            <div class="space-y-6">
                @forelse ($reports as $r)
                    @php
                        $isInfra = $r->type === 'infrastruktur';
                        $sebelum = $foto(is_array($r->photos) && count($r->photos) ? $r->photos[0] : $r->photo);
                        $sesudah = $foto($r->photo_after);
                        $fotoBarang = $foto($r->photo);
                    @endphp

                    <article class="overflow-hidden rounded-3xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
                        <h2 class="text-base font-bold text-[#0c203b]">
                            {{ $isInfra ? $r->title : 'Penyelesaian Laporan Barang Hilang: ' . $r->title }}
                        </h2>

                        {{-- GAMBAR --}}
                        <div class="mt-4">
                            @if ($isInfra)
                                @if ($sebelum && $sesudah)
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        <div class="relative overflow-hidden rounded-2xl bg-slate-100">
                                            <img src="{{ $sebelum }}" alt="Sebelum dikerjakan" loading="lazy" class="h-64 w-full object-cover">
                                            <span class="absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-black/55 px-3 py-1 text-[11px] font-bold text-white backdrop-blur">Sebelum Dikerjakan</span>
                                        </div>
                                        <div class="relative overflow-hidden rounded-2xl bg-slate-100">
                                            <img src="{{ $sesudah }}" alt="Setelah dikerjakan" loading="lazy" class="h-64 w-full object-cover">
                                            <span class="absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-black/55 px-3 py-1 text-[11px] font-bold text-white backdrop-blur">Setelah Dikerjakan</span>
                                        </div>
                                    </div>
                                @elseif ($sesudah || $sebelum)
                                    <div class="relative overflow-hidden rounded-2xl bg-slate-100">
                                        <img src="{{ $sesudah ?? $sebelum }}" alt="{{ $r->title }}" loading="lazy" class="h-72 w-full object-cover">
                                        <span class="absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-black/55 px-3 py-1 text-[11px] font-bold text-white backdrop-blur">
                                            {{ $sesudah ? 'Setelah Dikerjakan' : 'Kondisi Awal' }}
                                        </span>
                                    </div>
                                @endif
                            @elseif ($tampilFotoBarang && $fotoBarang)
                                <div class="overflow-hidden rounded-2xl bg-slate-100">
                                    <img src="{{ $fotoBarang }}" alt="{{ $r->title }}" loading="lazy" class="h-72 w-full object-cover">
                                </div>
                            @else
                                <div class="flex h-40 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-50 to-slate-50 text-center">
                                    <div>
                                        <div class="text-4xl">🤝</div>
                                        <p class="mt-1 text-xs font-bold text-slate-500">Barang berhasil dikembalikan ke pemiliknya</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($isInfra && $r->completion_notes)
                            <p class="mt-4 text-xs leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit($r->completion_notes, 220) }}</p>
                        @endif

                        {{-- FOOTER --}}
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-4">
                            <p class="text-[11px] font-medium text-slate-500">
                                {{ $isInfra ? 'Infrastruktur' : 'Barang Hilang' }}
                                @if ($r->category) · {{ $r->category->name }} @endif
                                @if ($r->address) · {{ $r->address }} @endif
                                · <span class="text-slate-400">{{ $r->updated_at->translatedFormat('d F Y') }}</span>
                            </p>
                            <span class="rounded-md bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-600">Selesai</span>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-white p-14 text-center text-sm text-slate-500">
                        Belum ada dokumentasi penanganan yang dapat ditampilkan.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">{{ $reports->links() }}</div>
        </section>
    </main>
</x-public-layout>