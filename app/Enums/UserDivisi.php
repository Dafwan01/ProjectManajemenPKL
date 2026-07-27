<?php

namespace App\Enums;

enum UserDivisi: string
{
    case APTIKA = 'APTIKA';
    case Jaringan = 'Jaringan';
    case Multimedia = 'Multimedia';

    // Label rapi untuk ditampilkan di UI/Blade
    public function label(): string
    {
        return match($this) {
            self::APTIKA => 'APTIKA',
            self::Jaringan => 'Jaringan',
            self::Multimedia => 'Multimedia',
        };
    }
}