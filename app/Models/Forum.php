<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Forum extends Model
{
    use LogsActivity;

    protected $guarded = ['forum_id'];
    protected $primaryKey = 'forum_id';

    /**
     * Konfigurasi Spatie Activitylog untuk Model Forum
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['created_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('forum');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
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