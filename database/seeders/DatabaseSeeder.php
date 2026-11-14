<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@laporkita.test'],
            [
                'name' => 'Admin LaporKita',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'warga@laporkita.test'],
            [
                'name' => 'Warga Contoh',
                'password' => Hash::make('password'),
                'role' => 'masyarakat',
            ]
        );

        $this->call(CategorySeeder::class);
    }
}