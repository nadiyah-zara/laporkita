<x-admin-layout title="Dashboard - Admin LaporKita">

    <div class="mb-6 flex items-center gap-3">
        <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Dashboard</h1>
            <p class="text-xs text-slate-500">Ringkasan laporan infrastruktur dari masyarakat.</p>
        </div>
    </div>

    {{-- KARTU STATUS --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        @foreach ([
        ['Menunggu Verifikasi', $stats['pending'], 'text-amber-600', 'bg-amber-50'],
        ['Terverifikasi', $stats['verified'], 'text-blue-600', 'bg-blue-50'],
        ['Sedang Dikerjakan', $stats['in_progress'], 'text-indigo-600', 'bg-indigo-50'],
        ['Selesai', $stats['selesai'], 'text-emerald-600', 'bg-emerald-50'],
        ['Ditolak', $stats['rejected'], 'text-rose-600', 'bg-rose-50'],
        ] as [$label, $angka, $warna, $bg])
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <span class="inline-block rounded-lg {{ $bg }} px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $warna }}">{{ $label }}</span>
            <p class="mt-3 text-3xl font-extrabold {{ $warna }}">{{ $angka }}</p>
        </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">

        {{-- PERLU TINDAKAN --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#0c203b]">Perlu Diverifikasi</h2>
                <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-600">{{ $stats['pending'] }} laporan</span>
            </div>

            <div class="mt-4 divide-y divide-slate-100">
                @forelse ($butuhTindakan as $r)
                <div class="flex flex-wrap items-start justify-between gap-3 py-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-extrabold text-[#0c203b]">{{ $r->ticket_code ?? 'Tanpa kode' }}</span>
                            @if ($r->urgency === 'kritis')
                            <span class="rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">KRITIS</span>
                            @endif
                            @if ($r->category)
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">{{ $r->category->name }}</span>
                            @endif
                        </div>
                        <p class="mt-1 truncate text-sm font-bold text-slate-800">{{ $r->title }}</p>
                        <p class="truncate text-[11px] text-slate-400">{{ $r->address }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="block text-[10px] text-slate-400">{{ $r->created_at->diffForHumans() }}</span>
                        <a href="{{ route('admin.infrastruktur.show', $r) }}" class="mt-1 inline-block rounded-lg bg-blue-600 px-3 py-1 text-[11px] font-bold text-white hover:bg-blue-700">Tinjau</a>
                    </div>
                </div>
                @empty
                <p class="py-10 text-center text-xs text-slate-400">Tidak ada laporan yang menunggu verifikasi. 🎉</p>
                @endforelse
            </div>
        </section>

        {{-- BARANG HILANG --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-[#0c203b]">Barang Hilang & Ditemukan</h2>
            <p class="mt-0.5 text-[11px] text-slate-400">Berjalan antarwarga, admin hanya memantau.</p>
            <div class="mt-5 space-y-3">
                <div class="flex items-center justify-between rounded-xl bg-blue-50 px-4 py-3">
                    <span class="text-xs font-semibold text-blue-700">Laporan aktif</span>
                    <span class="text-xl font-extrabold text-blue-700">{{ $barang['aktif'] }}</span>
                    <span class="text-xs font-semibold text-rose-700">Disembunyikan</span>
                    <span class="text-xl font-extrabold text-rose-700">{{ $barang['disembunyikan'] }}</span>
                </div>
                <a href="{{ route('admin.barang.index') }}" class="mt-1 block text-center text-xs font-bold text-blue-600 hover:underline">Kelola laporan barang →</a>
                <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-4 py-3">
                    <span class="text-xs font-semibold text-emerald-700">Berhasil dikembalikan</span>
                    <span class="text-xl font-extrabold text-emerald-700">{{ $barang['selesai'] }}</span>
                </div>
            </div>
        </section>
    </div>
</x-admin-layout>