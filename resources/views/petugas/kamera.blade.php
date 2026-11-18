<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamera Petugas - {{ $report->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen p-4 flex flex-col justify-between">
    <div class="max-w-lg mx-auto w-full">
        
        {{-- Header Petugas --}}
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
                <span class="text-[11px] font-bold tracking-wide uppercase text-blue-400">Update Lapangan Petugas</span>
                <h1 class="font-mono text-lg font-black text-white">{{ $report->ticket_code }}</h1>
                <p class="text-xs text-slate-400 truncate max-w-[240px]">{{ $report->title }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-2.5 py-1 text-[10px] font-bold text-emerald-400 border border-emerald-500/30">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                GPS Aktif
            </span>
        </div>

        {{-- Pesan Sukses --}}
        @if (session('success'))
            <div class="mt-4 p-3.5 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Form Kamera & Geolocation --}}
        <form method="POST" action="{{ route('petugas.kamera.store', [str_replace('#', '', $report->ticket_code), request()->route('token')]) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf

            {{-- Ambil Foto Kamera HP --}}
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Ambil Foto Langsung di Lokasi :</label>
                <input type="file" name="foto_lapangan" accept="image/*" capture="environment" required id="cameraInput"
                       class="block w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0066FF] file:text-white hover:file:bg-blue-600 cursor-pointer">
            </div>

            {{-- Preview Foto dengan Stempel GPS & Waktu --}}
            <div id="previewBox" class="hidden rounded-2xl overflow-hidden border border-slate-700 bg-black relative">
                <img id="imgPreview" class="w-full h-64 object-cover">
                <div class="absolute bottom-0 inset-x-0 bg-black/85 p-3 text-[11px] font-mono text-emerald-400 border-t border-slate-800">
                    <p id="stampTime">Waktu: -</p>
                    <p id="stampGps">GPS: Mengambil titik koordinat...</p>
                </div>
            </div>

            {{-- Hidden Field GPS & Timestamp --}}
            <input type="hidden" name="latitude" id="lat">
            <input type="hidden" name="longitude" id="lng">
            <input type="hidden" name="captured_at" id="capturedAt">

            {{-- Kolom Keterangan Petugas --}}
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1.5">Catatan / Keterangan Kondisi Lapangan :</label>
                <textarea name="keterangan" rows="3" required 
                          placeholder="Contoh: Pekerjaan lapis aus AC-WC seluas 18 m² telah rampung dipadatkan dengan tandem roller..." 
                          class="w-full rounded-xl bg-slate-800 border border-slate-700 p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-[#0066FF] hover:bg-blue-600 active:scale-95 font-bold text-xs rounded-xl shadow-lg transition">
                Kirim Update Lapangan
            </button>
        </form>
    </div>

    <script>
        // Otomatis Deteksi Koordinat GPS Petugas di HP
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                document.getElementById('lat').value = pos.coords.latitude;
                document.getElementById('lng').value = pos.coords.longitude;
                document.getElementById('stampGps').innerText = `GPS: ${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)}`;
            }, function(err) {
                document.getElementById('stampGps').innerText = "GPS: Izin lokasi belum diaktifkan di browser";
            });
        }

        // Preview Foto & Tangkap Tanggal Jam saat itu
        document.getElementById('cameraInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const now = new Date();
                const timeString = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('id-ID') + ' WIB';
                document.getElementById('capturedAt').value = now.toISOString();
                document.getElementById('stampTime').innerText = `Waktu: ${timeString}`;
                document.getElementById('imgPreview').src = URL.createObjectURL(file);
                document.getElementById('previewBox').classList.remove('hidden');
            }
        });
    </script>
</body>
</html>