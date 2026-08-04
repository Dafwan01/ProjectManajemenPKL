<?php

namespace App\Models;

use App\Models\User;
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
        'nama_project',
        'project_status',
        'kolaborator_ids',
    ];

    protected $casts = [
        'kolaborator_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getKolaboratorUsersAttribute()
    {
        if (empty($this->kolaborator_ids) || !is_array($this->kolaborator_ids)) {
            return collect();
        }

        return User::whereIn('user_id', $this->kolaborator_ids)->orderBy('nama')->get();
    }
}
