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

        // Koordinat Titik Tengah Balai Kota Bogor
        $centerLat = -6.5952;
        $centerLng = 106.7937;

        for ($i = 1; $i <= 10; $i++) {
           
            $latitude  = $centerLat + (mt_rand(-50, 50) / 100000);
            $longitude = $centerLng + (mt_rand(-50, 50) / 100000);

            presensi::create([
                'foto_masuk' => 'presensis/foto_masuk_' . $i . '.jpg',
                'foto_keluar' => $i % 2 === 0 ? 'presensis/foto_keluar_' . $i . '.jpg' : null,
                'tanggal' => '2026-07-22',
                'status_kehadiran' => $statuses[array_rand($statuses)],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'absen_masuk' => '08:00:00',
                'absen_keluar' => $i % 2 === 0 ? '17:00:00' : null,
            ]);
        }
    }
}