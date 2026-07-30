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
    
    // State Captcha
    public string $captchaInput = '';
    public string $captchaImage = '';
    public string $captchaCode = '';
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

    public function generateCaptcha(): void
    {
        // 1. Buat 5 karakter acak (tanpa Karakter membingungkan seperti 0, O, 1, I)
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        $this->captchaCode = $code;

        // 2. Buat Tampilan Visual Gambar Captcha menggunakan SVG
        $width = 220;
        $height = 65;

        $svg = "<svg width='{$width}' height='{$height}' xmlns='http://www.w3.org/2000/svg' style='background-color:#eff6ff; border-radius: 8px;'>";

        // Tambahkan garis pengganggu (noise lines)
        for ($i = 0; $i < 6; $i++) {
            $x1 = rand(0, $width); $y1 = rand(0, $height);
            $x2 = rand(0, $width); $y2 = rand(0, $height);
            $svg .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='#93c5fd' stroke-width='1.5' opacity='0.7'/>";
        }

        // Tambahkan titik-titik pengganggu (noise dots)
        for ($i = 0; $i < 30; $i++) {
            $cx = rand(0, $width); $cy = rand(0, $height);
            $svg .= "<circle cx='{$cx}' cy='{$cy}' r='1.5' fill='#3b82f6' opacity='0.4'/>";
        }

        // Cetak Karakter dengan Rotasi & Posisi Acak
        $charArray = str_split($code);
        $x = 25;
        foreach ($charArray as $char) {
            $y = rand(40, 48);
            $angle = rand(-20, 20);
            $svg .= "<text x='{$x}' y='{$y}' font-family='Arial, sans-serif' font-weight='900' font-size='30' fill='#1d4ed8' transform='rotate({$angle} {$x} {$y})'>{$char}</text>";
            $x += 36;
        }

        $svg .= '</svg>';

        // Convert ke format Data URI
        $this->captchaImage = 'data:image/svg+xml;base64,' . base64_encode($svg);
        $this->captchaInput = '';
    }

    public function login()
    {
        $this->errorMessage = '';
        $this->validate();

        // Cek captcha (Case Insensitive)
        if (strtoupper(trim($this->captchaInput)) !== strtoupper($this->captchaCode)) {
            $this->addError('captchaInput', 'Kode captcha salah. Silakan coba lagi.');
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

        $userRole = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;

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

    // Method untuk menyetujui pernyataan langsung dari modal
public function acceptTermsAndCloseModal(): void
{
    $this->agreeTerms = true;        // Otomatis checklist checkbox
    $this->showAgreementModal = false; // Tutup modal
}

    public function render()
    {
        return view('livewire.login');
    }
}