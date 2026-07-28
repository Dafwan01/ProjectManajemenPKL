<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class file extends Model
{
     public $timestamps = false;

    protected $table = 'files';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'user_id',
        'nama_file',
        'file',
    ];

      public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
