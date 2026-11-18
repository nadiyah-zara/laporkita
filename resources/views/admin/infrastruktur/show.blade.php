@php
    $badge = [
        'pending'     => ['Menunggu Verifikasi & Jadwal', 'bg-amber-50 text-amber-700 border-amber-200', 'bg-amber-500'],
        'verified'    => ['Terverifikasi & Siap Dikerjakan', 'bg-blue-50 text-[#0066FF] border-blue-200', 'bg-[#0066FF]'],
        'in_progress' => ['Sedang Dikerjakan Lapangan', 'bg-indigo-50 text-indigo-700 border-indigo-200', 'bg-indigo-500'],
        'done'        => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500'],
        'resolved'    => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500'],
        'rejected'    => ['Ditolak', 'bg-rose-50 text-rose-700 border-rose-200', 'bg-rose-500'],
    ];
    [$label, $badgeStyle, $dotStyle] = $badge[$report->status] ?? [$report->status, 'bg-slate-100 text-slate-700 border-slate-200', 'bg-slate-400'];

    $fotos = collect(is_array($report->photos) && count($report->photos) ? $report->photos : [$report->photo])->filter();
    $field = 'mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-800 transition focus:border-[#0066FF] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20';
    $waPelapor = preg_replace('/[^0-9]/', '', $report->reporter_phone ?? '');
    $waPetugas = preg_replace('/[^0-9]/', '', $report->supervisor_phone ?? '');
    
    // Hilangkan '#' dari kode tiket agar URL aman dibuka di browser HP
    $cleanTicket = str_replace('#', '', $report->ticket_code);
    $petugasToken = md5($report->id . 'laporkita');
    
    // Menggunakan url() langsung sehingga 100% tidak akan pernah error RouteNotFoundException
    $petugasLink = url("/petugas/kamera/{$cleanTicket}/{$petugasToken}");
@endphp

<x-admin-layout title="{{ $report->ticket_code }} - Detail & Kelola Laporan">

    {{-- TOMBOL KEMBALI & NAVIGASI ATAS --}}
    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.infrastruktur.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Daftar Laporan</span>
        </a>
    </div>

    {{-- NOTIFIKASI SUKSES / ERROR --}}
    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- HEADER KODE TIKET & STATUS --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="font-mono text-2xl font-black tracking-tight text-slate-900">{{ $report->ticket_code }}</h1>
            <span class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1 text-xs font-bold {{ $badgeStyle }}">
                <span class="h-2 w-2 rounded-full {{ $dotStyle }}"></span>
                {{ $label }}
            </span>
            @if ($report->urgency === 'kritis')
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-rose-600">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-rose-500"></span>
                    Prioritas Kritis
                </span>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-5">

        {{-- KOLOM KIRI (3/5): INFORMASI DETAIL LAPORAN --}}
        <div class="space-y-6 lg:col-span-3">
            
            {{-- Isi Laporan Warga --}}
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">{{ $report->title }}</h2>
                <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                    <span class="font-semibold text-slate-600">{{ $report->category?->name ?? 'Kategori Infrastruktur' }}</span>
                    <span>·</span>
                    <span>Masuk {{ $report->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi Keluhan Warga</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $report->description }}</p>
                </div>

                {{-- Foto Lampiran Warga (Kondisi Awal Sebelum Perbaikan) --}}
                @if ($fotos->isNotEmpty())
                    <div class="mt-6 border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Lampiran Foto Awal Warga</h3>
                            <span class="text-[10px] font-bold text-slate-500">Kondisi Sebelum</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($fotos as $f)
                            <a href="{{ asset('storage/' . $f) }}" target="_blank" class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-xs">
                                <img src="{{ asset('storage/' . $f) }}" alt="Foto awal" loading="lazy" class="h-36 w-full object-cover transition duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/30 opacity-0 transition-opacity group-hover:opacity-100">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                    </svg>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            {{-- Lokasi Kejadian --}}
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 text-slate-900">
                    <svg class="h-5 w-5 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-sm font-bold">Lokasi Kejadian</h3>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-700">{{ $report->address }}</p>
                
                @if ($report->latitude && $report->longitude)
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="font-mono text-xs text-slate-400">GPS: {{ $report->latitude }}, {{ $report->longitude }}</span>
                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0066FF] hover:underline">
                        <span>Buka di Google Maps</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
                @endif
            </section>

            {{-- Identitas Pelapor --}}
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 text-slate-900">
                    <svg class="h-5 w-5 text-[#0066FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <h3 class="text-sm font-bold">Identitas Pelapor</h3>
                </div>
                
                <div class="mt-4 flex flex-col justify-between gap-4 rounded-xl border border-slate-100 bg-slate-50/60 p-4 sm:flex-row sm:items-center">
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $report->reporter_name }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $report->reporter_phone }}</p>
                    </div>

                    @if ($waPelapor)
                    <a href="https://wa.me/{{ $waPelapor }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                        <span>Hubungi via WhatsApp</span>
                    </a>
                    @endif
                </div>
            </section>
        </div>

        {{-- KOLOM KANAN (2/5): AKSI WORKFLOW & LINK PETUGAS LAPANGAN --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- KOTAK LINK KHUSUS PETUGAS LAPANGAN (KAMERA & GPS AUTO-DETECT) --}}
            <section class="rounded-2xl border border-blue-200 bg-blue-50/40 p-5 shadow-xs" x-data="{ copied: false }">
                <div class="flex items-center gap-2 text-[#0066FF]">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="text-sm font-bold text-slate-900">Link Kamera Petugas Lapangan</h3>
                </div>
                <p class="mt-1 text-[11px] leading-relaxed text-slate-600">
                    Kirim link ini ke petugas lapangan saat <strong>Survey</strong> maupun <strong>Pengerjaan</strong>. Saat dibuka di HP petugas, link ini otomatis mengakses kamera dan mendeteksi titik koordinat GPS, tanggal & jam secara real-time.
                </p>

                <div class="mt-3 flex items-center gap-2">
                    <input type="text" readonly value="{{ $petugasLink }}" class="w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 font-mono text-[11px] text-slate-600 focus:outline-none">
                    <button type="button" @click="navigator.clipboard.writeText('{{ $petugasLink }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="shrink-0 rounded-lg bg-[#0066FF] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-blue-700">
                        <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                    </button>
                </div>

                @if ($waPetugas)
                <div class="mt-2 text-right">
                    <a href="https://wa.me/{{ $waPetugas }}?text={{ urlencode('Halo ' . $report->supervisor_name . ', berikut adalah link kamera update lapangan untuk tiket ' . $report->ticket_code . ': ' . $petugasLink) }}" 
                       target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:underline">
                        <span>Kirim Link via WhatsApp Petugas</span>
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
                @endif
            </section>

            {{-- 1. TAHAP PENDING: VERIFIKASI ATAU TOLAK --}}
            @if ($report->status === 'pending')
            <section class="rounded-2xl border border-amber-200 bg-white p-6 shadow-sm" x-data="{ tolak: false }">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900">1. Verifikasi Kelayakan Laporan</h3>
                </div>
                <p class="mt-2 text-xs text-slate-500">Periksa keabsahan laporan warga sebelum diteruskan ke tahap survey atau penjadwalan.</p>

                <form method="POST" action="{{ route('admin.infrastruktur.verifikasi', $report) }}" class="mt-4">
                    @csrf
                    <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#0066FF] py-3 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                        <span>Verifikasi & Setujui Laporan</span>
                    </button>
                </form>

                <div class="mt-3 text-center">
                    <button type="button" @click="tolak = !tolak" class="text-xs font-bold text-rose-600 hover:underline">
                        <span x-text="tolak ? 'Batal Tolak' : 'Tolak Laporan Ini'"></span>
                    </button>
                </div>

                {{-- Formulir Alasan Tolak --}}
                <form x-show="tolak" x-cloak method="POST" action="{{ route('admin.infrastruktur.tolak', $report) }}" class="mt-3 space-y-2.5">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea name="admin_note" rows="3" required maxlength="500" placeholder="Jelaskan alasan penolakan secara jelas..." class="{{ $field }}"></textarea>
                    </div>
                    <button class="w-full rounded-xl bg-rose-600 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-rose-700">
                        Konfirmasi Penolakan
                    </button>
                </form>
            </section>
            @endif

            {{-- 2. TAHAP PENUGASAN PETUGAS / TIM LAPANGAN --}}
            @if (in_array($report->status, ['verified', 'in_progress']))
            <section class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900">{{ $report->status === 'verified' ? 'Tugaskan Pengawas Lapangan' : 'Perbarui Petugas & Jadwal' }}</h3>
                </div>

                <form method="POST" action="{{ route('admin.infrastruktur.tugaskan', $report) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Nama Petugas / Pengawas <span class="text-rose-500">*</span></label>
                        <input type="text" name="supervisor_name" required value="{{ old('supervisor_name', $report->supervisor_name ?? 'Ir. Bambang Trihatmojo') }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Nomor HP Petugas (WhatsApp) <span class="text-rose-500">*</span></label>
                        <input type="tel" name="supervisor_phone" required placeholder="08123456789" value="{{ old('supervisor_phone', $report->supervisor_phone) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Target Jadwal Pengerjaan</label>
                        <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $report->scheduled_date) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Catatan Pengerjaan untuk Tim Lapangan</label>
                        <textarea name="supervisor_notes" rows="2" class="{{ $field }}" placeholder="Contoh: Pekerjaan lapis aus AC-WC seluas 18 m²...">{{ old('supervisor_notes', $report->supervisor_notes) }}</textarea>
                    </div>
                    <button class="w-full rounded-xl bg-[#0066FF] py-2.5 text-xs font-bold text-white shadow-sm hover:bg-blue-700 active:scale-95">
                        {{ $report->status === 'verified' ? 'Tugaskan & Mulai Pengerjaan' : 'Simpan Perubahan Penugasan' }}
                    </button>
                </form>
            </section>
            @endif

            {{-- 3. PROGRES PENGERJAAN & FORM SELESAI --}}
            @if ($report->status === 'in_progress')
            <section class="rounded-2xl border border-indigo-200/80 bg-white p-6 shadow-sm" x-data="{ p: {{ (int) ($report->progress_percentage ?? 57) }} }">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Progres Capaian Lapangan</h3>
                    <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-[#0066FF]" x-text="p + '%'"></span>
                </div>

                <form method="POST" action="{{ route('admin.infrastruktur.progres', $report) }}" class="mt-4 space-y-3">
                    @csrf
                    <input type="range" name="progress_percentage" min="0" max="99" step="5" x-model="p" 
                           class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-[#0066FF]">
                    <button class="w-full rounded-xl bg-indigo-600 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700">
                        Perbarui Capaian Progres
                    </button>
                </form>
            </section>

            {{-- Form Tandai Selesai (Unggah Foto Hasil Akhir / BAST) --}}
            <section class="rounded-2xl border border-emerald-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-bold">Selesaikan Penanganan (Before vs After)</h3>
                </div>
                <p class="mt-1 text-xs text-slate-500">Unggah foto hasil akhir untuk menampilkan perbandingan Sebelum vs Sesudah di halaman warga.</p>

                <form method="POST" action="{{ route('admin.infrastruktur.selesai', $report) }}" enctype="multipart/form-data" class="mt-4 space-y-3.5"
                      x-data="{ preview: null }">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Foto Hasil Perbaikan (After) <span class="text-rose-500">*</span></label>
                        <input type="file" name="photo_after" accept="image/*" required
                               @change="const f = $event.target.files[0]; preview = f ? URL.createObjectURL(f) : null"
                               class="mt-1.5 block w-full text-xs text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100">
                        <template x-if="preview">
                            <img :src="preview" class="mt-3 h-36 w-full rounded-xl border border-slate-200 object-cover shadow-xs" alt="Preview Foto Hasil">
                        </template>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700">Rincian Hasil Pengerjaan Lapangan (BAST) <span class="text-rose-500">*</span></label>
                        <textarea name="completion_notes" rows="3" required maxlength="1000" class="{{ $field }}" 
                                  placeholder="Contoh: Penggantian modul driver LED 120W pada 3 titik tiang, perbaikan koneksi kabel bawah tanah, dan kalibrasi sensor otomatis..."></textarea>
                    </div>

                    <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95"
                            onclick="return confirm('Apakah Anda yakin data perbaikan sudah sesuai dan laporan ini diselesaikan?')">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Sahkan & Tandai Laporan Selesai</span>
                    </button>
                </form>
            </section>
            @endif

            {{-- 4. KONDISI SETELAH SELESAI --}}
            @if (in_array($report->status, ['done', 'resolved']))
            <section class="rounded-2xl border border-emerald-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-2 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-bold">Laporan Telah Dituntaskan</h3>
                </div>

                @if ($report->photo_after)
                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                    <span class="block bg-slate-900 px-3 py-1 text-[10px] font-bold text-white">Foto Hasil Perbaikan (After)</span>
                    <a href="{{ asset('storage/' . $report->photo_after) }}" target="_blank">
                        <img src="{{ asset('storage/' . $report->photo_after) }}" class="h-44 w-full object-cover transition hover:scale-105" alt="Setelah dikerjakan">
                    </a>
                </div>
                @endif

                <div class="mt-4 rounded-xl bg-emerald-50/50 p-3.5 border border-emerald-100">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-800">Rincian Hasil Pengerjaan</span>
                    <p class="mt-1 text-xs leading-relaxed text-slate-700">{{ $report->completion_notes }}</p>
                </div>
            </section>
            @endif

        </div>
    </div>
</x-admin-layout>