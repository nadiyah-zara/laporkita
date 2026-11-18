{{-- Backdrop --}}
<div x-show="modal" x-cloak x-transition.opacity
     @keydown.escape.window="modal = ''"
     class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4">

    {{-- ===================== MODAL DAFTAR ===================== --}}
    <div x-show="modal === 'register'" @click.outside="modal = ''"
         class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">Daftar Akun</h2>
            <button type="button" @click="modal = ''"
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-xl text-white hover:bg-white/30">&times;</button>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4 p-6">
            @csrf
            <input type="hidden" name="_modal" value="register">

            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                <p class="font-bold">Mengapa kami meminta data ini?</p>
                <p class="mt-1">Layanan LaporKita mengumpulkan data pribadi pengguna sebagai jaminan keabsahan aduan, pengenal identitas, memverifikasi akun, dan mengirim notifikasi laporan.</p>
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700">Nama Lengkap<span class="text-blue-600"></span></label>
                <input type="text" name="name" required
                       value="{{ old('_modal') === 'register' ? old('name') : '' }}"
                       placeholder="Nama Lengkap Sesuai Dengan KTP"
                       class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600">
                @if (old('_modal') === 'register') @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700">Email<span class="text-blue-600"></span></label>
                <input type="email" name="email" required
                       value="{{ old('_modal') === 'register' ? old('email') : '' }}"
                       placeholder="Masukkan Email Anda"
                       class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600">
                @if (old('_modal') === 'register') @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
            </div>

            <div x-data="{ show: false }">
                <label class="text-sm font-medium text-slate-700">Kata Sandi<span class="text-blue-600"></span></label>
                <div class="relative mt-1">
                    <input :type="show ? 'text' : 'password'" name="password" required
                           placeholder="Masukkan Kata Sandi"
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500 hover:text-blue-700" x-text="show ? 'Sembunyi' : 'Lihat'"></button>
                </div>
                @if (old('_modal') === 'register') @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
            </div>

            <div x-data="{ show: false }">
                <label class="text-sm font-medium text-slate-700">Ulang Kata Sandi<span class="text-blue-600"></span></label>
                <div class="relative mt-1">
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                           placeholder="Masukkan Kata Sandi"
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500 hover:text-blue-700" x-text="show ? 'Sembunyi' : 'Lihat'"></button>
                </div>
            </div>

            <label class="flex items-center gap-3 text-sm text-slate-700">
                <input type="checkbox" required class="h-5 w-5 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                <span>Saya telah membaca dan menyetujui <a href="#" class="text-blue-600 hover:underline">Syarat dan Ketentuan Layanan</a></span>
            </label>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" @click="modal = ''" class="rounded-lg bg-slate-900 py-3 text-sm font-bold text-white hover:bg-slate-800">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-700 py-3 text-sm font-bold text-white hover:bg-blue-800">Daftar Akun</button>
            </div>

            <p class="text-center text-sm text-slate-700">
                Sudah memiliki akun?
                <a href="#" @click.prevent="modal = 'login'" class="text-blue-600 hover:underline">Masuk Sekarang</a>
            </p>
        </form>
    </div>

    {{-- ===================== MODAL MASUK ===================== --}}
    <div x-show="modal === 'login'" @click.outside="modal = ''"
         class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">Masuk</h2>
            <button type="button" @click="modal = ''"
                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/20 text-xl text-white hover:bg-white/30">&times;</button>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4 p-6">
            @csrf
            <input type="hidden" name="_modal" value="login">

            <div>
                <label class="text-sm font-medium text-slate-700">Email<span class="text-blue-600"></span></label>
                <input type="email" name="email" required
                       value="{{ old('_modal') === 'login' ? old('email') : '' }}"
                       placeholder="Masukkan Email Anda"
                       class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600">
                @if (old('_modal') === 'login') @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror @endif
            </div>

            <div x-data="{ show: false }">
                <label class="text-sm font-medium text-slate-700">Kata Sandi<span class="text-blue-600"></span></label>
                <div class="relative mt-1">
                    <input :type="show ? 'text' : 'password'" name="password" required
                           placeholder="Masukkan Kata Sandi"
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500 hover:text-blue-700" x-text="show ? 'Sembunyi' : 'Lihat'"></button>
                </div>
            </div>

            <a href="{{ route('password.request') }}" class="inline-block text-sm text-blue-600 hover:underline">Lupa kata sandi?</a>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" @click="modal = ''" class="rounded-lg bg-slate-900 py-3 text-sm font-bold text-white hover:bg-slate-800">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-700 py-3 text-sm font-bold text-white hover:bg-blue-800">Masuk</button>
            </div>

            <p class="text-center text-sm text-slate-700">
                Belum memiliki akun?
                <a href="#" @click.prevent="modal = 'register'" class="text-blue-600 hover:underline">Daftar Sekarang</a>
            </p>
        </form>
    </div>
</div>