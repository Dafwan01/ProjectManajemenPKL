<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Forum as ForumModel;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;

class Forum extends Component
{
    public $showModal = false;
    public $title = '';
    public $content = '';

    protected $rules = [
        'title'   => 'required|min:1|max:255',
        'content' => 'required|min:1',
    ];

    /**
     * Ambil layout yang sesuai berdasarkan role user yang sedang login.
     * PKL pakai layout khusus user, selain itu (Admin/Mentor) pakai layout dashboard.
     */
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

    public function openModal()
    {
        $this->reset(['title', 'content']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'content']);
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        ForumModel::create([
            'user_id' => auth()->id(),
            'title'   => $this->title,
            'content' => $this->content,
        ]);

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.forum', [
            'forums' => ForumModel::with('user')
                ->withCount('messages')
                ->latest()
                ->get(),
        ])->layout($this->layoutUntukRole());
    }
}