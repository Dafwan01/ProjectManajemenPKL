<?php

namespace App\Enums;

enum UserStatus: string
{
    case AKTIF = 'Aktif';
    case lulus = 'Lulus';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::lulus => 'Lulus',
        };
    }
}