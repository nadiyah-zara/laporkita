<div class="space-y-3 text-xs font-bold text-slate-800">
    <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">📍 {{ $claim->pickup_location }}</div>
    <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">📅 {{ $claim->pickup_date?->translatedFormat('l, d F Y') }}</div>
    <div class="rounded-2xl border border-blue-50 bg-[#f0f4ff]/50 p-4">⏰ {{ $claim->pickup_time }}</div>
</div>