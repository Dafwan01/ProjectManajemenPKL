<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class log_book extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'log_book_id';

    protected $fillable = [
        'kegiatan',
        'user_id',
        'presensi_id',
    ];

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
