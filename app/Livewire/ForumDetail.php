<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Forum;
use App\Models\ForumMessage;
use Livewire\Attributes\Layout;

#[Layout('layouts.user')]
class ForumDetail extends Component
{
    public Forum $forum;
    public $message = '';

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
        return view('livewire.forum-detail');
    }
}