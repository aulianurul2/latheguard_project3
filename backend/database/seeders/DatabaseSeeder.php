<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Utama Owner (Jaenal Arafat)
        User::updateOrCreate(
            ['email' => 'jaenal@gmail.com'],
            [
                'name'     => 'Jaenal',
                'password' => Hash::make('123456'),
                'role'     => 'owner',
            ]
        );

        // 2. Akun Contoh Teknisi (untuk uji pembatasan hak akses RBAC)
        User::updateOrCreate(
            ['email' => 'teknisi@latheguard.com'],
            [
                'name'     => 'Teknisi Workshop',
                'password' => Hash::make('123456'),
                'role'     => 'teknisi',
            ]
        );
    }
}