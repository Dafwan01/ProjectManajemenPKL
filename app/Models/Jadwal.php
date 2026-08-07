<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailJadwal;
use App\Enums\JadwalStatusKerja;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Jadwal extends Model
{
    use LogsActivity;

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

    /**
     * Konfigurasi Spatie Activitylog untuk Model Jadwal
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'jam_masuk',
                'jam_keluar',
                'status_kerja',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('jadwal');
    }

    public function detailJadwals()
    {
        return $this->hasMany(DetailJadwal::class, 'jadwal_id', 'jadwal_id');
    }
}