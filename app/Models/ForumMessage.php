<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumMessage extends Model
{
    protected $guarded = ['message_id'];

    // Relasi kembali ke topik Forum utama
    public function forum()
    {
      return $this->belongsTo(Forum::class, 'forum_id', 'forum_id');

    }

    // Relasi ke User pengirim pesan
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
