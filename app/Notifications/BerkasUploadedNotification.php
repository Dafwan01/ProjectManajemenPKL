<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BerkasUploadedNotification extends Notification
{
    use Queueable;

    public string $namaBerkas;
    public string $uploaderNama;

    public function __construct(string $namaBerkas, string $uploaderNama)
    {
        $this->namaBerkas = $namaBerkas;
        $this->uploaderNama = $uploaderNama;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Menyimpan notifikasi ke database
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'     => 'Berkas Baru Diunggah',
            'message'   => "{$this->uploaderNama} telah mengunggah {$this->namaBerkas} Anda.",
            'icon'      => 'document-text',
            'url'       => route('surat-penerimaan-magang'), // Arahkan ke halaman berkas
        ];
    }
}