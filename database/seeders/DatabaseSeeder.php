<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        // Akun admin
        $this->akun('admin@laporkita.test', 'Admin LaporKita', env('ADMIN_PASSWORD', 'admin12345'), 'admin');

        // Akun warga untuk demo dan pengujian (tidak dibuat di production)
        if (! app()->isProduction()) {
            $this->akun('warga1@laporkita.test', 'Warga Satu', 'password123', 'user');
            $this->akun('warga2@laporkita.test', 'Warga Dua', 'password123', 'user');
        }
    }

    private function akun(string $email, string $nama, string $password, string $role): void
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name'              => $nama,
            'role'              => $role,
            'password'          => Hash::make($password),
            'email_verified_at' => now(),
        ])->save();
    }
}