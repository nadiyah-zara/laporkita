<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Foto 1: Halaman Publik Lapor Infrastruktur
     */
    public function indexInfrastruktur(Request $request)
    {
        $query = Report::infrastruktur()
            ->whereIn('status', ['verified', 'in_progress'])
            ->with('category')
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Radius (pakai koordinat user dari JS)
        if ($request->filled('radius') && $request->filled('user_lat') && $request->filled('user_lng')) {
            $query->withinRadius(
                (float) $request->user_lat,
                (float) $request->user_lng,
                (float) $request->radius
            );
        }

        // Filter Rentang Tanggal
        if ($request->filled('days') && $request->days !== 'all') {
            $query->where('created_at', '>=', now()->subDays((int) $request->days));
        }

        $reports = $query->paginate(10)->withQueryString();

        $categories = Category::where('type', 'infrastruktur')->get();

        $counts = [
            'all'          => Report::infrastruktur()->whereIn('status', ['verified', 'in_progress'])->count(),
            'in_progress'  => Report::infrastruktur()->where('status', 'in_progress')->count(),
            'needs_repair' => Report::infrastruktur()->where('status', 'verified')->count(),
        ];

        return view('laporan.index', compact('reports', 'categories', 'counts'));
    }

    /**
     * Foto 2: Form Pelaporan Baru
     */
    public function createInfrastruktur()
    {
        $categories = Category::where('type', 'infrastruktur')->get();
        return view('laporan.create', compact('categories'));
    }

    /**
     * Foto 2: Simpan Data Laporan Baru
     * Setelah sukses simpan -> Otomatis diarahkan ke Foto 4 (Laporan Saya)
     */
    public function storeInfrastruktur(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'urgency'        => 'required|in:standar,kritis',
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'address'        => 'required|string|max:255',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'photos'         => 'required|array|min:2',
            'photos.*'       => 'image|mimes:jpeg,png,jpg,webp|max:10240',
            'reporter_name'  => 'required|string|max:150',
            'reporter_phone' => 'required|string|max:25',
        ], [
            'category_id.required'    => 'Silakan pilih kategori fasilitas.',
            'title.required'          => 'Judul laporan wajib diisi.',
            'description.required'    => 'Deskripsi kerusakan wajib diisi.',
            'address.required'        => 'Titik lokasi/alamat kejadian wajib diisi.',
            'photos.required'         => 'Minimal unggah 2 foto bukti kerusakan.',
            'photos.min'              => 'Wajib mengunggah minimal 2 foto bukti kerusakan.',
            'reporter_name.required'  => 'Nama lengkap pelapor wajib diisi.',
            'reporter_phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
        ]);
        $uploadedPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $uploadedPaths[] = $file->store('reports', 'public');
            }
        }
        Report::create([
            'user_id'             => Auth::id(),
            'category_id'         => $validated['category_id'],
            'type'                => 'infrastruktur',
            'title'               => $validated['title'],
            'urgency'             => $validated['urgency'],
            'description'         => $validated['description'],
            'address'             => $validated['address'],
            'latitude'            => $request->latitude ?? -6.319452,
            'longitude'           => $request->longitude ?? 106.865120,
            'photo'               => $uploadedPaths[0] ?? null,
            'photos'              => $uploadedPaths,
            'reporter_name'       => $validated['reporter_name'],
            'reporter_phone'      => $validated['reporter_phone'],
            'status'              => 'pending',
            'progress_percentage' => 0,
            'current_step'        => 1,
        ]);
        // ALUR OPSI 1: LANGSUNG KE HALAMAN LAPORAN SAYA (FOTO 4)
        return redirect()->route('laporan.infrastruktur.saya')
            ->with('success', 'Laporan Anda berhasil dikirim! Silakan pantau proses pengerjaannya di sini.');
    }

    /**
     * Foto 3: Detail Laporan (#JL2611001)
     */
    public function showInfrastruktur($id)
    {
        $report = Report::infrastruktur()->with(['category', 'user'])->findOrFail($id);

        $layakPublik = in_array($report->status, ['verified', 'in_progress', 'done', 'resolved']);
        $berhak = Auth::check() && (Auth::id() === $report->user_id || Auth::user()->role === 'admin');

        // Laporan yang belum diverifikasi atau ditolak hanya boleh dilihat pelapor dan admin
        abort_unless($layakPublik || $berhak, 404);

        return view('laporan.show', compact('report'));
    }

    /**
     * Foto 4: Halaman "Laporan Saya" (Khusus User Login)
     */
    public function sayaInfrastruktur(Request $request)
    {
        // Hanya ambil laporan milik akun yang sedang login
        $query = Report::infrastruktur()
            ->where('user_id', Auth::id())
            ->with('category')
            ->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $reports = $query->get();
        $categories = Category::where('type', 'infrastruktur')->get();
        // Hitung statistik khusus akun user ini
        $userReports = Report::infrastruktur()->where('user_id', Auth::id());
        $stats = [
            'total'       => (clone $userReports)->count(),
            'in_progress' => (clone $userReports)->where('status', 'in_progress')->count(),
            'pending'     => (clone $userReports)->where('status', 'pending')->count(),
            'verified'    => (clone $userReports)->where('status', 'verified')->count(),
            'resolved'    => (clone $userReports)->whereIn('status', ['done', 'resolved'])->count(),
        ];
        return view('laporan.my-reports', compact('reports', 'categories', 'stats'));
    }

        // ==========================================================
    // MODUL LAPOR BARANG HILANG & DITEMUKAN
    // ==========================================================

    /**
     * Foto 1: Halaman Depan / Publik Barang Hilang
     */
    /**
     * Foto 1: Halaman Depan / Publik Barang Hilang
     */
    public function indexBarang(Request $request)
    {
        // Hanya tampilkan laporan yang AKTIF (belum selesai)
        $query = Report::barang()
            ->with('category')
            ->whereNotIn('status', ['resolved', 'done'])
            ->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('item_kind')) {
            $query->where('item_kind', $request->item_kind);
        }

        $reports    = $query->paginate(10);
        $categories = Category::where('type', 'barang_hilang')->get();

        // Counts juga hanya hitung yang masih AKTIF
        $counts = [
            'all'       => Report::barang()
                ->whereNotIn('status', ['resolved', 'done'])
                ->count(),
            'hilang'    => Report::barang()
                ->whereNotIn('status', ['resolved', 'done'])
                ->where('item_kind', 'kehilangan')
                ->count(),
            'ditemukan' => Report::barang()
                ->whereNotIn('status', ['resolved', 'done'])
                ->where('item_kind', 'penemuan')
                ->count(),
            // Khusus untuk info statistik: berapa yang sudah berhasil dikembalikan
            'selesai'   => Report::barang()
                ->whereIn('status', ['resolved', 'done'])
                ->count(),
        ];

        return view('barang.index', compact('reports', 'categories', 'counts'));
    }

    /**
     * Foto 2: Halaman Pilih Jenis Laporan (Kehilangan vs Penemuan)
     */
    public function pilihJenisBarang()
    {
        return view('barang.pilih-jenis');
    }

    /**
     * Foto 3: Form Laporan Kehilangan
     */
    public function createKehilangan()
    {
        $categories = Category::where('type', 'barang_hilang')->get();
        return view('barang.create-kehilangan', compact('categories'));
    }

    /**
     * Foto 4: Form Laporan Penemuan
     */
    public function createPenemuan()
    {
        $categories = Category::where('type', 'barang_hilang')->get();
        return view('barang.create-penemuan', compact('categories'));
    }

    /**
     * Simpan Data Laporan Barang (Kehilangan / Penemuan)
     */
    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'item_kind'       => 'required|in:kehilangan,penemuan',
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'description'     => 'required|string',
            'event_date'      => 'required|date',
            'event_time'      => 'nullable|string',
            'address'         => 'required|string|max:255',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'reporter_phone'  => 'required|string|max:25',
            'photo'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Maks 5MB
        ], [
            'title.required'          => 'Nama barang wajib diisi.',
            'category_id.required'    => 'Silakan pilih kategori barang.',
            'description.required'    => 'Deskripsi & ciri-ciri khusus wajib diisi.',
            'event_date.required'     => 'Tanggal kejadian wajib diisi.',
            'address.required'        => 'Alamat patokan lokasi wajib diisi.',
            'reporter_phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports/barang', 'public');
        }

        $report = Report::create([
            'user_id'        => Auth::id(),
            'category_id'    => $validated['category_id'],
            'type'           => 'barang_hilang',
            'item_kind'      => $validated['item_kind'],
            'title'          => $validated['title'],
            'description'    => $validated['description'],
            'event_date'     => $validated['event_date'],
            'event_time'     => $validated['event_time'] ?? null,
            'address'        => $validated['address'],
            'latitude'       => $request->latitude ?? -6.175392,
            'longitude'      => $request->longitude ?? 106.827153,
            'photo'          => $photoPath,
            'reporter_name'  => Auth::user()->name ?? 'Warga',
            'reporter_phone' => $validated['reporter_phone'],
            'status'         => 'verified', // langsung tayang di feed publik
        ]);

        return redirect()->route('laporan.barang.saya')
            ->with('success', 'Laporan barang berhasil diterbitkan!');
    }
}
