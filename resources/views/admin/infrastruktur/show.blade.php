@php
$badge = [
'pending' => ['Menunggu Verifikasi', 'bg-amber-50 text-amber-600'],
'verified' => ['Terverifikasi', 'bg-blue-50 text-blue-600'],
'in_progress' => ['Sedang Dikerjakan', 'bg-indigo-50 text-indigo-600'],
'done' => ['Selesai', 'bg-emerald-50 text-emerald-600'],
'resolved' => ['Selesai', 'bg-emerald-50 text-emerald-600'],
'rejected' => ['Ditolak', 'bg-rose-50 text-rose-600'],
];
[$label, $warna] = $badge[$report->status] ?? [$report->status, 'bg-slate-100 text-slate-600'];

$fotos = collect(is_array($report->photos) && count($report->photos) ? $report->photos : [$report->photo])->filter();
$field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-[#f0f4ff]/40 p-3 text-xs text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none';
$wa = \App\Models\ItemClaim::waNumber($report->reporter_phone);
@endphp

<x-admin-layout title="{{ $report->ticket_code }} - Admin LaporKita">

    <a href="{{ route('admin.infrastruktur.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0066FF] px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">← Kembali</a>

    @if (session('success'))
    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">
        @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
    </div>
    @endif

    <div class="mb-6 mt-5 flex flex-wrap items-center gap-3">
        <h1 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">{{ $report->ticket_code }}</h1>
        <span class="rounded-md px-3 py-1 text-[11px] font-bold {{ $warna }}">{{ $label }}</span>
        @if ($report->urgency === 'kritis')
        <span class="rounded-md bg-rose-50 px-3 py-1 text-[11px] font-bold text-rose-600">KRITIS</span>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-5">

        {{-- KIRI: DETAIL LAPORAN --}}
        <div class="space-y-6 lg:col-span-3">
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-[#0c203b]">{{ $report->title }}</h2>
                <p class="mt-1 text-[11px] text-slate-400">
                    {{ $report->category?->name }} · Masuk {{ $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                </p>
                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $report->description }}</p>

                @if ($fotos->isNotEmpty())
                <div class="mt-5 grid grid-cols-2 gap-3">
                    @foreach ($fotos as $f)
                    <a href="{{ asset('storage/' . $f) }}" target="_blank" class="overflow-hidden rounded-xl bg-slate-100">
                        <img src="{{ asset('storage/' . $f) }}" alt="Foto laporan" loading="lazy" class="h-44 w-full object-cover transition hover:scale-105">
                    </a>
                    @endforeach
                </div>
                @endif
            </section>

            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0c203b]">Lokasi</h3>
                <p class="mt-2 text-xs text-slate-600">📍 {{ $report->address }}</p>
                @if ($report->latitude && $report->longitude)
                <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank"
                    class="mt-2 inline-block text-xs font-bold text-blue-600 hover:underline">Buka di Google Maps →</a>
                @endif
            </section>

            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0c203b]">Pelapor</h3>
                <p class="mt-2 text-xs font-semibold text-slate-800">{{ $report->reporter_name }}</p>
                <p class="text-xs text-slate-500">{{ $report->reporter_phone }}</p>
                <a href="https://wa.me/{{ $wa }}" target="_blank"
                    class="mt-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">💬 Hubungi via WhatsApp</a>
            </section>
        </div>

        {{-- KANAN: PANEL AKSI --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- PENDING --}}
            @if ($report->status === 'pending')
            <section class="rounded-2xl border border-amber-100 bg-white p-6 shadow-sm" x-data="{ tolak: false }">
                <h3 class="text-sm font-bold text-[#0c203b]">Verifikasi Laporan</h3>
                <p class="mt-1 text-[11px] text-slate-500">Pastikan laporan valid dan lokasinya jelas sebelum diteruskan.</p>

                <form method="POST" action="{{ route('admin.infrastruktur.verifikasi', $report) }}" class="mt-4">
                    @csrf
                    <button class="w-full rounded-xl bg-blue-600 py-3 text-xs font-bold text-white shadow-md hover:bg-blue-700">✔ Verifikasi Laporan</button>
                </form>
                <button type="button" @click="tolak = !tolak" class="mt-3 w-full text-xs font-bold text-rose-600 hover:underline">✕ Tolak Laporan</button>

                <form x-show="tolak" x-cloak method="POST" action="{{ route('admin.infrastruktur.tolak', $report) }}" class="mt-3 space-y-3">
                    @csrf
                    <textarea name="admin_note" rows="3" required maxlength="500" placeholder="Alasan penolakan (akan dilihat pelapor)" class="{{ $field }}"></textarea>
                    <button class="w-full rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Penolakan</button>
                </form>
            </section>
            @endif

            {{-- VERIFIED / IN PROGRESS: PENUGASAN --}}
            @if (in_array($report->status, ['verified', 'in_progress']))
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0c203b]">{{ $report->status === 'verified' ? 'Tugaskan Petugas' : 'Petugas & Jadwal' }}</h3>
                <form method="POST" action="{{ route('admin.infrastruktur.tugaskan', $report) }}" class="mt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Nama petugas / pengawas <span class="text-rose-500">*</span></label>
                        <input type="text" name="supervisor_name" required value="{{ old('supervisor_name', $report->supervisor_name) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Nomor HP petugas <span class="text-rose-500">*</span></label>
                        <input type="tel" name="supervisor_phone" required placeholder="0812-3456-7890"
                            value="{{ old('supervisor_phone', $report->supervisor_phone) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Jadwal pengerjaan</label>
                        <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $report->scheduled_date) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Catatan untuk petugas</label>
                        <textarea name="supervisor_notes" rows="2" maxlength="1000" class="{{ $field }}">{{ old('supervisor_notes', $report->supervisor_notes) }}</textarea>
                    </div>
                    <button class="w-full rounded-xl bg-blue-600 py-2.5 text-xs font-bold text-white hover:bg-blue-700">
                        {{ $report->status === 'verified' ? 'Tugaskan & Mulai Pengerjaan' : 'Simpan Perubahan' }}
                    </button>
                </form>
            </section>
            @endif

            {{-- IN PROGRESS: PROGRES --}}
            @if ($report->status === 'in_progress')
            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                x-data="{ p: {{ (int) ($report->progress_percentage ?? 0) }} }">
                <h3 class="text-sm font-bold text-[#0c203b]">Progres Pengerjaan</h3>
                <form method="POST" action="{{ route('admin.infrastruktur.progres', $report) }}" class="mt-3 space-y-3">
                    @csrf
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                        <span>Capaian</span><span class="text-indigo-600" x-text="p + '%'"></span>
                    </div>
                    <input type="range" name="progress_percentage" min="0" max="99" step="5" x-model="p" class="w-full accent-indigo-600">
                    <button class="w-full rounded-xl bg-indigo-600 py-2.5 text-xs font-bold text-white hover:bg-indigo-700">Perbarui Progres</button>
                    <p class="text-[10px] text-slate-400">100% diisi otomatis saat laporan diselesaikan di bawah.</p>
                </form>
            </section>

            <section class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-[#0c203b]">Selesaikan Laporan</h3>
                <p class="mt-1 text-[11px] text-slate-500">Unggah foto hasil. Laporan akan tampil publik di Dokumentasi Penanganan.</p>
                <form method="POST" action="{{ route('admin.infrastruktur.selesai', $report) }}" enctype="multipart/form-data" class="mt-3 space-y-3"
                    x-data="{ preview: null }">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Foto setelah dikerjakan <span class="text-rose-500">*</span></label>
                        <input type="file" name="photo_after" accept="image/*" required
                            @change="const f = $event.target.files[0]; preview = f ? URL.createObjectURL(f) : null"
                            class="mt-1.5 block w-full text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-emerald-700">
                        <template x-if="preview"><img :src="preview" class="mt-2 h-32 w-auto rounded-xl border border-slate-200 object-cover" alt="Preview"></template>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Catatan penyelesaian <span class="text-rose-500">*</span></label>
                        <textarea name="completion_notes" rows="3" required maxlength="1000" class="{{ $field }}" placeholder="Contoh: Pengaspalan titik rusak sepanjang 20 meter telah selesai."></textarea>
                    </div>
                    <button class="w-full rounded-xl bg-emerald-600 py-3 text-xs font-bold text-white shadow-md hover:bg-emerald-700"
                        onclick="return confirm('Tandai laporan ini selesai?')">✔ Tandai Selesai</button>
                </form>
            </section>
            @endif

            {{-- DONE --}}
            @if (in_array($report->status, ['done', 'resolved']))
            <section class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-emerald-700">✔ Laporan Selesai</h3>
                @if ($report->photo_after)
                <a href="{{ asset('storage/' . $report->photo_after) }}" target="_blank">
                    <img src="{{ asset('storage/' . $report->photo_after) }}" class="mt-3 h-44 w-full rounded-xl object-cover" alt="Setelah dikerjakan">
                </a>
                @endif
                <p class="mt-3 text-xs leading-relaxed text-slate-700">{{ $report->completion_notes }}</p>
                <a href="{{ route('dokumentasi.index', ['jenis' => 'infrastruktur']) }}" target="_blank" class="mt-3 inline-block text-xs font-bold text-blue-600 hover:underline">Lihat di Dokumentasi Penanganan →</a>
            </section>
            @endif

            {{-- REJECTED --}}
            @if ($report->status === 'rejected')
            <section class="rounded-2xl border border-rose-100 bg-rose-50/50 p-6">
                <h3 class="text-sm font-bold text-rose-700">Laporan Ditolak</h3>
                <p class="mt-2 text-xs text-rose-600">Alasan: {{ $report->admin_note ?? '-' }}</p>
            </section>
            @endif

            {{-- RINGKASAN PENANGANAN --}}
            @if ($report->supervisor_name && !in_array($report->status, ['verified', 'in_progress']))
            <section class="rounded-2xl border border-slate-100 bg-white p-6 text-xs shadow-sm">
                <h3 class="text-sm font-bold text-[#0c203b]">Penanganan</h3>
                <p class="mt-2 text-slate-600">Petugas: <b class="text-slate-800">{{ $report->supervisor_name }}</b></p>
                @if ($report->scheduled_date)
                <p class="text-slate-600">Jadwal: <b class="text-slate-800">{{ \Carbon\Carbon::parse($report->scheduled_date)->translatedFormat('d F Y') }}</b></p>
                @endif
            </section>
            @endif
        </div>
    </div>
</x-admin-layout>