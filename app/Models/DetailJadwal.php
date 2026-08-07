<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jadwal;
use App\Models\User;

class DetailJadwal extends Model
{
public $timestamps = true;
    protected $table = 'detail_jadwals';
    protected $primaryKey = 'detail_jadwal_id';

    protected $fillable = [
        'jadwal_id',
        'user_id',
        'hari',
        'created_at', // Tambahkan ini
        'updated_at',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id', 'jadwal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
