<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Infrastruktur Jalan & Jembatan', 'slug' => 'jalan-jembatan',   'type' => 'infrastruktur'],
            ['name' => 'Fasilitas Air & Sanitasi',        'slug' => 'air-sanitasi',     'type' => 'infrastruktur'],
            ['name' => 'Penerangan Jalan Umum',           'slug' => 'penerangan',       'type' => 'infrastruktur'],
            ['name' => 'Ketertiban & Ruang Hijau',        'slug' => 'ketertiban',       'type' => 'infrastruktur'],
            ['name' => 'Barang Hilang',                   'slug' => 'barang-hilang',    'type' => 'barang_hilang'],
            ['name' => 'Barang Ditemukan',                'slug' => 'barang-ditemukan', 'type' => 'barang_hilang'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}