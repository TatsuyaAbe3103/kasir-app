<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin / Owner (Bisa kelola menu & lihat laporan laba rugi)
        User::updateOrCreate(
            ['email' => 'admin@nusa.com'],
            [
                'name' => 'Manager Resto (Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Akun Kasir (Hanya akses transaksi kasir)
        User::updateOrCreate(
            ['email' => 'kasir@nusa.com'],
            [
                'name' => 'Siti (Kasir 01)',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ]
        );
    }
}
