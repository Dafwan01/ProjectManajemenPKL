<?php

namespace App\Livewire;

use App\Enums\UserRole;
use Livewire\Component;
use App\Models\Forum;
use App\Models\ForumMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\BadWord;
use Livewire\WithFileUploads;

class ForumDetail extends Component
{
    use WithFileUploads;

    public Forum $forum;
    public string $message = '';
    public $gambar;

    // Properti Edit Modal
    public bool $showEditModal = false;
    public string $editTitle = '';
    public string $editContent = '';
    public $editImage;
    public ?string $existingImage = null;

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

    public function mount(Forum $forum)
    {
        $this->forum = $forum->load(['user', 'messages.user']);
    }

    public function openEditModal()
    {
        $user = Auth::user();
        $authId = Auth::id();

        $role = $user?->role instanceof \UnitEnum
            ? $user->role->value
            : (string) $user?->role;

        // Otorisasi: Pembuat forum ATAU Admin (mentor & user lain hanya boleh miliknya sendiri)
        $isOwner = (string) $this->forum->user_id === (string) $authId;
        $isAdmin = ($role === UserRole::ADMIN->value);

        if (!$isOwner && !$isAdmin) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk mengedit forum ini.');
            return;
        }

        $this->editTitle = $this->forum->title;
        $this->editContent = $this->forum->content;
        $this->existingImage = $this->forum->gambar;
        $this->editImage = null;

        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editTitle', 'editContent', 'editImage', 'existingImage']);
        $this->resetErrorBag();
    }

    public function removeExistingImage()
    {
        $this->existingImage = null;
    }

    public function updateForum()
    {
        $this->validate([
            'editTitle'   => 'required|min:1|max:255',
            'editContent' => 'required|min:1',
            'editImage'   => 'nullable|image|max:2048',
        ], [
            'editTitle.required'   => 'Judul forum wajib diisi!',
            'editTitle.max'        => 'Judul forum maksimal 255 karakter!',
            'editContent.required' => 'Isi forum wajib diisi!',
            'editImage.image'      => 'Berkas harus berupa gambar!',
            'editImage.max'        => 'Ukuran gambar maksimal 2 MB!',
        ]);

        if (BadWord::cek($this->editTitle)) {
            $this->addError('editTitle', 'Judul forum mengandung kata-kata yang tidak diperbolehkan.');
            return;
        }

        if (BadWord::cek($this->editContent)) {
            $this->addError('editContent', 'Isi forum mengandung kata-kata yang tidak diperbolehkan.');
            return;
        }

        $user = Auth::user();
        $authId = Auth::id();

        $role = $user?->role instanceof \UnitEnum 
            ? $user->role->value 
            : (string) $user?->role;

        $isOwner = (string) $this->forum->user_id === (string) $authId;
        $isAdmin = ($role === UserRole::ADMIN->value);

        if (!$isOwner && !$isAdmin) {
            session()->flash('error', 'Anda tidak memiliki hak akses.');
            return;
        }

        $imagePath = $this->forum->gambar;

        if ($this->editImage) {
            if ($this->forum->gambar) {
                Storage::disk('public')->delete($this->forum->gambar);
            }
            $imagePath = $this->editImage->store('forums', 'public');
        } elseif (!$this->existingImage && $this->forum->gambar) {
            Storage::disk('public')->delete($this->forum->gambar);
            $imagePath = null;
        }

        $this->forum->update([
            'title'   => $this->editTitle,
            'content' => $this->editContent,
            'gambar'  => $imagePath,
        ]);

        $this->forum->refresh();
        $this->closeEditModal();
        session()->flash('message', 'Topik forum berhasil diperbarui.');
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required_without:gambar|nullable|string',
            'gambar'  => 'nullable|image|max:5120',
        ], [
            'message.required_without' => 'Ketik pesan atau lampirkan gambar terlebih dahulu!',
            'gambar.image'            => 'Berkas harus berupa gambar!',
            'gambar.max'              => 'Ukuran gambar maksimal 5 MB!',
        ]);

        if ($this->message && BadWord::cek($this->message)) {
            $this->addError('message', 'Pesan Anda mengandung kata-kata yang tidak diperbolehkan.');
            return;
        }

        $gambarPath = null;
        if ($this->gambar) {
            $gambarPath = $this->gambar->store('forum-messages', 'public');
        }

        ForumMessage::create([
            'forum_id' => $this->forum->forum_id,
            'user_id'  => Auth::id(),
            'content'  => $this->message,
            'gambar'   => $gambarPath,
        ]);

        $this->reset(['message', 'gambar']);
        $this->forum->load('messages.user');
    }

    public function delete()
    {
        $user = Auth::user();
        $authId = Auth::id();

        $role = $user?->role instanceof \UnitEnum
            ? $user->role->value
            : (string) $user?->role;

        // Otorisasi Backend
        $isOwner = (string) $this->forum->user_id === (string) $authId;
        $isAdmin = ($role === UserRole::ADMIN->value);

        if (!$isOwner && !$isAdmin) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk menghapus forum ini.');
            return;
        }

        if ($this->forum->gambar) {
            Storage::disk('public')->delete($this->forum->gambar);
        }

        foreach ($this->forum->messages as $msg) {
            if ($msg->gambar) {
                Storage::disk('public')->delete($msg->gambar);
            }
        }

        $this->forum->delete();

        session()->flash('message', 'Topik forum berhasil dihapus.');
        return redirect()->route('forum');
    }

    public function render()
    {
        return view('livewire.forum-detail')->layout($this->layoutUntukRole());
    }
}
