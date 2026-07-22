<?php

namespace Database\Seeders;

use App\Models\presensi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['hadir', 'izin', 'sakit'];

        for ($i = 1; $i <= 10; $i++) {
            presensi::create([
                'foto_masuk' => 'presensis/foto_masuk_' . $i . '.jpg',
                'foto_keluar' => $i % 2 === 0 ? 'presensis/foto_keluar_' . $i . '.jpg' : null,
                'tanggal' => '2026-07-22',
                'status_kehadiran' => $statuses[array_rand($statuses)],
                'lokasi_masuk' => 'Jl. Sudirman No. ' . $i,
            ]);
        }
    }
}
