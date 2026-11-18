@php
    $boleh = auth()->check() && (auth()->id() === $report->user_id || auth()->user()->role === 'admin');
    $waPetugas = \App\Models\ItemClaim::waNumber($report->supervisor_phone);
@endphp

@if ($boleh && $report->supervisor_name)
    <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-4.5 text-xs shadow-xs">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            
            {{-- Data Petugas Lapangan --}}
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 font-bold text-[#0066FF]">
                    {{ strtoupper(substr($report->supervisor_name, 0, 1)) }}
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#0066FF]">Petugas Penanggung Jawab</span>
                    <p class="text-sm font-bold text-slate-900">{{ $report->supervisor_name }}</p>
                    
                    <div class="mt-0.5 flex flex-wrap items-center gap-2 text-slate-500 text-[11px]">
                        @if ($report->scheduled_date)
                            <span>Jadwal: {{ \Carbon\Carbon::parse($report->scheduled_date)->translatedFormat('d F Y') }}</span>
                            <span>·</span>
                        @endif
                        @if ($report->supervisor_phone)
                            <span>{{ $report->supervisor_phone }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tombol Kontak Resmi via WhatsApp --}}
            @if ($report->supervisor_phone)
                <a href="https://wa.me/{{ $waPetugas }}" target="_blank"
                   class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.115.549 4.101 1.509 5.829l-1.602 5.856 6.004-1.574c1.655.905 3.551 1.42 5.568 1.42 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/>
                    </svg>
                    <span>Hubungi Petugas</span>
                </a>
            @endif

        </div>
    </div>
@endif