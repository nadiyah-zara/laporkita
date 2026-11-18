<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;

class LaporanInfrastrukturController extends Controller
{
    // 6 Tahapan Sesuai Desain:
    // 1 Dilaporkan, 2 Survey Tim, 3 Diverifikasi, 4 Persiapan, 5 Pengerjaan, 6 Selesai
    private const STEP = [
        'pending'     => 1,
        'survey'      => 2,
        'verified'    => 3,
        'persiapan'   => 4,
        'pengerjaan'  => 5,
        'done'        => 6,
    ];

    public function index(Request $request)
    {
        $status = $request->query('status', 'semua');

        $counts = [
            'semua'       => Report::infrastruktur()->count(),
            'pending'     => Report::infrastruktur()->where('status', 'pending')->count(),
            'verified'    => Report::infrastruktur()->where('status', 'verified')->count(),
            'in_progress' => Report::infrastruktur()->where('status', 'in_progress')->count(),
            'done'        => Report::infrastruktur()->whereIn('status', ['done', 'resolved'])->count(),
            'rejected'    => Report::infrastruktur()->where('status', 'rejected')->count(),
        ];

        $query = Report::infrastruktur()->with('category');

        if ($status === 'done') {
            $query->whereIn('status', ['done', 'resolved']);
        } elseif (in_array($status, ['pending', 'verified', 'in_progress', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->query('urgency'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        $reports = $query->orderByRaw("urgency = 'kritis' desc")->latest()->paginate(15)->withQueryString();
        $categories = Category::where('type', 'infrastruktur')->get();

        return view('admin.infrastruktur.index', compact('reports', 'counts', 'status', 'categories'));
    }

    public function show(Report $report)
    {
        $this->pastikanInfrastruktur($report);
        $report->load(['category', 'user', 'logs']);

        return view('admin.infrastruktur.show', compact('report'));
    }

    public function verifikasi(Report $report)
    {
        $this->pastikanInfrastruktur($report);
        abort_unless($report->status === 'pending', 422);

        $report->forceFill([
            'status'       => 'verified', 
            'current_step' => self::STEP['verified']
        ])->save();

        $report->catat('Laporan diverifikasi', 'Laporan Anda sudah diverifikasi oleh admin dinas dan siap dijadwalkan pengerjaannya.');

        return back()->with('success', 'Laporan diverifikasi. Silakan tugaskan pengawas dan tim lapangan.');
    }

    public function tolak(Request $request, Report $report)
    {
        $this->pastikanInfrastruktur($report);
        abort_unless(in_array($report->status, ['pending', 'verified']), 422);

        $data = $request->validate(
            ['admin_note' => 'required|string|max:500'],
            ['admin_note.required' => 'Alasan penolakan wajib diisi.']
        );

        $report->forceFill([
            'status'     => 'rejected', 
            'admin_note' => $data['admin_note']
        ])->save();

        $report->catat('Laporan ditolak', $data['admin_note']);

        return back()->with('success', 'Laporan ditolak.');
    }

    public function tugaskan(Request $request, Report $report)
    {
        $this->pastikanInfrastruktur($report);
        abort_unless(in_array($report->status, ['verified', 'in_progress']), 422);

        $data = $request->validate([
            'supervisor_name'  => 'required|string|max:150',
            'supervisor_phone' => 'required|string|max:25',
            'scheduled_date'   => 'nullable|date',
            'supervisor_notes' => 'nullable|string|max:1000',
        ], [
            'supervisor_name.required'  => 'Nama petugas wajib diisi.',
            'supervisor_phone.required' => 'Nomor HP petugas wajib diisi agar pelapor bisa menghubungi.',
        ]);

        $pertamaKali = ($report->status === 'verified');
        $stepSekarang = ($report->progress_percentage ?? 0) > 0 
            ? self::STEP['pengerjaan'] 
            : self::STEP['persiapan'];

        $report->forceFill([
            'supervisor_name'  => $data['supervisor_name'],
            'supervisor_phone' => $data['supervisor_phone'],
            'scheduled_date'   => $data['scheduled_date'] ?? null,
            'supervisor_notes' => $data['supervisor_notes'] ?? null,
            'status'           => 'in_progress',
            'current_step'     => $stepSekarang,
        ])->save();

        if ($pertamaKali) {
            $jadwal = $report->scheduled_date
                ? ' Target jadwal: ' . \Carbon\Carbon::parse($report->scheduled_date)->translatedFormat('d F Y') . '.'
                : '';
            $report->catat('Petugas ditugaskan', 'Pengawas: ' . $data['supervisor_name'] . '.' . $jadwal . ' Kontak petugas tersedia di rincian laporan.');
        }

        return back()->with('success', 'Petugas ditugaskan. Laporan kini dalam status persiapan/pengerjaan.');
    }

    public function progres(Request $request, Report $report)
    {
        $this->pastikanInfrastruktur($report);
        abort_unless($report->status === 'in_progress', 422);

        $data = $request->validate(['progress_percentage' => 'required|integer|min:0|max:99']);

        $report->forceFill([
            'progress_percentage' => $data['progress_percentage'],
            'current_step'        => self::STEP['pengerjaan'],
        ])->save();

        $report->catat('Progres diperbarui', 'Capaian pengerjaan lapangan mencapai ' . $data['progress_percentage'] . '%.', notify: false);

        return back()->with('success', 'Progres diperbarui menjadi ' . $data['progress_percentage'] . '%.');
    }

    public function selesai(Request $request, Report $report)
    {
        $this->pastikanInfrastruktur($report);
        abort_unless($report->status === 'in_progress', 422);

        $data = $request->validate([
            'photo_after'      => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'completion_notes' => 'required|string|max:1000',
        ], [
            'photo_after.required'      => 'Foto hasil akhir pengerjaan wajib diunggah.',
            'completion_notes.required' => 'Rincian penyelesaian pengerjaan wajib diisi.',
        ]);

        $path = $request->file('photo_after')->store('reports/after', 'public');

        $report->forceFill([
            'photo_after'         => $path,
            'completion_notes'    => $data['completion_notes'],
            'progress_percentage' => 100,
            'status'              => 'done',
            'current_step'        => self::STEP['done'],
        ])->save();

        // Mencatat log selesai dengan foto perbaikan
        $report->catat('Perbaikan selesai', $data['completion_notes'], $path);

        return back()->with('success', 'Laporan berhasil diselesaikan. Foto Sebelum vs Sesudah kini tampil di publik.');
    }

    private function pastikanInfrastruktur(Report $report): void
    {
        abort_unless($report->type === 'infrastruktur', 404);
    }
}
