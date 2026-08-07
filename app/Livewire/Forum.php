<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Forum as ForumModel;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\BadWord;

class Forum extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $title = '';
    public string $content = '';
    public $image;
    public ?string $existingGambar = null;

    protected array $rules = [
        'title'   => 'required|min:1|max:255',
        'content' => 'required|min:1',
        'image'   => 'nullable|image|max:2048',
    ];

    protected array $messages = [
        'title.required'   => 'Judul forum wajib diisi!',
        'title.max'        => 'Judul forum maksimal 255 karakter!',
        'content.required' => 'Isi forum wajib diisi!',
        'image.image'      => 'Berkas harus berupa gambar (JPG, PNG, WEBP)!',
        'image.max'        => 'Ukuran gambar maksimal 2 MB!',
    ];

    private function layoutUntukRole(): string
    {
        $user = Auth::user();

        $role = $user?->role instanceof \UnitEnum
            ? $user->role->value
            : (string) $user?->role;

        return $role === UserRole::PKL->value
            ? 'layouts.user'
            : 'layouts.dashboard';
    }

    public function openModal()
    {
        $this->reset(['title', 'content', 'image', 'existingGambar', 'editingId']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $forum = ForumModel::findOrFail($id);
        $user = Auth::user();
        $authId = Auth::id();

        $role = $user?->role instanceof \UnitEnum
            ? $user->role->value
            : (string) $user?->role;

        // Otorisasi: Pembuat forum ATAU Admin/Non-PKL
        $isOwner = (string) $forum->user_id === (string) $authId;
        $isAdmin = ($role === UserRole::ADMIN->value) || ($role !== UserRole::PKL->value);

        if (!$isOwner && !$isAdmin) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk mengubah forum ini.');
            return;
        }

        $this->editingId = $forum->forum_id;
        $this->title = $forum->title;
        $this->content = $forum->content;
        $this->existingGambar = $forum->gambar;
        $this->image = null;

        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function removeExistingGambar()
    {
        $this->existingGambar = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'content', 'image', 'existingGambar', 'editingId']);
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

        if ($this->editingId) {
            // PROSES UPDATE FORUM
            $forum = ForumModel::findOrFail($this->editingId);
            $user = Auth::user();
            $authId = Auth::id();

            $role = $user?->role instanceof \UnitEnum
                ? $user->role->value
                : (string) $user?->role;

            // Otorisasi Backend
            $isOwner = (string) $forum->user_id === (string) $authId;
            $isAdmin = ($role === UserRole::ADMIN->value) || ($role !== UserRole::PKL->value);

            if (!$isOwner && !$isAdmin) {
                session()->flash('error', 'Anda tidak memiliki hak akses.');
                return;
            }

            $imagePath = $forum->gambar;

            if ($this->image) {
                if ($forum->gambar) {
                    Storage::disk('public')->delete($forum->gambar);
                }
                $imagePath = $this->image->store('forums', 'public');
            } elseif (!$this->existingGambar && $forum->gambar) {
                Storage::disk('public')->delete($forum->gambar);
                $imagePath = null;
            }

            $forum->update([
                'title'   => $this->title,
                'content' => $this->content,
                'gambar'  => $imagePath,
            ]);

            session()->flash('message', 'Topik forum berhasil diperbarui.');
        } else {
            // PROSES SIMPAN FORUM BARU
            $imagePath = null;
            if ($this->image) {
                $imagePath = $this->image->store('forums', 'public');
            }

            ForumModel::create([
                'user_id' => Auth::id(),
                'title'   => $this->title,
                'content' => $this->content,
                'gambar'  => $imagePath,
            ]);

            session()->flash('message', 'Topik forum baru berhasil dibuat.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $forum = ForumModel::with('messages')->findOrFail($id);
        $user = Auth::user();
        $authId = Auth::id();

        $role = $user?->role instanceof \UnitEnum
            ? $user->role->value
            : (string) $user?->role;

        // Otorisasi Backend
        $isOwner = (string) $forum->user_id === (string) $authId;
        $isAdmin = ($role === UserRole::ADMIN->value) || ($role !== UserRole::PKL->value);

        if (!$isOwner && !$isAdmin) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk menghapus forum ini.');
            return;
        }

        if ($forum->gambar) {
            Storage::disk('public')->delete($forum->gambar);
        }

        foreach ($forum->messages as $msg) {
            if ($msg->gambar) {
                Storage::disk('public')->delete($msg->gambar);
            }
        }

        $forum->delete();
        session()->flash('message', 'Topik forum berhasil dihapus.');
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
