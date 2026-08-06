<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // 1. Trait Upload File
use App\Models\Forum as ForumModel;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use App\Services\BadWord;

class Forum extends Component
{
    use WithFileUploads; // 2. Pasang Trait

    public $showModal = false;
    public $title = '';
    public $content = '';
    public $image; // 3. Property Gambar

    protected $rules = [
        'title'   => 'required|min:1|max:255',
        'content' => 'required|min:1',
        'image'   => 'nullable|image|max:2048', // Maksimal 2MB (jpg, png, webp)
    ];

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
        $this->reset(['title', 'content', 'image']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'content', 'image']);
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        if (BadWord::cek($this->title)) {
            $this->addError('title', 'Judul forum mengandung kata-kata yang tidak diperbolehkan.');
            return;
        }

        if (BadWord::cek($this->content)) {
            $this->addError('content', 'Isi forum mengandung kata-kata yang tidak diperbolehkan.');
            return;
        }

        // 4. Proses Simpan Gambar jika di-upload
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('forums', 'public');
        }

        ForumModel::create([
            'user_id' => auth()->id(),
            'title'   => $this->title,
            'content' => $this->content,
            'gambar'   => $imagePath,
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