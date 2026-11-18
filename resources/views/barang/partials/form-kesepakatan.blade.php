<form method="POST" action="{{ route('barang.klaim.kesepakatan', $claim->id) }}" class="mt-4 space-y-4">
    @csrf
    <div>
        <label class="block text-xs font-bold text-slate-700">1. Lokasi Pertemuan / Pengambilan <span class="text-rose-500">*</span></label>
        <input type="text" name="pickup_location" required placeholder="Contoh: Pos Satpam Gedung Z, Politeknik Negeri Medan"
            class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-xs font-bold text-slate-700">2. Tanggal Pengambilan <span class="text-rose-500">*</span></label>
            <input type="date" name="pickup_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-700">3. Jam Janji Temu <span class="text-rose-500">*</span></label>
            <input type="time" name="pickup_time" required value="12:00"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20">
        </div>
    </div>

    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <span>Konfirmasi Kesepakatan</span>
    </button>
</form>