<?php

namespace App\Enums;

enum JadwalStatusKerja: string
{
    case WFH = 'WFH';
    case WFO = 'WFO';

    public function label(): string
    {
        return match($this) {
            self::WFH => 'Work From Home',
            self::WFO => 'Work From Office',
        };
    }
}
