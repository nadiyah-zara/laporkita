<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    private const TIPE = ['infrastruktur' => 'Infrastruktur', 'barang_hilang' => 'Barang Hilang'];

    public function index()
    {
        return view('admin.kategori.index', [
            'kategori' => Category::orderBy('name')->get()->groupBy('type'),
            'jumlah'   => Report::select('category_id', DB::raw('COUNT(*) as total'))->groupBy('category_id')->pluck('total', 'category_id'),
            'tipe'     => self::TIPE,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(self::TIPE))],
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->where('type', $request->type)],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Kategori dengan nama itu sudah ada pada jenis yang sama.',
        ]);

        $data['slug'] = $this->buatSlug($data['name']);

        (new Category())->forceFill($data)->save();

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $kategori)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->where('type', $kategori->type)->ignore($kategori->id)
            ],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Kategori dengan nama itu sudah ada.',
        ]);

        // Slug ikut diperbarui hanya kalau namanya berubah
        if ($data['name'] !== $kategori->name) {
            $data['slug'] = $this->buatSlug($data['name'], $kategori->id);
        }

        $kategori->forceFill($data)->save();

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $kategori)
    {
        $dipakai = Report::where('category_id', $kategori->id)->count();

        if ($dipakai > 0) {
            return back()->with('error', "Kategori tidak bisa dihapus karena dipakai {$dipakai} laporan.");
        }

        $kategori->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
    /** Slug unik: pohon-tumbang, pohon-tumbang-2, dst. */
    private function buatSlug(string $nama, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($nama) ?: 'kategori';
        $slug = $dasar;
        $i = 2;

        while (Category::where('slug', $slug)
            ->when($abaikanId, fn($q) => $q->where('id', '!=', $abaikanId))
            ->exists()
        ) {
            $slug = $dasar . '-' . $i++;
        }

        return $slug;
    }
}
