@php 
    $isPdf = $photo && str_ends_with(strtolower($photo), '.pdf'); 
@endphp

<div class="mt-4 space-y-3 text-xs">
    {{-- Ciri Khusus --}}
    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4">
        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Identitas Unik Barang</span>
        <p class="mt-1 text-xs font-semibold text-slate-800 leading-relaxed">{{ $feature }}</p>
    </div>

    {{-- Lapisan Bagian Dalam --}}
    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4">
        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Warna Furing / Lapisan Dalam</span>
        <p class="mt-1 text-xs font-semibold text-slate-800 leading-relaxed">{{ $color }}</p>
    </div>

    {{-- Penjelasan Mendalam --}}
    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4">
        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Penjelasan Rinci Pemilik/Penemu</span>
        <p class="mt-1 whitespace-pre-line text-xs font-semibold text-slate-800 leading-relaxed">{{ $detail }}</p>
    </div>

    {{-- Foto / Berkas Lampiran --}}
    @if ($photo)
        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4">
            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $photoLabel }}</span>
            @if ($isPdf)
                <div class="mt-3">
                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" 
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-[#0066FF] shadow-xs hover:bg-blue-50/40">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Buka Berkas Bukti (PDF)</span>
                    </a>
                </div>
            @else
                <div class="mt-3">
                    <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="inline-block overflow-hidden rounded-xl border border-slate-200 bg-white">
                        <img src="{{ asset('storage/' . $photo) }}" class="h-44 w-auto object-cover transition hover:scale-105" alt="Bukti Foto">
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>