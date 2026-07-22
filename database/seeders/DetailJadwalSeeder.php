<?php

namespace Database\Seeders;

use App\Models\DetailJadwal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        foreach ($days as $day) {
            DetailJadwal::create([
                'jadwal_id' => 1,
                'user_id' => 3,
                'hari' => $day,
            ]);
        }

        foreach ($days as $day) {
            DetailJadwal::create([
                'jadwal_id' => 2,
                'user_id' => 4,
                'hari' => $day,
            ]);
        }

        foreach ($days as $day) {
            DetailJadwal::create([
                'jadwal_id' => 3,
                'user_id' => 5,
                'hari' => $day,
            ]);
        }
    }
}
