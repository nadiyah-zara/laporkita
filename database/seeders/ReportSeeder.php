<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $barang = Category::firstOrCreate(
            ['slug' => 'barang-hilang-ditemukan'],
            ['name' => 'Barang Hilang & Ditemukan']
        );
        $jalan = Category::firstOrCreate(
            ['slug' => 'infrastruktur-jalan-jembatan'],
            ['name' => 'Infrastruktur Jalan & Jembatan']
        );

        // Barang hilang & ditemukan: [item_kind, lat, lng, menit lalu]
        foreach ([
            ['ditemukan', 3.5632, 98.6560, 20],
            ['dicari',    3.5640, 98.6572, 45],
            ['ditemukan', 3.5615, 98.6551, 90],
            ['ditemukan', 3.5622, 98.6575, 180],
            ['dicari',    3.5608, 98.6580, 300],
        ] as [$kind, $lat, $lng, $minutesAgo]) {
            Report::create([
                'user_id' => $user->id,
                'category_id' => $barang->id,
                'type' => 'barang_hilang',
                'item_kind' => $kind,
                'title' => 'Dompet Lipat Cokelat & Isinya',
                'description' => 'Ditemukan di mesjid bagian shaf perempuan paling belakang dekat tempat mukena',
                'address' => 'Mesjid Politeknik Negeri Medan',
                'latitude' => $lat,
                'longitude' => $lng,
                'status' => 'verified',
                'created_at' => now()->subMinutes($minutesAgo),
                'updated_at' => now()->subMinutes($minutesAgo),
            ]);
        }

        // Infrastruktur: [judul, deskripsi, status, lat, lng, menit lalu]
        foreach ([
            ['Jalan Rusak dan Berbatu', 'Jalan ada yang berlubang lumayan besar dan dalam sehingga jadi berbahaya', 'verified', 3.5688, 98.6574, 20],
            ['Jalan Rusak dan Berbatu', 'Jalan ada yang berlubang lumayan besar dan dalam sehingga jadi berbahaya', 'verified', 3.5675, 98.6590, 300],
            ['Jembatan Rusak', 'Walaupun hanya jembatan kecil, tapi ini merupakan jembatan utama yang jalurnya cukup ramai', 'in_progress', 3.5662, 98.6612, 2880],
        ] as [$title, $desc, $status, $lat, $lng, $minutesAgo]) {
            Report::create([
                'user_id' => $user->id,
                'category_id' => $jalan->id,
                'type' => 'infrastruktur',
                'title' => $title,
                'description' => $desc,
                'address' => 'Jl. Dr. Mansyur',
                'latitude' => $lat,
                'longitude' => $lng,
                'status' => $status,
                'created_at' => now()->subMinutes($minutesAgo),
                'updated_at' => now()->subMinutes($minutesAgo),
            ]);
        }
    }
}