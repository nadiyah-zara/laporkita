@php
    $boleh = auth()->check() && (auth()->id() === $report->user_id || auth()->user()->role === 'admin');
    $waPetugas = \App\Models\ItemClaim::waNumber($report->supervisor_phone);
@endphp

@if ($boleh && $report->supervisor_name)
    <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-4 text-xs">
        <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Petugas Penanggung Jawab</p>
        <p class="mt-1 text-sm font-bold text-[#0c203b]">{{ $report->supervisor_name }}</p>

        @if ($report->scheduled_date)
            <p class="mt-0.5 text-slate-500">Jadwal: {{ \Carbon\Carbon::parse($report->scheduled_date)->translatedFormat('d F Y') }}</p>
        @endif

        @if ($report->supervisor_phone)
            <p class="mt-0.5 text-slate-500">{{ $report->supervisor_phone }}</p>
            <a href="https://wa.me/{{ $waPetugas }}" target="_blank"
                class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                Hubungi Petugas
            </a>
        @endif
    </div>
@endif