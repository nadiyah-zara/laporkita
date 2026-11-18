@props(['claim'])

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div class="rounded-2xl border border-slate-100 bg-[#f0f4ff]/40 p-5"
    x-data="{
        open: false,
        loading: false,
        sudah: @js((bool) $claim->qr_shown_at),
        error: '',
        async tampil() {
            if (!confirm('Pastikan Anda sudah bertemu dengan penemu. QR hanya bisa ditampilkan SEKALI dan tidak bisa dibuka lagi setelah ditutup. Lanjutkan?')) return;

            this.loading = true;
            this.error = '';

            try {
                const res = await fetch('{{ route('barang.klaim.qr', $claim) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                if (!res.ok) {
                    this.error = data.message || 'QR tidak dapat ditampilkan.';
                    if (res.status === 409) this.sudah = true;
                    return;
                }

                this.sudah = true;
                this.open = true;
                await this.$nextTick();

                const el = document.getElementById('qr-ticket-{{ $claim->id }}');
                el.innerHTML = '';
                new QRCode(el, { text: data.url, width: 176, height: 176, colorDark: '#0c203b', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.H });
            } catch (e) {
                this.error = 'Gagal menghubungi server. Periksa koneksi Anda.';
            } finally {
                this.loading = false;
            }
        },
        tutup() {
            if (!confirm('Tutup QR? Setelah ditutup, QR tidak dapat dibuka lagi.')) return;
            const el = document.getElementById('qr-ticket-{{ $claim->id }}');
            if (el) el.innerHTML = '';
            this.open = false;
        }
    }">

    <div class="flex flex-wrap items-center gap-4">
        <button type="button" @click="tampil()" :disabled="sudah || loading"
            class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-xs font-bold text-white transition"
            :class="sudah ? 'cursor-not-allowed bg-slate-300' : 'bg-blue-600 shadow-lg shadow-blue-500/25 hover:bg-blue-700'">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span x-text="loading ? 'Memuat...' : (sudah ? 'QR Sudah Ditampilkan' : 'Tampilkan QR')"></span>
        </button>
    </div>

    <p x-show="!sudah" class="mt-3 text-[11px] font-semibold leading-relaxed text-amber-700">
        ⚠ QR hanya dapat ditampilkan satu kali. Tampilkan saat Anda sudah benar-benar bertemu penemu.
    </p>
    <p x-show="sudah && !open" x-cloak class="mt-3 text-[11px] leading-relaxed text-slate-500">
        QR sudah pernah ditampilkan. Jika ada kendala, minta penemu menekan "Izinkan tampil ulang" di halamannya.
    </p>
    <p x-show="error" x-text="error" x-cloak class="mt-3 text-[11px] font-semibold text-rose-600"></p>

    {{-- MODAL QR (tidak bisa ditutup dengan klik di luar, supaya tidak hilang tak sengaja) --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="w-full max-w-sm rounded-3xl bg-white p-6 text-center shadow-2xl">
            <h3 class="text-base font-bold text-[#0c203b]">QR Verifikasi Serah Terima</h3>
            <p class="mt-1 text-xs text-slate-400">Tunjukkan ke penemu barang saat bertemu</p>

            <div class="my-6 flex justify-center">
                <div class="rounded-2xl border border-slate-200 bg-white p-3">
                    <div id="qr-ticket-{{ $claim->id }}" class="flex h-44 w-44 items-center justify-center"></div>
                </div>
            </div>

            <p class="font-mono text-sm font-bold tracking-wider text-blue-600">{{ $claim->claim_code }}</p>
            <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-[11px] font-semibold text-amber-700">QR tidak dapat dibuka lagi setelah ditutup.</p>

            <button type="button" @click="tutup()" class="mt-4 w-full rounded-xl bg-slate-900 py-3 text-xs font-bold text-white hover:bg-slate-800">Selesai & Tutup</button>
        </div>
    </div>
</div>