<?php

namespace Database\Seeders;

use App\Models\Mesin;
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
            ['email' => 'owner@latheguard.test'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password123'),
                'role' => 'owner',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teknisi@latheguard.test'],
            [
                'name' => 'Teknisi',
                'password' => Hash::make('password123'),
                'role' => 'teknisi',
            ]
        );

        Mesin::firstOrCreate(
            ['nama' => 'Mesin Bubut 1'],
            ['tipe' => 'Bubut', 'lokasi' => 'Workshop']
        );

        $this->call(KomponenSeeder::class);
    }
}