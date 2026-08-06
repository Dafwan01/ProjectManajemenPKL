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

        $newUsers = [
            ['nama' => 'Hanan Nuranisa Umar Jawas', 'email' => 'hananuj11@gmail.com'],
            ['nama' => 'Ratu Ritz Calton Kalendara', 'email' => 'raturizclton@gmail.com'],
            ['nama' => 'Erlangga Pharadiva Putra', 'email' => 'pharadivaputra13@gmail.com'],
            ['nama' => 'Muhammad Khilal Al Fazri', 'email' => 'khilalalfazri26@gmail.com'],
            ['nama' => 'M. Rifa Fauzan', 'email' => 'muhamadrifafauzan585@gmail.com'],
            ['nama' => 'Putra Nirbana Sabilillah', 'email' => 'putrasabilillah04@gmail.com'],
            ['nama' => 'Rangga Firmansyah', 'email' => 'Ranggagaep@gmail.com'],
            ['nama' => 'Arton Sena', 'email' => 'artonsena07@gmail.com'],
            ['nama' => 'Dicky Ramadhan', 'email' => 'dickyramadhan.tech@gmail.com'],
            ['nama' => 'Fauzi Romadhoni', 'email' => 'fauziromadhoni21@gmail.com'],
            ['nama' => 'Daffa Rizqi Wandika', 'email' => 'major8849@gmail.com'],
            ['nama' => 'Hanin Putri Sholiha', 'email' => 'haninn.putri@gmail.com'],
            ['nama' => 'Azka Mortaza', 'email' => 'azkamortaza5@gmail.com'],
            ['nama' => 'Luna Falya Iskandar', 'email' => 'lunafalyais@gmail.com'],
            ['nama' => 'Zafira A\'idah Gunawan', 'email' => 'zafiraag127@gmail.com'],
            ['nama' => 'Syahna Aulia Putri', 'email' => 'syahnaauliap@gmail.com'],
            ['nama' => 'Fariz Rizky Fadillah', 'email' => 'farizrizkyfadillah91@gmail.com'],
            ['nama' => 'Muhammad Fauzi Syabana', 'email' => 'fauzimmhd6@gmail.com'],
            ['nama' => 'Tiurma Taqiyyahafizh', 'email' => 'tiurma.taqiyy@gmail.com'],
            ['nama' => 'Adrian Maulana Yusuf', 'email' => 'adrianmaul1356@gmail.com'],
        ];

        foreach ($newUsers as $user) {
            User::create([
                'nama' => $user['nama'],
                'email' => $user['email'],
                'password' => Hash::make('12345678'),
                'role' => UserRole::PKL,
                'status' => UserStatus::AKTIF,
                'sekolah_id' => $sekolah1->sekolah_id,
                'mentor' => '',
                'divisi_id' => 1,
            ]);
        }
    }
}