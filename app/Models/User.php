<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'asal_sekolah',
        'mentor',
        'status',
        'email',
        'password',
        'tanggal_mulai',
        'tanggal_Akhir',
        'role',
        'nilai',
        'surat_penerimaan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'tanggal_mulai' => 'datetime',
        'tanggal_Akhir' => 'date',

        // Casting Enum di sini
        'role' => UserRole::class,
        'status' => UserStatus::class,
    ];
}