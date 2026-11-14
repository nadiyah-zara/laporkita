<form method="POST" action="{{ route('barang.klaim.kesepakatan', $claim->id) }}" class="mt-4 space-y-4">
    @csrf
    <div>
        <label class="block text-xs font-bold text-slate-800">1. Lokasi Pengambilan Barang <span class="text-rose-500">*</span></label>
        <input type="text" name="pickup_location" required placeholder="Contoh: Kopi Kenangan Dr. Mansyur"
            class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
    </div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block text-xs font-bold text-slate-800">2. Tanggal <span class="text-rose-500">*</span></label>
            <input type="date" name="pickup_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-800">3. Jam <span class="text-rose-500">*</span></label>
            <input type="time" name="pickup_time" required value="12:00"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3.5 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none">
        </div>
    </div>
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700">✔ Konfirmasi</button>
</form>