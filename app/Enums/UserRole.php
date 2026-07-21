<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MENTOR = 'mentor';
    case PKL = 'PKL';

    // Label rapi untuk ditampilkan di UI/Blade
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MENTOR => 'Mentor / Pembimbing',
            self::PKL => 'Siswa/Mahasiswa PKL',
        };
    }
}