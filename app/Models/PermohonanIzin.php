<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class PermohonanIzin extends Model
{
    use LogsActivity;

    protected $table = 'permohonan_izins';
    protected $primaryKey = 'permohonan_id';

    protected $fillable = [
        'user_id',
        'tanggal_permohonan',
        'tanggal_awal',
        'tanggal_akhir',
        'jenis',
        'alasan',
        'lampiran',
        'status',
        'catatan_admin',
        'alamat_izin',
        'jumlah_hari',
        'absen_masuk',
        'absen_pulang',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'absen_masuk' => 'boolean',
        'absen_pulang' => 'boolean',
    ];

    /**
     * Konfigurasi Spatie Activitylog untuk Model PermohonanIzin
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'tanggal_permohonan',
                'tanggal_awal',
                'tanggal_akhir',
                'jenis',
                'alasan',
                'status',
                'catatan_admin',
                'jumlah_hari',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('permohonan_izin');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}