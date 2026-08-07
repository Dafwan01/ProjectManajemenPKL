<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class log_book extends Model
{
    use LogsActivity;

    public $timestamps = false;

    protected $primaryKey = 'log_book_id';

    protected $fillable = [
        'kegiatan',
        'user_id',
        'presensi_id',
    ];

    /**
     * Konfigurasi Spatie Activitylog untuk Model LogBook
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'kegiatan',
                'user_id',
                'presensi_id',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('log_book');
    }

    /**
     * Get the user that owns the log book.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the presensi associated with the log book.
     */
    public function presensi(): BelongsTo
    {
        return $this->belongsTo(presensi::class, 'presensi_id', 'presensi_id');
    }
}