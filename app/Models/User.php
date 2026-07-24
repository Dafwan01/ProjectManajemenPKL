<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\DetailJadwal;
use App\Models\Project;
use App\Models\Nilai;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

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
        'surat_penerimaan',
        'foto',
        'skill',
        'sertifikat',
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

    public function detailJadwals()
    {
        return $this->hasMany(DetailJadwal::class, 'user_id', 'user_id');
    }

     public function projects()
    {
        return $this->hasMany(Project::class, 'user_id', 'user_id');
    }

      public function nilais()
    {
        return $this->hasMany(Nilai::class, 'user_id', 'user_id');
    }

    public function nilai()
{
    return $this->hasOne(Nilai::class, 'user_id', 'user_id');
}
}