<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\HasActivity;

class Sekolah extends Model
{
    use HasActivity;

    protected $primaryKey = 'sekolah_id';

    protected $fillable = ['nama_sekolah'];

    /**
     * Konfigurasi Spatie Activitylog untuk Model Sekolah
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Dipantau perubahannya
            ->logOnly(['nama_sekolah'])
            // Hanya simpan jika nilai nama_sekolah berubah
            ->logOnlyDirty()
            // Mencegah log kosong
            ->dontLogEmptyChanges()
            // Tag identifikasi nama log
            ->useLogName('sekolah');
    }

    /**
     * Relasi ke Model User (Satu Sekolah memiliki banyak User/Siswa PKL)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'sekolah_id', 'sekolah_id');
    }
}