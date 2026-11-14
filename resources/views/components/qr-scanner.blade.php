@props(['expected'])

<div x-data="{
        open: false,
        error: '',
        scanner: null,
        async start() {
            this.open = true;
            this.error = '';
            await this.$nextTick();
            try {
                this.scanner = new Html5Qrcode('qr-reader');
                await this.scanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: 220 },
                    (text) => {
                        if (text.startsWith('{{ $expected }}')) {
                            this.stop();
                            window.location.href = text;
                        } else {
                            this.error = 'QR ini bukan untuk transaksi ini.';
                        }
                    }
                );
            } catch (e) {
                this.error = 'Kamera tidak dapat dibuka. Izinkan akses kamera di browser.';
            }
        },
        async stop() {
            try { if (this.scanner) { await this.scanner.stop(); } } catch (e) {}
            this.open = false;
        }
    }">
    <button type="button" @click="start()" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-xs font-bold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-700">
        SCAN QR
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl">
            <h3 class="text-base font-bold text-[#0c203b]">Scan QR Pemilik Barang</h3>
            <p class="mt-1 text-xs text-slate-400">Arahkan kamera ke QR di layar pemilik.</p>
            <div id="qr-reader" class="mt-4 overflow-hidden rounded-2xl"></div>
            <p x-show="error" x-text="error" class="mt-3 text-xs font-semibold text-rose-600"></p>
            <button type="button" @click="stop()" class="mt-4 w-full rounded-xl bg-slate-900 py-3 text-xs font-bold text-white hover:bg-slate-800">Tutup</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>