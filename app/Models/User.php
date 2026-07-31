<?php

namespace App\Models;

use App\Enums\UserDivisi;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\User\Presensi;
use App\Models\DetailJadwal;
use App\Models\Project;
use App\Models\Nilai;
use App\Models\file;
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
        'divisi',
        'surat_penerimaan',
        'foto',
        'skill',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'jurusan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'tanggal_mulai' => 'datetime',
        'tanggal_Akhir' => 'date',
        'tanggal_lahir' => 'date',

        // Casting Enum di sini
        'role' => UserRole::class,
        'status' => UserStatus::class,
        'divisi' => UserDivisi::class,
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
    public function presensis(){
        return $this->hasOne(Presensi::class, 'user_id','user_id');
    }

      public function files()
    {
        return $this->hasMany(file::class, 'user_id', 'user_id');
    }
}