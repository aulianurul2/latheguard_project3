<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    foreach ([
        ['nama' => 'Bantalan dan Poros', 'deskripsi' => 'Komponen penumpu putaran spindle'],
        ['nama' => 'Transmisi',          'deskripsi' => 'Komponen penerus putaran dari motor'],
        ['nama' => 'Pelumasan',          'deskripsi' => 'Pelumas dan komponen pelumasan'],
        ['nama' => 'Penjepit',           'deskripsi' => 'Komponen penjepit benda kerja'],
    ] as $k) {
        \App\Models\Kategori::firstOrCreate(
            ['nama' => $k['nama']],
            ['deskripsi' => $k['deskripsi']]
        );
    }
}
}
