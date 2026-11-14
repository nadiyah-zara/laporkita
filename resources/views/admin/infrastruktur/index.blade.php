@php
    $badge = [
        'pending'     => ['Menunggu Verifikasi', 'bg-amber-50 text-amber-600'],
        'verified'    => ['Terverifikasi', 'bg-blue-50 text-blue-600'],
        'in_progress' => ['Dikerjakan', 'bg-indigo-50 text-indigo-600'],
        'done'        => ['Selesai', 'bg-emerald-50 text-emerald-600'],
        'resolved'    => ['Selesai', 'bg-emerald-50 text-emerald-600'],
        'rejected'    => ['Ditolak', 'bg-rose-50 text-rose-600'],
    ];
    $tabs = ['semua' => 'Semua', 'pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'in_progress' => 'Dikerjakan', 'done' => 'Selesai', 'rejected' => 'Ditolak'];
@endphp

<x-admin-layout title="Laporan Infrastruktur - Admin LaporKita">

    <div class="mb-6 flex items-center gap-3">
        <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Laporan Infrastruktur</h1>
            <p class="text-xs text-slate-500">Periksa, tugaskan, dan pantau penanganan laporan masyarakat.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif

    {{-- TAB STATUS --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.infrastruktur.index', array_filter(['status' => $key === 'semua' ? null : $key, 'search' => request('search'), 'urgency' => request('urgency'), 'category_id' => request('category_id')])) }}"
                class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[11px] font-bold {{ $status === $key ? 'bg-[#0c203b] text-white' : 'bg-white text-slate-600 shadow-sm hover:bg-slate-50' }}">
                {{ $label }}
                <span class="rounded-full px-1.5 text-[10px] {{ $status === $key ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    {{-- FILTER --}}
    <form method="GET" class="mt-4 grid grid-cols-1 gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:grid-cols-4">
        <input type="hidden" name="status" value="{{ $status === 'semua' ? '' : $status }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, kode tiket, alamat..."
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-blue-600 focus:bg-white focus:outline-none md:col-span-2">
        <select name="urgency" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-semibold">
            <option value="">Semua Urgensi</option>
            <option value="kritis" @selected(request('urgency') === 'kritis')>Kritis</option>
            <option value="standar" @selected(request('urgency') === 'standar')>Standar</option>
        </select>
        <select name="category_id" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-semibold">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </form>

    {{-- TABEL --}}
    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table class="w-full min-w-[760px] text-left text-xs">
            <thead class="border-b border-slate-100 bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Tiket</th>
                    <th class="px-5 py-3">Laporan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Masuk</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($reports as $r)
                    @php [$label, $warna] = $badge[$r->status] ?? [$r->status, 'bg-slate-100 text-slate-600']; @endphp
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-4 align-top">
                            <p class="font-extrabold text-[#0c203b]">{{ $r->ticket_code ?? '-' }}</p>
                            @if ($r->urgency === 'kritis')
                                <span class="mt-1 inline-block rounded-md bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">KRITIS</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-top">
                            <p class="font-bold text-slate-800">{{ $r->title }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-400">{{ $r->category?->name }} · {{ \Illuminate\Support\Str::limit($r->address, 50) }}</p>
                        </td>
                        <td class="px-5 py-4 align-top">
                            <span class="rounded-md px-2.5 py-1 text-[10px] font-bold {{ $warna }}">{{ $label }}</span>
                            @if ($r->status === 'in_progress')
                                <div class="mt-2 h-1.5 w-24 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-indigo-500" style="width: {{ $r->progress_percentage ?? 0 }}%"></div>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 align-top text-[11px] text-slate-500">{{ $r->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-4 text-right align-top">
                            <a href="{{ route('admin.infrastruktur.show', $r) }}" class="rounded-lg bg-blue-600 px-3.5 py-1.5 text-[11px] font-bold text-white hover:bg-blue-700">
                                {{ $r->status === 'pending' ? 'Tinjau' : 'Detail' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-14 text-center text-slate-400">Tidak ada laporan pada filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $reports->links() }}</div>
</x-admin-layout>