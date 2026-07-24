<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermohonanIzin extends Model
{
    protected $table = 'permohonan_izins';
    protected $primaryKey = 'permohonan_id';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jenis',
        'alasan',
        'lampiran',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}