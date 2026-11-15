@php $field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none'; @endphp

<x-admin-layout title="Profil Admin - LaporKita">

    <div class="mb-6 flex items-center gap-3">
        <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Profil Admin</h1>
            <p class="text-xs text-slate-500">Ubah data akun dan ganti password Anda.</p>
        </div>
    </div>

    @if (session('success')) <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div> @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
            @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">

        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-[#0c203b]">Data Akun</h2>
            <form method="POST" action="{{ route('admin.profil.update') }}" class="mt-4 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Nama</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Email</label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Nomor HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="{{ $field }}">
                </div>
                <button class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white hover:bg-blue-700">Simpan Perubahan</button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-[#0c203b]">Ganti Password</h2>
            <form method="POST" action="{{ route('admin.profil.password') }}" class="mt-4 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Password saat ini</label>
                    <input type="password" name="current_password" required autocomplete="current-password" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Password baru (min. 8 karakter)</label>
                    <input type="password" name="password" required autocomplete="new-password" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Ulangi password baru</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="{{ $field }}">
                </div>
                <button class="rounded-xl bg-[#0c203b] px-6 py-2.5 text-xs font-bold text-white hover:bg-slate-800">Ganti Password</button>
            </form>
        </section>
    </div>
</x-admin-layout>