<?php

namespace App\Livewire;

use App\Enums\UserRole;
use Livewire\Component;
use App\Models\Forum;
use App\Models\ForumMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;


class ForumDetail extends Component
{
    public Forum $forum;
    public $message = '';
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
        $this->validate([
            'message' => 'required|min:1'
        ]);

        ForumMessage::create([
            'forum_id' => $this->forum->forum_id,
            'user_id'  => auth()->id(),
            'content'  => $this->message,
        ]);

        $this->reset('message');
        $this->forum->load('messages.user'); // Refresh list pesan
    }

    public function render()
    {
        return view('livewire.forum-detail')->layout($this->layoutUntukRole());
    }
}