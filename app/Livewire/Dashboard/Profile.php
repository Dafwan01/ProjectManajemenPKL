<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class Profile extends Component
{
    use WithFileUploads;

    public bool $isEditing = false;

    public User $user;

    public $nama = '';
    public $email = '';
    public $foto = null;
    public $fotoLama = null;

    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        $this->user = User::with(['divisi.bidang'])->find(Auth::id());

        if (! $this->user) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $this->loadUserData();
    }

    private function loadUserData()
    {
        $this->nama = $this->user->nama;
        $this->email = $this->user->email;
        $this->fotoLama = $this->user->foto;
    }

    public function enableEdit()
    {
        $this->isEditing = true;
        $this->resetValidation();
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->loadUserData();
        $this->reset(['foto', 'password', 'password_confirmation']);
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'nama' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user->user_id, 'user_id')],
            'foto' => 'nullable|image|max:2048',
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];

        $this->validate($rules);

        $data = [
            'nama' => $this->nama,
            'email' => $this->email,
        ];

        if ($this->foto) {
            if ($this->fotoLama && Storage::disk('public')->exists($this->fotoLama)) {
                Storage::disk('public')->delete($this->fotoLama);
            }

            $path = $this->foto->store('foto-profil', 'public');
            $data['foto'] = $path;
            $this->fotoLama = $path;
        }

        if (!empty($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        $this->user->update($data);
        $this->user->refresh();

        $this->isEditing = false;
        $this->reset(['foto', 'password', 'password_confirmation']);

        session()->flash('message', 'Profil berhasil diperbarui!');
    }
}