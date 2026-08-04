<?php

namespace App\Notifications;

use App\Models\presensi as PresensiModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PresensiTerlambatNotification extends Notification
{
    use Queueable;

    public function __construct(public PresensiModel $presensi)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $siswa = $this->presensi->user;

        return [
            'type'        => 'presensi_terlambat',
            'presensi_id' => $this->presensi->presensi_id,
            'user_id'     => $siswa->user_id,
            'nama'        => $siswa->nama,
            'foto'        => $siswa->foto,
            'tanggal'     => $this->presensi->tanggal->format('Y-m-d'),
            'absen_masuk' => $this->presensi->absen_masuk,
            'message'     => "{$siswa->nama} terlambat absen masuk pada "
                . $this->presensi->tanggal->translatedFormat('d M Y')
                . " pukul {$this->presensi->absen_masuk}",
        ];
    }
}