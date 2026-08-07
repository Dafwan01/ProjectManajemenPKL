<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ForumMessage extends Model
{
    use LogsActivity;

    protected $guarded = ['message_id'];

    /**
     * Konfigurasi Spatie Activitylog untuk Model ForumMessage
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['created_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('forum_message');
    }

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