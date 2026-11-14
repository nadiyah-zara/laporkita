<div class="mt-8 border-b border-slate-100 pb-10">
    <div class="grid grid-cols-2 gap-3 text-center text-[11px] sm:grid-cols-6">
        @foreach ($stages as $n => [$name, $time])
            @php $done = !$claim->rejected_at && $claim->current_step >= $n; @endphp
            <div class="flex flex-col items-center">
                <div class="flex h-7 w-7 items-center justify-center rounded-full {{ $done ? 'bg-blue-600 font-bold text-white' : 'bg-slate-100 text-slate-400' }}">
                    {{ $done ? '✓' : $n }}
                </div>
                <span class="mt-2 font-bold {{ $done ? 'text-slate-900' : 'text-slate-400' }}">{{ $name }}</span>
                <span class="text-[9px] text-slate-400">{{ $time }}</span>
            </div>
        @endforeach
    </div>
</div>