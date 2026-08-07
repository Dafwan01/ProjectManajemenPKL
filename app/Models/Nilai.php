<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Nilai extends Model
{
    use LogsActivity;

    protected $table = 'nilais';
    protected $primaryKey = 'nilai_id';

    protected $fillable = [
        'user_id',
        'kedisiplinan',
        'kemampuan_teknis',
        'problem_solving',
        'komunikasi_kerjasama',
        'kualitas_ketepatan',
        'catatan',
    ];

    /**
     * Konfigurasi Spatie Activitylog untuk Model Nilai
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'kedisiplinan',
                'kemampuan_teknis',
                'problem_solving',
                'komunikasi_kerjasama',
                'kualitas_ketepatan',
                'catatan',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('nilai');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getRataRataAttribute()
    {
        $nilai = collect([
            $this->kedisiplinan,
            $this->kemampuan_teknis,
            $this->problem_solving,
            $this->komunikasi_kerjasama,
            $this->kualitas_ketepatan,
        ])->filter(fn ($v) => $v !== null);

        return $nilai->isNotEmpty() ? round($nilai->avg(), 1) : null;
    }
}