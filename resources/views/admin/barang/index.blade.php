@php
    $tabs = ['semua' => 'Semua', 'dicari' => 'Dicari', 'ditemukan' => 'Ditemukan', 'selesai' => 'Selesai', 'disembunyikan' => 'Disembunyikan'];
@endphp

<x-admin-layout title="Barang Hilang - Admin LaporKita">

    <div class="mb-6 flex items-center gap-3">
        <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Barang Hilang & Ditemukan</h1>
            <p class="text-xs text-slate-500">Moderasi laporan antarwarga. Turunkan laporan yang spam atau memuat data pribadi.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    {{-- TAB --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.barang.index', array_filter(['tab' => $key === 'semua' ? null : $key, 'search' => request('search')])) }}"
                class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[11px] font-bold {{ $tab === $key ? 'bg-[#0c203b] text-white' : 'bg-white text-slate-600 shadow-sm hover:bg-slate-50' }}">
                {{ $label }}
                <span class="rounded-full px-1.5 text-[10px] {{ $tab === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    {{-- PENCARIAN --}}
    <form method="GET" class="mt-4 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
        <input type="hidden" name="tab" value="{{ $tab === 'semua' ? '' : $tab }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, kode tiket, atau alamat..."
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-blue-600 focus:bg-white focus:outline-none">
    </form>

    {{-- TABEL --}}
    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table class="w-full min-w-[760px] text-left text-xs">
            <thead class="border-b border-slate-100 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Tiket</th>
                    <th class="px-5 py-3">Laporan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggapan</th>
                    <th class="px-5 py-3">Masuk</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($reports as $r)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-4 align-top">
                            <p class="font-extrabold text-[#0c203b]">{{ $r->ticket_code }}</p>
                            @if ($r->item_kind === 'penemuan')
                                <span class="mt-1 inline-block rounded-md bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">Ditemukan</span>
                            @else
                                <span class="mt-1 inline-block rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">Dicari</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-top">
                            <p class="font-bold text-slate-800">{{ $r->title }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-400">{{ $r->category?->name }} · {{ \Illuminate\Support\Str::limit($r->address, 45) }}</p>
                            <p class="text-[11px] text-slate-400">oleh {{ $r->user?->name ?? $r->reporter_name }}</p>
                        </td>
                        <td class="px-5 py-4 align-top">
                            @if ($r->hidden_at)
                                <span class="rounded-md bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-600">Disembunyikan</span>
                            @elseif (in_array($r->status, ['resolved', 'done']))
                                <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-600">Selesai</span>
                            @else
                                <span class="rounded-md bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-600">Aktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-top font-bold text-slate-700">{{ $r->claims_count }}</td>
                        <td class="px-5 py-4 align-top text-[11px] text-slate-500">{{ $r->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-4 text-right align-top">
                            <a href="{{ route('admin.barang.show', $r) }}" class="rounded-lg bg-blue-600 px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-blue-700">Tinjau</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-14 text-center text-slate-400">Tidak ada laporan pada filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $reports->links() }}</div>
</x-admin-layout>