<x-admin-layout title="Kategori Laporan - Admin LaporKita">

    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Kategori</h1>
                <p class="text-xs text-slate-500">Atur kategori yang tersedia bagi masyarakat saat membuat laporan baru.</p>
            </div>
        </div>
    </div>

    {{-- ALERT SUKSES / ERROR --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-800 shadow-sm">
            <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-700 shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULIR TAMBAH KATEGORI --}}
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
        <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Tambah Kategori Baru</h2>
        <form method="POST" action="{{ route('admin.kategori.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-[220px_1fr_auto]">
            @csrf
            <div>
                <select name="type" required 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition focus:border-[#0066FF] focus:bg-white focus:outline-none">
                    @foreach ($tipe as $key => $label)
                        <option value="{{ $key }}" @selected(old('type') === $key)>Tipe: {{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Ketik nama kategori (contoh: Jalan Rusak, Dompet, dsb)..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-2.5 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
            </div>
            <button class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-[#0066FF] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kategori</span>
            </button>
        </form>
    </section>

    {{-- DAFTAR KATEGORI PER JENIS --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        @foreach ($tipe as $key => $label)
            @php $daftar = $kategori->get($key, collect()); @endphp
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-[#0066FF]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-slate-900">{{ $label }}</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600">
                        {{ $daftar->count() }} kategori
                    </span>
                </div>

                <ul class="mt-3 divide-y divide-slate-100">
                    @forelse ($daftar as $k)
                        @php $dipakai = $jumlah[$k->id] ?? 0; @endphp
                        <li class="py-3" x-data="{ edit: false }">
                            {{-- View Normal --}}
                            <div class="flex items-center justify-between gap-3" x-show="!edit">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-bold text-slate-800">{{ $k->name }}</p>
                                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $dipakai }} laporan terhubung</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-3 text-xs font-semibold">
                                    <button type="button" @click="edit = true" class="text-[#0066FF] hover:underline">Ubah</button>
                                    @if ($dipakai === 0)
                                        <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-rose-600 hover:underline">Hapus</button>
                                        </form>
                                    @else
                                        <span class="cursor-not-allowed text-slate-300" title="Kategori tidak bisa dihapus karena masih digunakan oleh laporan aktif.">Hapus</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Form Inline Edit --}}
                            <form x-show="edit" x-cloak method="POST" action="{{ route('admin.kategori.update', $k) }}" class="flex items-center gap-2 pt-1">
                                @csrf @method('PATCH')
                                <input type="text" name="name" value="{{ $k->name }}" required maxlength="100"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-1.5 text-xs text-slate-800 focus:border-[#0066FF] focus:bg-white focus:outline-none">
                                <button class="rounded-xl bg-[#0066FF] px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-blue-700">Simpan</button>
                                <button type="button" @click="edit = false" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-8 text-center text-xs text-slate-400">Belum ada kategori yang ditambahkan.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>
</x-admin-layout>