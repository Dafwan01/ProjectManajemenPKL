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
    public bool $showPassword = false;
    public bool $agreeTerms = false;
    public bool $showAgreementModal = false;
    public string $captchaInput = '';
    public string $captchaQuestion = '';
    public int $captchaAnswer = 0;
    public string $errorMessage = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
        'captchaInput' => 'required',
        'agreeTerms' => 'accepted',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Password wajib diisi.',
        'captchaInput.required' => 'Captcha wajib diisi.',
        'agreeTerms.accepted' => 'Anda harus menyetujui pernyataan sebelum login.',
    ];

    public function mount()
    {
        $this->generateCaptcha();
    }

    public function login()
    {
        $this->errorMessage = '';
        $this->validate();

        if ((int) trim($this->captchaInput) !== $this->captchaAnswer) {
            $this->addError('captchaInput', 'Jawaban captcha salah. Silakan coba lagi.');
            $this->generateCaptcha();
            return;
        }

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (!Auth::attempt($credentials, $this->remember)) {
            $this->errorMessage = 'Email atau password salah.';
            $this->generateCaptcha();
            return;
        }

        request()->session()->regenerate();

        $user = Auth::user();

        if ($user->role?->value === UserRole::PKL->value) {
            return redirect()->route('user.presensi'); // atau route dashboard user PKL kamu
        }

        return redirect()->route('dashboard');
    }

    public function togglePasswordVisibility(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function generateCaptcha(): void
    {
        $a = rand(1, 9);
        $b = rand(1, 9);

        $this->captchaQuestion = "Berapa $a + $b?";
        $this->captchaAnswer = $a + $b;
        $this->captchaInput = '';
    }

    public function render()
    {
        return view('livewire.login');
    }
}