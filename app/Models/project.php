<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class project extends Model
{
     public $timestamps = false;

    protected $table = 'projects';
    protected $primaryKey = 'project_id';

    protected $fillable = [
        'user_id',
        'file_project',
        'link_github',
    ];

      public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
