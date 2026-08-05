<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $guarded = ['forum_id'];
protected $primaryKey = 'forum_id';
    public function user()
    {
        return $this->belongsTo(User::class ,'user_id', 'user_id');
    }

    public function messages()
    {
     return $this->hasMany(ForumMessage::class, 'forum_id', 'forum_id');
    }

    public function getContentPreviewAttribute()
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->content), 80);
    }
}
