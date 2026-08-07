<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NilaiUpdatedNotification extends Notification
{
    use Queueable;

    public string $uploaderNama;

    public function __construct(string $uploaderNama)
    {
        $this->uploaderNama = $uploaderNama;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Nilai',
            'message' => "{$this->uploaderNama} telah memperbarui/mengunggah Nilai magang Anda.",
            'icon'    => 'academic-cap',
            'url'     => route('nilai'),
        ];
    }
}