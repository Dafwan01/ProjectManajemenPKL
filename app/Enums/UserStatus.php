<?php

namespace App\Enums;

enum UserStatus: string
{
    case AKTIF = 'Aktif';
    case LULUS = 'Lulus'; // <-- Ubah 'lulus' menjadi 'LULUS' (Huruf Kapital)

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::LULUS => 'Lulus',
        };
    }
}