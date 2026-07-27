<?php

namespace App\Livewire;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.auth')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $errorMessage = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Password wajib diisi.',
    ];

    public function login()
    {
        $this->errorMessage = '';
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (!Auth::attempt($credentials, $this->remember)) {
            $this->errorMessage = 'Email atau password salah.';
            return;
        }

        request()->session()->regenerate();

        $user = Auth::user();

        if ($user->role?->value === UserRole::PKL->value) {
            return redirect()->route('user.presensi'); // atau route dashboard user PKL kamu
        }

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.login');
    }
}