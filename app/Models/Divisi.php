<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $primaryKey = 'divisi_id';
    protected $fillable = ['bidang_id', 'nama_divisi'];

    // Divisi milik 1 Bidang
    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id', 'bidang_id');
        //                                      ^ FK di tabel divisi   ^ owner key (primary key) di tabel bidang
    }

    // 1 Divisi punya banyak User
    public function users()
    {
        return $this->hasMany(User::class, 'divisi_id', 'divisi_id');
    }
}