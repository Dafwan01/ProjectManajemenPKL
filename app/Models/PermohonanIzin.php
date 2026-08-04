<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanIzin extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}