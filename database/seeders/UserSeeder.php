<?php

namespace Database\Seeders;

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
        User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('akuadmin'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::AKTIF,
            'asal_sekolah' => 'SMK Negeri 1',
            'mentor' => 'Budi Santoso',
            'divisi' => 'APTIKA',
        ]);

        User::create([
            'nama' => 'Mentor Utama',
            'email' => 'mentor@example.com',
            'password' => Hash::make('akumentor'),
            'role' => UserRole::MENTOR,
            'status' => UserStatus::AKTIF,
            'asal_sekolah' => '-',
            'mentor' => '-',
               'divisi' => 'APTIKA',
        ]);

        User::create([
            'nama' => 'Andi Kusuma',
            'email' => 'andi.kusuma@example.com',
            'password' => Hash::make('akupkl123'),
            'role' => UserRole::PKL,
            'status' => UserStatus::AKTIF,
            'asal_sekolah' => 'SMK Negeri 2 Jakarta',
            'mentor' => 'Budi Santoso',
            'skill' => 'Laravel, React, MySQL',
               'divisi' => 'APTIKA',
        ]);

        User::create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@example.com',
            'password' => Hash::make('akupkl123'),
            'role' => UserRole::PKL,
            'status' => UserStatus::AKTIF,
            'asal_sekolah' => 'SMK Negeri 3 Bandung',
            'mentor' => 'Bambang Sutrisno',
            'skill' => 'PHP, Vue, PostgreSQL',
               'divisi' => 'APTIKA',
        ]);

        User::create([
            'nama' => 'Rudi Hartono',
            'email' => 'pkl@example.com',
            'password' => Hash::make('akupkl'),
            'role' => UserRole::PKL,
            'status' => UserStatus::AKTIF,
            'asal_sekolah' => 'SMK Negeri 1 Surabaya',
            'mentor' => 'Cahyani Putri',
            'skill' => 'Java, Spring Boot, MongoDB',
               'divisi' => 'APTIKA',
        ]);
    }
}
