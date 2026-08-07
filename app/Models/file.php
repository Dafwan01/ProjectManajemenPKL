<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class file extends Model
{
    use LogsActivity;

    protected $table = 'files';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'user_id',
        'nama_file',
        'file',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Konfigurasi Spatie Activitylog untuk Model File
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'nama_file',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('file');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getFileExtensionAttribute(): ?string
    {
        if (empty($this->file)) {
            return null;
        }

        return strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
    }

    public function getFileSizeAttribute(): ?int
    {
        if (empty($this->file)) {
            return null;
        }

        try {
            return Storage::disk('public')->size($this->file);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFileSizeFormattedAttribute(): ?string
    {
        $size = $this->file_size;

        if (! $size) {
            return null;
        }

        if ($size >= 1024 * 1024) {
            return number_format($size / 1024 / 1024, 1) . ' MB';
        }

        return number_format($size / 1024, 1) . ' KB';
    }
}