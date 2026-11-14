<x-public-layout title="Pilih Jenis Pelaporan - LaporKita">
    <main class="min-h-screen bg-[#f4f7fb] py-8 px-4 sm:px-6">
        <div class="mx-auto max-w-4xl">
            
            {{-- TOMBOL KEMBALI --}}
            <div class="mb-6">
                <a href="{{ route('laporan.barang.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>

            {{-- HEADER TITLE --}}
            <div class="mb-8 flex items-center gap-3">
                <div class="h-9 w-2 rounded-full bg-[#0066FF]"></div>
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0c203b]">
                    Pilih Jenis Pelaporan
                </h1>
            </div>

            {{-- 2 PILIHAN CARD BESAR --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                {{-- CARD 1: SAYA KEHILANGAN BARANG --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-100 bg-white p-8 shadow-sm transition-all hover:shadow-md">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                        </div>
                        <h2 class="mt-6 text-xl font-bold text-[#0c203b]">Saya Kehilangan Barang (Barang Hilang)</h2>
                        <p class="mt-3 text-xs leading-relaxed text-slate-500">
                            Laporkan barang berharga atau identitas Anda yang tertinggal agar masuk ke radar GIS publik dan diverifikasi oleh posko pengamanan setempat.
                        </p>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('laporan.barang.kehilangan') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#0c203b] py-3.5 text-xs font-bold text-white transition-all hover:bg-slate-800">
                            Isi Formulir Kehilangan
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                {{-- CARD 2: SAYA MENEMUKAN BARANG --}}
                <div class="flex flex-col justify-between rounded-3xl border border-slate-100 bg-white p-8 shadow-sm transition-all hover:shadow-md">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
                        </div>
                        <h2 class="mt-6 text-xl font-bold text-[#0c203b]">Saya Menemukan Barang (Barang Ditemukan)</h2>
                        <p class="mt-3 text-xs leading-relaxed text-slate-500">
                            Bantu kembalikan barang temuan kepada pemilik sah atau catat titik pos pengamanan yang menyimpan barang agar pemilik dapat mengambilnya secara langsung.
                        </p>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('laporan.barang.penemuan') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#0066FF] py-3.5 text-xs font-bold text-white transition-all hover:bg-blue-700">
                            Isi Formulir Penemuan
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>
</x-public-layout>