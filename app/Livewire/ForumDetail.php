<?php

namespace App\Livewire;

use App\Enums\UserRole;
use Livewire\Component;
use App\Models\Forum;
use App\Models\ForumMessage;
use Illuminate\Support\Facades\Auth;
use App\Services\BadWord;
use Livewire\WithFileUploads; // 1. Import Trait File Uploads

class ForumDetail extends Component
{
    use WithFileUploads; // 2. Pasang Trait ini

    public Forum $forum;
    public $message = '';
    public $gambar; // 3. WAJIB: Properti publik ini yang menyelesaikan error $gambar

    private function layoutUntukRole(): string
    {
        $user = Auth::user();

        $role = $user?->role instanceof \UnitEnum
            ? $user->role->value
            : $user?->role;

        return $role === UserRole::PKL->value
            ? 'layouts.user'
            : 'layouts.dashboard';
    }

    public function mount(Forum $forum)
    {
        $this->forum = $forum->load(['user', 'messages.user']);
    }

    public function sendMessage()
    {
        // Validasi: Pesan boleh kosong jika ada gambar yang diunggah
        $this->validate([
            'message' => 'required_without:gambar|nullable|string',
            'gambar'  => 'nullable|image|max:5120', // Maksimal 5 MB (5120 KB)
        ], [
            'message.required_without' => 'Ketik pesan atau lampirkan gambar.',
            'gambar.max' => 'Ukuran gambar maksimal 5 MB.',
        ]);

        // Cek kata kasar jika pesan diisi
        if ($this->message && BadWord::cek($this->message)) {
            $this->addError('message', 'Pesan Anda mengandung kata-kata yang tidak diperbolehkan.');
            return;
        }

        // Simpan file gambar ke storage
        $gambarPath = null;
        if ($this->gambar) {
            $gambarPath = $this->gambar->store('forum-messages', 'public');
        }

        ForumMessage::create([
            'forum_id' => $this->forum->forum_id,
            'user_id'  => auth()->id(),
            'content'  => $this->message,
            'gambar'   => $gambarPath, // Menyimpan ke kolom DB 'gambar'
        ]);

        $this->reset(['message', 'gambar']);
        $this->forum->load('messages.user'); // Refresh list pesan
    }

    public function render()
    {
        return view('livewire.forum-detail')->layout($this->layoutUntukRole());
    }
}