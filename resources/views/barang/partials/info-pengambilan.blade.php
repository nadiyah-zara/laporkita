<div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
    {{-- 1. Lokasi Pengambilan --}}
    <div class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 transition-all hover:bg-white hover:shadow-xs">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-2xs">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Titik Lokasi</span>
            <p class="mt-0.5 text-xs font-bold text-slate-800 break-words leading-snug">
                {{ $claim->pickup_location ?: 'Lokasi belum ditentukan' }}
            </p>
        </div>
    </div>

    {{-- 2. Tanggal Pengambilan --}}
    <div class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 transition-all hover:bg-white hover:shadow-xs">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-2xs">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Hari & Tanggal</span>
            <p class="mt-0.5 text-xs font-bold text-slate-800 leading-snug">
                {{ $claim->pickup_date ? $claim->pickup_date->translatedFormat('l, d F Y') : '-' }}
            </p>
        </div>
    </div>

    {{-- 3. Jam Pertemuan --}}
    <div class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 transition-all hover:bg-white hover:shadow-xs">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0066FF] shadow-2xs">
            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Jam Janji Temu</span>
            <p class="mt-0.5 text-xs font-bold text-slate-800 leading-snug">
                {{ $claim->pickup_time ? $claim->pickup_time . ' WIB' : '-' }}
            </p>
        </div>
    </div>
</div>