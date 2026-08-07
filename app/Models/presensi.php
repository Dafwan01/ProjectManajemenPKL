<?php

namespace App\Models;

use App\Enums\PresensiStatusKehadiran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class presensi extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $primaryKey = 'presensi_id';

    protected $fillable = [
        'user_id',
        'foto_masuk',
        'foto_keluar',
        'tanggal',
        'status_kehadiran',
        'absen_masuk',
        'absen_keluar',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status_kehadiran' => PresensiStatusKehadiran::class,
    ];

    /**
     * Konfigurasi Spatie Activitylog untuk Model Presensi
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'tanggal',
                'status_kehadiran',
                'absen_masuk',
                'absen_keluar',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('presensi');
    }

    /**
     * Get the log books associated with this presensi.
     */
    public function logBooks(): HasMany
    {
        return $this->hasMany(log_book::class, 'presensi_id', 'presensi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}