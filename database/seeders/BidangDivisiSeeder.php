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
                'nama_bidang' => 'Teknologi Informasi & Komunikasi',
                'divisi' => [
                    'Software Engineering',
                    'Network & Infrastructure',
                    'Cyber Security',
                    'Data Analytics',
                ],
            ],
            [
                'nama_bidang' => 'Sumber Daya Manusia & Umum',
                'divisi' => [
                    'Rekrutmen & Talenta',
                    'Pelatihan & Pengembangan',
                    'Kesejahteraan & Hubungan Industrial',
                ],
            ],
            [
                'nama_bidang' => 'Keuangan & Akuntansi',
                'divisi' => [
                    'Perencanaan Keuangan (FP&A)',
                    'Akuntansi & Perpajakan',
                    'Pengadaan (Procurement)',
                ],
            ],
            [
                'nama_bidang' => 'Pemasaran & Hubungan Masyarakat',
                'divisi' => [
                    'Digital Marketing',
                    'Public Relations (PR)',
                    'Desain Kreatif & Media',
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
                    'bidang_id' => $bidang->id,
                    'nama_divisi' => $namaDivisi,
                ]);
            }
        }
    }
}