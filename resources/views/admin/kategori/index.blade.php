<x-admin-layout title="Kategori - Admin LaporKita">

    <div class="mb-6 flex items-center gap-3">
        <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Kategori</h1>
            <p class="text-xs text-slate-500">Kelola kategori yang dipilih warga saat membuat laporan.</p>
        </div>
    </div>

    @if (session('success')) <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">{{ session('error') }}</div> @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    {{-- TAMBAH --}}
    <form method="POST" action="{{ route('admin.kategori.store') }}" class="grid grid-cols-1 gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm md:grid-cols-[200px_1fr_auto]">
        @csrf
        <select name="type" required class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-semibold">
            @foreach ($tipe as $key => $label)
                <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Nama kategori baru"
            class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs focus:border-blue-600 focus:bg-white focus:outline-none">
        <button class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-blue-700">+ Tambah</button>
    </form>

    {{-- DAFTAR PER JENIS --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        @foreach ($tipe as $key => $label)
            @php $daftar = $kategori->get($key, collect()); @endphp
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#0c203b]">{{ $label }}</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600">{{ $daftar->count() }} kategori</span>
                </div>

                <ul class="mt-4 divide-y divide-slate-100">
                    @forelse ($daftar as $k)
                        @php $dipakai = $jumlah[$k->id] ?? 0; @endphp
                        <li class="py-3" x-data="{ edit: false }">
                            <div class="flex items-center justify-between gap-3" x-show="!edit">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $k->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $dipakai }} laporan</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-3 text-[11px] font-bold">
                                    <button type="button" @click="edit = true" class="text-blue-600 hover:underline">Ubah</button>
                                    @if ($dipakai === 0)
                                        <form method="POST" action="{{ route('admin.kategori.destroy', $k) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button class="text-rose-600 hover:underline">Hapus</button>
                                        </form>
                                    @else
                                        <span class="text-slate-300" title="Masih dipakai laporan">Hapus</span>
                                    @endif
                                </div>
                            </div>

                            <form x-show="edit" x-cloak method="POST" action="{{ route('admin.kategori.update', $k) }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <input type="text" name="name" value="{{ $k->name }}" required maxlength="100"
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:border-blue-600 focus:outline-none">
                                <button class="rounded-lg bg-blue-600 px-3 py-2 text-[11px] font-bold text-white">Simpan</button>
                                <button type="button" @click="edit = false" class="rounded-lg bg-slate-100 px-3 py-2 text-[11px] font-bold text-slate-600">Batal</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-8 text-center text-xs text-slate-400">Belum ada kategori.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>
</x-admin-layout>