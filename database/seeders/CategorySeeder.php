<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['Infrastruktur Jalan & Jembatan', 'jalan-jembatan',   'infrastruktur'],
            ['Fasilitas Air & Sanitasi',       'air-sanitasi',     'infrastruktur'],
            ['Penerangan Jalan Umum',          'penerangan',       'infrastruktur'],
            ['Ketertiban & Ruang Hijau',       'ketertiban',       'infrastruktur'],
            ['Barang Hilang',                  'barang-hilang',    'barang_hilang'],
            ['Barang Ditemukan',               'barang-ditemukan', 'barang_hilang'],
            ['Dompet & Tas',                   'dompet-tas',       'barang_hilang'],
        ];

        foreach ($daftar as [$nama, $slug, $tipe]) {
            $kategori = Category::firstOrNew(['slug' => $slug]);
            $kategori->forceFill(['name' => $nama, 'type' => $tipe])->save();
        }
    }
}