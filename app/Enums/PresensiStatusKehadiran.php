<?php

namespace App\Enums;

enum PresensiStatusKehadiran: string
{
    case Hadir = 'hadir';
    case TidakHadir = 'tidak_hadir';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Terlambat = 'terlambat';

    // Label rapi untuk ditampilkan di UI/Blade
    public function label(): string
    {
        return match($this) {
            self::Hadir => 'Hadir',
            self::TidakHadir => 'Tidak Hadir',
            self::Izin => 'Izin',
            self::Sakit => 'Sakit',
            self::Terlambat => 'Terlambat',
        };
    }
}