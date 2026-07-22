<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jadwal::create([
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '16:00:00',
            'status_kerja' => 'WFO',
        ]);

        Jadwal::create([
            'jam_masuk' => '09:00:00',
            'jam_keluar' => '17:00:00',
            'status_kerja' => 'WFO',
        ]);

        Jadwal::create([
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '16:00:00',
            'status_kerja' => 'WFH',
        ]);
    }
}
