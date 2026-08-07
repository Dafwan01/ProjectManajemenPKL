<?php

namespace App\Livewire;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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
    
    // State Captcha & Rate Limiter Lockout
    public string $captchaInput = '';
    public string $captchaImage = '';
    public string $captchaCode = '';
    public string $errorMessage = '';
    public bool $isLocked = false;
    public int $secondsRemaining = 0;

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

    // Cek status rate limit saat email diubah
    public function updatedEmail(): void
    {
        $this->checkRateLimitStatus();
    }

    private function checkRateLimitStatus(): void
    {
        if (empty($this->email)) return;

        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->isLocked = true;
            $this->secondsRemaining = RateLimiter::availableIn($throttleKey);
        } else {
            $this->isLocked = false;
            $this->secondsRemaining = 0;
        }
    }

    public function generateCaptcha(): void
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        $this->captchaCode = $code;

        $width = 220;
        $height = 65;

        $svg = "<svg width='{$width}' height='{$height}' xmlns='http://www.w3.org/2000/svg' style='background-color:#eff6ff; border-radius: 8px;'>";

        for ($i = 0; $i < 6; $i++) {
            $x1 = rand(0, $width); $y1 = rand(0, $height);
            $x2 = rand(0, $width); $y2 = rand(0, $height);
            $svg .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='#93c5fd' stroke-width='1.5' opacity='0.7'/>";
        }

        for ($i = 0; $i < 30; $i++) {
            $cx = rand(0, $width); $cy = rand(0, $height);
            $svg .= "<circle cx='{$cx}' cy='{$cy}' r='1.5' fill='#3b82f6' opacity='0.4'/>";
        }

        $charArray = str_split($code);
        $x = 25;
        foreach ($charArray as $char) {
            $y = rand(40, 48);
            $angle = rand(-20, 20);
            $svg .= "<text x='{$x}' y='{$y}' font-family='Arial, sans-serif' font-weight='900' font-size='30' fill='#1d4ed8' transform='rotate({$angle} {$x} {$y})'>{$char}</text>";
            $x += 36;
        }

        $svg .= '</svg>';

        $this->captchaImage = 'data:image/svg+xml;base64,' . base64_encode($svg);
        $this->captchaInput = '';
    }

    public function login()
    {
        $this->errorMessage = '';

        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        // 1. Cek apakah user sedang terkunci
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->isLocked = true;
            $this->secondsRemaining = RateLimiter::availableIn($throttleKey);
            $this->errorMessage = "Terlalu banyak percobaan login yang salah. Silakan tunggu hingga hitungan mundur selesai.";
            $this->generateCaptcha();
            return;
        }

        $this->validate();

        // Cek Captcha
        if (strtoupper(trim($this->captchaInput)) !== strtoupper($this->captchaCode)) {
            $this->addError('captchaInput', 'Kode captcha salah. Silakan coba lagi.');
            $this->generateCaptcha();
            return;
        }

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        // 2. Eksekusi Attempt Login
        if (!Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($throttleKey, 60);

            $attemptsLeft = RateLimiter::remaining($throttleKey, 3);

            if ($attemptsLeft > 0) {
                $this->errorMessage = "Email atau password salah. Sisa percobaan: {$attemptsLeft}x lagi.";
            } else {
                $this->isLocked = true;
                $this->secondsRemaining = RateLimiter::availableIn($throttleKey);
                $this->errorMessage = "Terlalu banyak percobaan login yang salah. Akses dikunci sementara selama 1 menit.";
            }

            $this->generateCaptcha();
            return;
        }

        // Jika berhasil login, reset limiter
        RateLimiter::clear($throttleKey);

        request()->session()->regenerate();

        // Hapus intended URL dari session agar user PKL tidak dipaksa ke /dashboard
        session()->forget('url.intended');

        $user = Auth::user();
        $userRole = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;

        // Redirect sesuai role
        if ($userRole === UserRole::PKL->value) {
            return redirect()->route('user.presensi');
        }

        return redirect()->route('dashboard');
    }

    public function togglePasswordVisibility(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function openAgreementModal(): void
    {
        $this->showAgreementModal = true;
    }

    public function closeAgreementModal(): void
    {
        $this->showAgreementModal = false;
    }

    public function acceptTermsAndCloseModal(): void
    {
        $this->agreeTerms = true;
        $this->showAgreementModal = false;
    }

    public function render()
    {
        return view('livewire.login');
    }
}