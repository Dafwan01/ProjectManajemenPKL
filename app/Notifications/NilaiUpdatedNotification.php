<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NilaiUpdatedNotification extends Notification
{
    use Queueable;

    public string $uploaderNama;
    public string $title;
    public string $message;

    public function __construct(string $uploaderNama, string $title = 'Pembaruan Nilai', ?string $message = null)
    {
        $this->uploaderNama = $uploaderNama;
        $this->title = $title;
        $this->message = $message ?? "{$this->uploaderNama} telah memperbarui/mengunggah dokumen magang Anda.";
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'icon'    => 'academic-cap',
            'url'     => route('user.dokumen'),
        ];
    }
}