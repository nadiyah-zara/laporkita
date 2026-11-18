@php 
    $field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20'; 
@endphp

<x-admin-layout title="Pengaturan Profil - Admin LaporKita">

    {{-- HEADER HALAMAN --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-sm">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Profil Administrator</h1>
                <p class="text-xs text-slate-500">Kelola identitas akun dan keamanan kata sandi Anda.</p>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-700 shadow-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- FORM 1: DATA AKUN --}}
        <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3.5">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-[#0066FF]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900">Informasi Pribadi</h2>
            </div>

            <form method="POST" action="{{ route('admin.profil.update') }}" class="mt-4 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Nama Lengkap</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Alamat Email</label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="{{ $field }}">
                </div>
                <button class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </form>
        </section>

        {{-- FORM 2: GANTI PASSWORD --}}
        <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm" x-data="{ lihat: false }">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-slate-900">Perbarui Kata Sandi</h2>
                </div>

                {{-- Toggle Show/Hide Password --}}
                <button type="button" @click="lihat = !lihat"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#0066FF]">
                    <svg x-show="!lihat" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="lihat" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                    <span x-text="lihat ? 'Sembunyikan' : 'Perlihatkan'"></span>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.profil.password') }}" class="mt-4 space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Password Saat Ini</label>
                    <input :type="lihat ? 'text' : 'password'" name="current_password" required autocomplete="current-password" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Password Baru (min. 8 karakter)</label>
                    <input :type="lihat ? 'text' : 'password'" name="password" required autocomplete="new-password" class="{{ $field }}">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700">Konfirmasi Password Baru</label>
                    <input :type="lihat ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" class="{{ $field }}">
                </div>

                <button class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 active:scale-95">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Ganti Kata Sandi</span>
                </button>
            </form>
        </section>
    </div>
</x-admin-layout>