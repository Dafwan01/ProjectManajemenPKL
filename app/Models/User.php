<?php

namespace App\Models;

use App\Enums\UserDivisi;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Livewire\User\Presensi;
use App\Models\DetailJadwal;
use App\Models\project;
use App\Models\Nilai;
use App\Models\file;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\HasActivity;


class User extends Authenticatable
{
    use Notifiable, HasActivity,SoftDeletes;

    public $timestamps = false;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'sekolah_id',
        'mentor',
        'status',
        'email',
        'password',
        'tanggal_mulai',
        'tanggal_akhir',
        'role',
        'divisi_id',
        'foto',
        'skill',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'jurusan',
        'jadwal_dilihat_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'tanggal_mulai' => 'datetime',
        'tanggal_akhir' => 'date',
        'tanggal_lahir' => 'date',

        // Casting Enum
        'role' => UserRole::class,
        'status' => UserStatus::class,
    ];

    /**
     * Konfigurasi Spatie Activitylog untuk Model User
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Kolom yang dipantau perubahannya
            ->logOnly([
                'nama',
                'email',
                'role',
                'status',
                'mentor',
                'sekolah_id',
                'divisi_id',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'jurusan',
                'skill',
            ])
            // Pengecualian keamanan
            ->logExcept(['password', 'remember_token'])
            // Hanya catat log jika ada kolom yang nilainya benar-benar berubah
            ->logOnlyDirty()
            // Mencegah log kosong jika tidak ada perubahan data
            ->dontLogEmptyChanges()
            // Tag identifikasi nama log
            ->useLogName('user');
    }

    public function getTanggalAkhirAttribute($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        foreach (['tanggal_akhir', 'tanggal_Akhir'] as $column) {
            if (array_key_exists($column, $this->attributes) && $this->attributes[$column] !== null && $this->attributes[$column] !== '') {
                $rawValue = $this->attributes[$column];

                return $rawValue instanceof \DateTimeInterface
                    ? $rawValue
                    : Carbon::parse($rawValue);
            }
        }

        return null;
    }

    public function setTanggalAkhirAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['tanggal_akhir'] = null;
            if (Schema::hasColumn($this->getTable(), 'tanggal_Akhir')) {
                $this->attributes['tanggal_Akhir'] = null;
            }
            return;
        }

        $dateValue = $value instanceof \DateTimeInterface ? $value : Carbon::parse($value);
        $formatted = $dateValue->format('Y-m-d');

        $this->attributes['tanggal_akhir'] = $formatted;
        if (Schema::hasColumn($this->getTable(), 'tanggal_Akhir')) {
            $this->attributes['tanggal_Akhir'] = $formatted;
        }
    }

    public function getJenisKelaminAttribute($value)
    {
        return $this->normalizeGenderValue($value);
    }

    public function setJenisKelaminAttribute($value)
    {
        $this->attributes['jenis_kelamin'] = $this->normalizeGenderValue($value);
    }

    private function normalizeGenderValue($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match (strtolower(trim((string) $value))) {
            'laki-laki', 'laki laki', 'male', 'pria' => 'Laki-laki',
            'perempuan', 'wanita', 'female' => 'Perempuan',
            default => trim((string) $value),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI MODEL
    |--------------------------------------------------------------------------
    */

    public function detailJadwals()
    {
        return $this->hasMany(DetailJadwal::class, 'user_id', 'user_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id', 'user_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'user_id', 'user_id')->latestOfMany('project_id');
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'user_id', 'user_id');
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class, 'user_id', 'user_id');
    }

    public function presensis()
    {
        return $this->hasOne(Presensi::class, 'user_id', 'user_id');
    }

    public function files()
    {
        return $this->hasMany(file::class, 'user_id', 'user_id');
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }

    public function divisi()
    {
            return $this->belongsTo(Divisi::class, 'divisi_id', 'divisi_id');
    }

    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

    public function forumMessages()
    {
        return $this->hasMany(ForumMessage::class);
    }
}