<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\PermohonanIzin;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermohonanIzinSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', UserRole::PKL->value)->get();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user dengan role PKL. Jalankan seeder User terlebih dahulu.');
            return;
        }

        $alasanIzin = [
            'Ada keperluan keluarga mendadak.',
            'Mengurus dokumen sekolah.',
            'Menghadiri acara keluarga.',
            'Ada urusan administrasi kampus.',
        ];

        $alasanSakit = [
            'Demam dan tidak enak badan.',
            'Sakit perut sejak semalam.',
            'Flu dan pusing.',
            'Kondisi kesehatan kurang fit.',
        ];

        $statusOptions = ['pending', 'disetujui', 'ditolak'];

        foreach ($users as $index => $user) {
            // Setiap user dapat 2 permohonan contoh
            for ($i = 0; $i < 2; $i++) {
                $jenis = $i % 2 === 0 ? 'izin' : 'sakit';
                $status = $statusOptions[array_rand($statusOptions)];

                PermohonanIzin::create([
                    'user_id' => $user->user_id,
                    'tanggal' => now()->subDays(rand(0, 10))->format('Y-m-d'),
                    'jenis' => $jenis,
                    'alasan' => $jenis === 'izin'
                        ? $alasanIzin[array_rand($alasanIzin)]
                        : $alasanSakit[array_rand($alasanSakit)],
                    'lampiran' => null,
                    'status' => $status,
                    'catatan_admin' => $status !== 'pending' ? 'Diproses oleh admin.' : null,
                ]);
            }
        }

        $this->command->info('Seeder PermohonanIzin berhasil dijalankan.');
    }
}