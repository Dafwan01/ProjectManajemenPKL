<?php

namespace Database\Seeders;

use App\Models\DetailJadwal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DetailJadwal::create([
            'jadwal_id' => 1,
            'user_id' => 3,
            'hari' => 'Senin',
        ]);
    }
}
