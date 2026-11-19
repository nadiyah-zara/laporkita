<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Login Admin - LaporKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative flex min-h-screen items-center justify-center bg-slate-800 bg-cover bg-center bg-no-repeat p-4"
      style="background-image: url('{{ asset('images/hero-bg.png') }}');">

    {{-- Lapisan gelap tipis supaya kartu login tetap jelas di atas foto --}}
    <div class="absolute inset-0 bg-slate-900/50"></div>

    <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-4">
            <h2 class="text-2xl font-bold text-white">Masuk Admin</h2>
        </div>

        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4 p-6">
            @csrf

            <div>
                <label class="text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" required autofocus
                       value="{{ old('email') }}"
                       placeholder="Masukkan Email Admin"
                       class="mt-1 w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ show: false }">
                <label class="text-sm font-medium text-slate-700">Kata Sandi</label>
                <div class="relative mt-1">
                    <input :type="show ? 'text' : 'password'" name="password" required
                           placeholder="Masukkan Kata Sandi"
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm focus:border-blue-600 focus:ring-blue-600">
                    <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-3 text-xs font-semibold text-slate-500 hover:text-blue-700"
                            x-text="show ? 'Sembunyi' : 'Lihat'"></button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <a href="{{ route('password.request') }}" class="inline-block text-sm text-blue-600 hover:underline">Lupa kata sandi?</a>

            <button type="submit"
                    class="w-full rounded-lg bg-blue-700 py-3 text-sm font-bold text-white hover:bg-blue-800">
                Masuk
            </button>
        </form>
    </div>

</body>
</html>
