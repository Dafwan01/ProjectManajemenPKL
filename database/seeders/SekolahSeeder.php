<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Sekolah::create([
            'nama_sekolah' => 'SMK Negeri 1',
        ]);

        \App\Models\Sekolah::create([
            'nama_sekolah' => 'SMK Negeri 2 Jakarta',
        ]);

        \App\Models\Sekolah::create([
            'nama_sekolah' => 'SMK Negeri 3 Bandung',
        ]);

        \App\Models\Sekolah::create([
            'nama_sekolah' => 'SMK Negeri 1 Surabaya',
        ]);
    }
}
