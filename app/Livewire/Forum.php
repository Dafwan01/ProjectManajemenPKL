<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination; // 1. Trait Pagination
use App\Models\Forum as ForumModel;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use App\Services\BadWord;

class Forum extends Component
{
    use WithFileUploads, WithPagination; // 2. Pasang Trait

    public $showModal = false;
    public $title = '';
    public $content = '';
    public $image;

    // 3. Property Search
    public $search = '';

    protected $rules = [
        'title'   => 'required|min:1|max:255',
        'content' => 'required|min:1',
        'image'   => 'nullable|image|max:2048',
    ];

    // 4. Reset ke halaman 1 setiap kali search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

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
            // 5. Filter + Paginate
            'forums' => ForumModel::with('user')
                ->withCount('messages')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('content', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', function ($uq) {
                              $uq->where('nama', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->latest()
                ->paginate(10),
        ])->layout($this->layoutUntukRole());
    }
}