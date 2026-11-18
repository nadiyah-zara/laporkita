<div class="rounded-3xl border border-emerald-200 bg-emerald-50/60 p-8 text-center shadow-sm">
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 shadow-xs">
        <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
    </div>
    
    <h3 class="mt-4 text-xl font-bold text-slate-900">Barang Berhasil Dikembalikan!</h3>
    <p class="mx-auto mt-1.5 max-w-md text-xs leading-relaxed text-slate-500">
        Proses serah terima barang telah tuntas diverifikasi. Laporan ini resmi diselesaikan dan diarsipkan.
    </p>

    <div class="mt-6 flex justify-center">
        <a href="{{ $backUrl }}" 
           class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>{{ $backLabel }}</span>
        </a>
    </div>
</div>