@if ($claim->current_step == 5 && !$claim->rejected_at && $claim->qr_shown_at)
    <form method="POST" action="{{ route('barang.klaim.qr.reset', $claim) }}" class="mt-4 text-center"
        onsubmit="return confirm('Izinkan pemilik menampilkan QR code sekali lagi?')">
        @csrf
        <button class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0066FF] hover:underline">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>QR pemilik bermasalah? Izinkan tampil ulang</span>
        </button>
    </form>
@endif