<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailJadwal;
use App\Enums\JadwalStatusKerja;


class Jadwal extends Model
{
    public $timestamps = false;

    protected $table = 'jadwals';
    protected $primaryKey = 'jadwal_id';

    protected $fillable = [
        'jam_masuk',
        'jam_keluar',
        'status_kerja',
    ];

    protected $casts = [
        'status_kerja' => JadwalStatusKerja::class,
    ];

    public function detailJadwals()
    {
        return $this->hasMany(DetailJadwal::class, 'jadwal_id', 'jadwal_id');
    }
}
