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

        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm" x-data="{ lihat: false }">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#0c203b]">Ganti Password</h2>
                <button type="button" @click="lihat = !lihat"
                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-blue-600">
                    {{-- mata terbuka --}}
                    <svg x-show="!lihat" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{-- mata dicoret --}}
                    <svg x-show="lihat" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <span x-text="lihat ? 'Sembunyikan' : 'Tampilkan'"></span>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.profil.password') }}" class="mt-4 space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Password saat ini</label>
                    <input :type="lihat ? 'text' : 'password'" name="current_password" required autocomplete="current-password" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Password baru (min. 8 karakter)</label>
                    <input :type="lihat ? 'text' : 'password'" name="password" required autocomplete="new-password" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Ulangi password baru</label>
                    <input :type="lihat ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" class="{{ $field }}">
                </div>

                <button class="rounded-xl bg-[#0c203b] px-6 py-2.5 text-xs font-bold text-white hover:bg-slate-800">Ganti Password</button>
            </form>
        </section>
    </div>
</x-admin-layout>