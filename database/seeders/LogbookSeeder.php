<?php

namespace Database\Seeders;

use App\Models\log_book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogbookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            'Membuat database design untuk aplikasi',
            'Implement fitur login dan authentication',
            'Testing API endpoint untuk user management',
            'Fix bug pada proses export data',
            'Meeting dengan tim untuk diskusi requirement',
            'Dokumentasi API dan database schema',
            'Deploy aplikasi ke server production',
            'Training untuk end user',
            'Membuat unit test untuk module auth',
            'Refactoring code untuk performance optimization',
        ];

        for ($i = 1; $i <= 10; $i++) {
            log_book::create([
                'kegiatan' => $activities[($i - 1) % count($activities)],
                'user_id' => ($i % 3) + 3,  // User ID 3, 4, 5
                'presensi_id' => $i,
            ]);
        }
    }
}
