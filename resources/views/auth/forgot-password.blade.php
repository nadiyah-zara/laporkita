<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lupa Password - {{ config('app.name', 'LaporKita') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts & Tailwind CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-800 antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <div class="grid min-h-screen lg:grid-cols-12">
        
        {{-- ================================================================= --}}
        {{-- KOLOM KIRI: FORM LUPA PASSWORD                                   --}}
        {{-- ================================================================= --}}
        <div class="z-10 flex flex-col justify-between bg-white p-8 sm:p-12 lg:col-span-6 lg:p-16 xl:col-span-5">
            
            {{-- LOGO BRANDING LAPORKITA --}}
            <div>
                <a href="/" class="group inline-flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white shadow-md shadow-blue-600/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-[#0c203b]">
                        Lapor<span class="text-blue-600">Kita</span>
                    </span>
                </a>
            </div>

            {{-- FORM CONTAINER UTAMA --}}
            <div class="my-auto mx-auto w-full max-w-md py-8 lg:mx-0">
                
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b] sm:text-4xl">
                    Lupa Password
                </h1>

                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                    Masukkan alamat Email Anda yang terdaftar. Kami akan mengirimkan tautan untuk mereset password baru Anda melalui email.
                </p>

                {{-- NOTIFIKASI SUKSES (BREEZE STATUS) --}}
                @if (session('status'))
                    <div class="mt-5 flex items-center gap-2.5 rounded-xl border border-emerald-100 bg-emerald-50/90 p-4 text-xs font-semibold text-emerald-800 shadow-sm">
                        <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- FORM RESET PASSWORD --}}
                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                    @csrf

                    {{-- INPUT ALAMAT EMAIL --}}
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Alamat Email
                        </label>
                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   placeholder="contoh: nama@email.com"
                                   class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3.5 pr-4 pl-11 text-sm text-slate-800 shadow-sm transition-all placeholder:text-slate-400 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/10 @enderror" />
                        </div>
                        
                        {{-- ERROR VALIDASI --}}
                        @error('email')
                            <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    {{-- TOMBOL KIRIM (TEMA BIRU SESUAI DESAIN) --}}
                    <div>
                        <button type="submit" 
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700 hover:shadow-blue-600/40 focus:outline-none focus:ring-4 focus:ring-blue-600/20 active:scale-[0.99]">
                            <span>Kirim Tautan Reset</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- LINK KEMBALI KE LOGIN --}}
                    <div class="pt-2 text-center">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 transition-colors hover:text-blue-600">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span>Ingat password? Kembali ke Halaman Masuk</span>
                        </a>
                    </div>
                </form>

            </div>

            {{-- FOOTER BAWAH FORM --}}
            <div class="pt-6 text-xs text-slate-400">
                &copy; {{ date('Y') }} LaporKita. Portal Resmi Pengaduan & Layanan Masyarakat Digital.
            </div>

        </div>

        {{-- ================================================================= --}}
        {{-- KOLOM KANAN: VISUAL HERO TEMA BIRU (BRANDING LAPORKITA)         --}}
        {{-- ================================================================= --}}
        <div class="relative hidden overflow-hidden bg-[#0c203b] lg:block lg:col-span-6 xl:col-span-7">
            
            {{-- FOTO PETUGAS / INFRASTRUKTUR SEBAGAI BACKGROUND --}}
            <img src="{{ asset('images/hero-bg.jpg') }}" 
                 alt="LaporKita Layanan Publik" 
                 class="absolute inset-0 h-full w-full object-cover object-center opacity-30 mix-blend-overlay" />
            
            {{-- OCEAN BLUE GRADIENT OVERLAY (SESUAI TEMA BIRU LAPORKITA) --}}
            <div class="absolute inset-0 bg-gradient-to-tr from-[#06182e]/95 via-[#0c2c54]/85 to-[#1d4ed8]/75"></div>

            {{-- KONTEN BRANDING OVERLAY --}}
            <div class="relative z-10 flex h-full flex-col justify-between p-12 text-white lg:p-16">
                
                {{-- LOGO BADGE DI POJOK KANAN ATAS --}}
                <div class="flex items-center justify-end">
                    <div class="inline-flex items-center gap-2.5 rounded-2xl border border-white/15 bg-white/10 px-4 py-2 shadow-lg backdrop-blur-md">
                        <svg class="h-5 w-5 text-blue-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="text-lg font-extrabold tracking-wider">
                            LAPOR<span class="text-blue-300">KITA!</span>
                        </span>
                    </div>
                </div>

                {{-- PESAN UTAMA & STATISTIK KINERJA --}}
                <div class="max-w-lg">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/20 px-3.5 py-1 text-xs font-bold text-blue-200 backdrop-blur-md">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Layanan Terpadu & Terpercaya
                    </div>
                    
                    <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">
                        Satu Laporan, Langkah Nyata untuk Kota yang Lebih Baik.
                    </h2>
                    
                    <p class="mt-4 text-sm leading-relaxed text-blue-100/80">
                        Akses kembali akun Anda untuk memantau status aduan, verifikasi tindak lanjut, dan transparansi penanganan fasilitas kota.
                    </p>

                    <div class="mt-8 grid grid-cols-2 gap-4 border-t border-white/10 pt-6">
                        <div>
                            <p class="text-2xl font-black text-white">100%</p>
                            <p class="mt-0.5 text-xs text-blue-200/80">Transparansi Laporan</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-white">24/7</p>
                            <p class="mt-0.5 text-xs text-blue-200/80">Sistem Laporan Aktif</p>
                        </div>
                    </div>
                </div>

                {{-- FOOTER KANAN --}}
                <div class="text-xs text-blue-200/60">
                    Pusat Bantuan & Layanan Hotline: 777 (Bebas Pulsa)
                </div>

            </div>

        </div>

    </div>

</body>
</html>