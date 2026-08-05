<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\Divisi;
use Illuminate\Database\Seeder;

class BidangDivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Struktur Data Bidang dan Divisinya
        $data = [
            [
                'nama_bidang' => 'IKP (Informasi Dan Komunikasi Publik)',
                'divisi' => [
                    'Social Media Spesialist',
                    'Broadcast Spesialist',
                    'Multimedia Spesialist',
                    'Public Relation Spesialist ',
                ],
            ],
            [
                'nama_bidang' => 'APTIKA (Aplikasi Informatika)',
                'divisi' => [
                    'Programming',
                    'Jaringan',
                ],
            ],
        ];

        // Looping untuk menyimpan ke database
        foreach ($data as $item) {
            $bidang = Bidang::create([
                'nama_bidang' => $item['nama_bidang'],
            ]);

            foreach ($item['divisi'] as $namaDivisi) {
                Divisi::create([
                    'bidang_id' => $bidang->bidang_id,
                    'nama_divisi' => $namaDivisi,
                ]);
            }
        }
    }
}