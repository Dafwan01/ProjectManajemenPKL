<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public ?User $user = null;
    public $nama;
    public $email;
    public $asal_sekolah;
    public $mentor;
    public $skill;
    public $tanggal_mulai;
    public $tanggal_akhir;
    public $password;
    public $confirm_password;
    public bool $editing = false;

    protected function rules()
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->user_id, 'user_id'),
            ],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'mentor' => ['nullable', 'string', 'max:255'],
            'skill' => ['nullable', 'string', 'max:500'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'password' => ['nullable', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => ['nullable', 'string'],
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama lengkap wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan oleh akun lain.',
        'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.same' => 'Password dan konfirmasi password harus sama.',
    ];

    public function mount()
    {
        $this->user = Auth::user() ?? User::where('role', UserRole::PKL)->first();

        if (! $this->user) {
            abort(403);
        }

        $this->fillProfileFields();
    }

    private function fillProfileFields(): void
    {
        $this->nama = $this->user->nama;
        $this->email = $this->user->email;
        $this->asal_sekolah = $this->user->asal_sekolah;
        $this->mentor = $this->user->mentor;
        $this->skill = $this->user->skill;
        $this->tanggal_mulai = $this->user->tanggal_mulai?->format('Y-m-d');
        $this->tanggal_akhir = $this->user->tanggal_akhir?->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function startEditing()
    {
        $this->editing = true;
    }

    public function cancelEditing()
    {
        $this->editing = false;
        $this->fillProfileFields();
    }

    public function saveProfile()
    {
        $validated = $this->validate();

        $updateData = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'asal_sekolah' => $validated['asal_sekolah'] ?? null,
            'mentor' => $validated['mentor'] ?? null,
            'skill' => $validated['skill'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_akhir' => $validated['tanggal_akhir'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $this->user->update($updateData);
        $this->editing = false;

        session()->flash('message', 'Profil berhasil diperbarui.');
        $this->fillProfileFields();
    }

    public function render()
    {
        return view('livewire.user.profile')
            ->layout('layouts.user');
    }
}
