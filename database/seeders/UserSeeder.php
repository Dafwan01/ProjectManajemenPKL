<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sekolah1 = Sekolah::firstOrCreate(['nama_sekolah' => 'SMK Negeri 1']);
        $sekolah2 = Sekolah::firstOrCreate(['nama_sekolah' => 'SMK Negeri 2 Jakarta']);
        $sekolah3 = Sekolah::firstOrCreate(['nama_sekolah' => 'SMK Negeri 3 Bandung']);
        $sekolah4 = Sekolah::firstOrCreate(['nama_sekolah' => 'SMK Negeri 1 Surabaya']);

        User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('akuadmin'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::AKTIF,
            'mentor' => 'Budi Santoso',
            'divisi_id' => 1,
        ]);

        User::create([
            'nama' => 'Mentor Utama',
            'email' => 'mentor@example.com',
            'password' => Hash::make('akumentor'),
            'role' => UserRole::MENTOR,
            'status' => UserStatus::AKTIF,
            'mentor' => '-',
            'divisi_id' => 1,
        ]);

        User::create([
            'nama' => 'Andi Kusuma',
            'email' => 'andi.kusuma@example.com',
            'password' => Hash::make('akupkl123'),
            'role' => UserRole::PKL,
            'status' => UserStatus::AKTIF,
            'sekolah_id' => $sekolah2->sekolah_id,
            'mentor' => 'Budi Santoso',
            'skill' => 'Laravel, React, MySQL',
            'divisi_id' => 1,
        ]);

        User::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@example.com',
            'password' => Hash::make('akupkl123'),
            'role' => UserRole::PKL,
            'status' => UserStatus::AKTIF,
            'sekolah_id' => $sekolah3->sekolah_id,
            'mentor' => 'Bambang Sutrisno',
            'skill' => 'PHP, Vue, PostgreSQL',
            'divisi_id' => 2,
        ]);

        User::create([
            'nama' => 'Rudi Hartono',
            'email' => 'pkl@example.com',
            'password' => Hash::make('akupkl'),
            'role' => UserRole::PKL,
            'status' => UserStatus::AKTIF,
            'sekolah_id' => $sekolah4->sekolah_id,
            'mentor' => 'Cahyani Putri',
            'skill' => 'Java, Spring Boot, MongoDB',
            'divisi_id' => 3,
        ]);
    }
}
