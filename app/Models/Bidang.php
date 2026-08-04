<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
      protected $primaryKey = 'bidang_id';
    protected $fillable = ['nama_bidang'];

    public function divisis()
    {
        return $this->hasMany(Divisi::class);
    }
}
