@props(['claim'])

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div x-data="{
        open: false,
        url: '{{ route('barang.klaim.konfirmasi', $claim) }}',
        render() {
            this.open = true;
            this.$nextTick(() => {
                const el = document.getElementById('qr-ticket');
                el.innerHTML = '';
                new QRCode(el, { text: this.url, width: 176, height: 176, colorDark: '#0c203b', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.H });
            });
        }
    }">
    <button type="button" @click="render()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-xs font-bold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-700">
        TAMPILKAN QR
    </button>
    <p class="mt-2 text-[10px] font-bold text-rose-600">*Tampilkan QR hanya saat Anda benar-benar sudah bertemu penemu.</p>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl" @click.outside="open = false">
            <h3 class="text-base font-bold text-[#0c203b]">QR Verifikasi Serah Terima</h3>
            <p class="mt-1 text-xs text-slate-400">Tunjukkan ke penemu barang saat bertemu</p>
            <div class="my-6 flex justify-center">
                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                    <div id="qr-ticket" class="flex h-44 w-44 items-center justify-center"></div>
                </div>
            </div>
            <p class="font-mono text-sm font-bold tracking-wider text-blue-600">{{ $claim->claim_code }}</p>
            <button type="button" @click="open = false" class="mt-4 w-full rounded-xl bg-slate-900 py-3 text-xs font-bold text-white hover:bg-slate-800">Tutup QR</button>
        </div>
    </div>
</div>