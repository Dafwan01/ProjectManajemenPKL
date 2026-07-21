<?php

namespace App\Enums;

enum UserStatus: string
{
    case AKTIF = 'Aktif';
    case TIDAK_AKTIF = 'Tidak Aktif';
    case PENDING = 'Pending';
}