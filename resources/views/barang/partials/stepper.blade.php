<div class="mt-6 border-b border-slate-100 pb-8">
    <div class="grid grid-cols-2 gap-3 text-center sm:grid-cols-6">
        @foreach ($stages as $n => [$name, $time])
            @php 
                $done = !$claim->rejected_at && $claim->current_step >= $n; 
            @endphp
            <div class="flex flex-col items-center">
                {{-- Lingkaran Angka / Ceklis SVG --}}
                <div class="flex h-8 w-8 items-center justify-center rounded-xl text-xs font-bold transition-all {{ $done ? 'bg-[#0066FF] text-white shadow-xs' : 'bg-slate-100 text-slate-400' }}">
                    @if ($done)
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        {{ $n }}
                    @endif
                </div>
                <span class="mt-2 text-xs font-bold {{ $done ? 'text-slate-800' : 'text-slate-400' }}">
                    {{ $name }}
                </span>
                <span class="text-[10px] text-slate-400">{{ $time }}</span>
            </div>
        @endforeach
    </div>
</div>