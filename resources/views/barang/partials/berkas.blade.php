@php $isPdf = $photo && str_ends_with(strtolower($photo), '.pdf'); @endphp
<div class="mt-4 space-y-3 text-xs">
    <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
        <p class="font-bold text-slate-500">Identitas unik barang</p>
        <p class="mt-1 font-semibold text-slate-900">{{ $feature }}</p>
    </div>
    <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
        <p class="font-bold text-slate-500">Warna furing / lapisan dalam</p>
        <p class="mt-1 font-semibold text-slate-900">{{ $color }}</p>
    </div>
    <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
        <p class="font-bold text-slate-500">Penjelasan rinci</p>
        <p class="mt-1 whitespace-pre-line font-semibold text-slate-900">{{ $detail }}</p>
    </div>
    @if ($photo)
        <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">
            <p class="font-bold text-slate-500">{{ $photoLabel }}</p>
            @if ($isPdf)
                <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="mt-2 inline-block font-bold text-blue-600 hover:underline">Buka berkas PDF</a>
            @else
                <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                    <img src="{{ asset('storage/' . $photo) }}" class="mt-2 h-48 w-auto rounded-xl border border-slate-200 object-cover" alt="Foto">
                </a>
            @endif
        </div>
    @endif
</div>