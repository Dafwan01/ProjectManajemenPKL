<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jadwal;
use App\Models\User;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DetailJadwal extends Model
{
    use LogsActivity;

    public $timestamps = true;
    protected $table = 'detail_jadwals';
    protected $primaryKey = 'detail_jadwal_id';

    protected $fillable = [
        'jadwal_id',
        'user_id',
        'hari',
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['jadwal_id', 'hari'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('detail_jadwal');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id', 'jadwal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}