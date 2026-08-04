<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Header extends Component
{
    public $notifikasi = [];
    public $jumlahBelumDibaca = 0;

    public function mount()
    {
        $this->loadNotifikasi();
    }

    public function loadNotifikasi()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->notifikasi = $user->notifications()->latest()->take(10)->get();
        $this->jumlahBelumDibaca = $user->unreadNotifications()->count();
    }

    public function tandaiDibaca($id)
    {
        Auth::user()?->notifications()->where('id', $id)->first()?->markAsRead();
        $this->loadNotifikasi();
    }

    public function tandaiSemuaDibaca()
    {
        Auth::user()?->unreadNotifications->markAsRead();
        $this->loadNotifikasi();
    }

    public function render()
    {
        return view('livewire.components.header');
    }
}