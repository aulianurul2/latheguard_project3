<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Komponen;
use App\Models\Mesin;
use Illuminate\Database\Seeder;

class KomponenSeeder extends Seeder
{
    public function run(): void
    {
        $mesin = Mesin::firstOrCreate(
            ['nama' => 'Mesin Bubut 1'],
            ['tipe' => 'Bubut', 'lokasi' => 'Workshop']
        );

        $daftar = [
            ['nama' => 'Bearing Spindle', 'kategori' => 'Bantalan dan Poros', 'interval_jam' => 500],
            ['nama' => 'Belt',            'kategori' => 'Transmisi',          'interval_jam' => 800],
            ['nama' => 'Oli',             'kategori' => 'Pelumasan',          'interval_jam' => 200],
            ['nama' => 'Chuck',           'kategori' => 'Penjepit',           'interval_jam' => 1000],
        ];

        foreach ($daftar as $k) {
            $kategori = Kategori::where('nama', $k['kategori'])->first();

            $komponen = Komponen::firstOrCreate(
                ['mesin_id' => $mesin->id, 'nama' => $k['nama']],
                [
                    'kategori_id'  => $kategori?->id,
                    'interval_jam' => $k['interval_jam'],
                    'jam_terpakai' => 0,
                ]
            );

            // Komponen lama (dari tes Postman) yang belum punya kategori ikut dilengkapi
            if ($komponen->kategori_id === null && $kategori) {
                $komponen->update(['kategori_id' => $kategori->id]);
            }
        }
    }
}