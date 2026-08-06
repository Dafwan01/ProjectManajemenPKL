<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Models\Sekolah;
use App\Enums\UserRole;
use App\Models\Divisi;
use App\Models\Bidang;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public ?User $user = null;
    public $nama;
    public $email;
    public $sekolah_id;

    // Bidang & Divisi ditentukan oleh admin, user hanya bisa melihat (read-only).
    public $divisi_id;

    public $mentor;
    public $skill;
    public $tanggal_mulai;
    public $tanggal_akhir;
    public $password;
    public $confirm_password;
    public bool $editing = false;

    // Field Tambahan
    public $tempat_lahir;
    public $tanggal_lahir;
    public $jenis_kelamin;
    public $jurusan;

    // Foto profil
    public $fotoUpload = null;   // upload file biasa
    public $fotoCaptured = null; // hasil jepretan kamera (base64)
    public bool $showPhotoOptions = false;

    #[Computed]
    public function daftarSekolah()
    {
        return Sekolah::orderBy('nama_sekolah', 'asc')->get();
    }

    // Dipakai untuk menampilkan nama Divisi & Bidang milik user (read-only).
    // Sengaja ambil kolom bidang_id langsung (bukan lewat relasi $divisi->bidang)
    // supaya tidak tergantung pada guessing foreign key Eloquent yang pernah bermasalah.
    #[Computed]
    public function divisiSaatIni()
    {
        if (empty($this->divisi_id)) {
            return null;
        }

        return Divisi::find($this->divisi_id);
    }

    #[Computed]
    public function bidangSaatIni()
    {
        $divisi = $this->divisiSaatIni;

        if (! $divisi || empty($divisi->bidang_id)) {
            return null;
        }

        return Bidang::find($divisi->bidang_id);
    }

    protected function rules()
    {
        $passwordRule = Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols();

        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->user_id, 'user_id'),
            ],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
           'sekolah_id' => ['nullable', 'integer', Rule::exists('sekolahs', 'sekolah_id')],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'skill' => ['nullable', 'string', 'max:500'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'password' => ['nullable', $passwordRule, 'same:confirm_password'],
            'confirm_password' => ['nullable', 'string'],
            'fotoUpload' => ['nullable', 'image', 'max:2048'],
            'fotoCaptured' => ['nullable', 'string', 'max:5000000'],
            
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama lengkap wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan oleh akun lain.',
        'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
        'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        'password.same' => 'Password dan konfirmasi password harus sama.',
        'fotoUpload.image' => 'File harus berupa gambar.',
        'fotoUpload.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    public function mount()
    {
        $this->user = Auth::user();

        if (! $this->user) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $this->fillProfileFields();
    }

    private function fillProfileFields(): void
    {
        $this->nama = $this->user->nama;
        $this->email = $this->user->email;
        $this->tempat_lahir = $this->user->tempat_lahir;
        $this->tanggal_lahir = $this->formatDateForInput($this->user->tanggal_lahir);
        $this->jenis_kelamin = $this->normalizeGenderValue($this->user->jenis_kelamin);
        $this->sekolah_id = $this->user->sekolah_id;
        $this->jurusan = $this->user->jurusan;
        $this->mentor = $this->user->mentor;
        $this->skill = $this->user->skill;
        $this->tanggal_mulai = $this->formatDateForInput($this->user->tanggal_mulai);
        $this->tanggal_akhir = $this->formatDateForInput($this->user->tanggal_akhir);

        $this->divisi_id = $this->user->divisi_id;
    }

    private function formatDateForInput($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function normalizeGenderValue($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return match (strtolower(trim((string) $value))) {
            'laki-laki', 'laki laki', 'male', 'pria' => 'Laki-laki',
            'perempuan', 'wanita', 'female' => 'Perempuan',
            default => trim((string) $value),
        };
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'fotoUpload' || $propertyName === 'fotoCaptured') {
            return;
        }

        $this->validateOnly($propertyName);
    }

    public function startEditing()
    {
        $this->editing = true;
    }

    public function cancelEditing()
    {
        $this->editing = false;
        $this->fotoUpload = null;
        $this->fotoCaptured = null;
        $this->showPhotoOptions = false;
        $this->password = null;
        $this->confirm_password = null;
        $this->fillProfileFields();
    }

    public function togglePhotoOptions()
    {
        $this->showPhotoOptions = ! $this->showPhotoOptions;
    }

    public function saveProfile()
    {
        $validated = $this->validate();

        $updateData = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'sekolah_id' => $validated['sekolah_id'] ?? null,
            'jurusan' => $validated['jurusan'] ?? null,
            'skill' => $validated['skill'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_akhir' => $validated['tanggal_akhir'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $fotoPath = $this->simpanFoto();
        if ($fotoPath) {
            $updateData['foto'] = $fotoPath;
        }

        $this->user->update($updateData);
        $this->user->refresh();

        $this->editing = false;
        $this->fotoUpload = null;
        $this->fotoCaptured = null;
        $this->showPhotoOptions = false;
        $this->password = null;
        $this->confirm_password = null;

        session()->flash('message', 'Profil berhasil diperbarui.');
        $this->fillProfileFields();

        return redirect()->route('user.profile');
    }

    private function simpanFoto(): ?string
    {
        $namaFile = Str::slug($this->user->nama) . '-profile-' . time();

        $hapusFotoLama = function () {
            if ($this->user->foto && Storage::disk('public')->exists($this->user->foto)) {
                Storage::disk('public')->delete($this->user->foto);
            }
        };

        if ($this->fotoUpload) {
            $hapusFotoLama();
            $extension = $this->fotoUpload->getClientOriginalExtension();
            return $this->fotoUpload->storeAs('profile', $namaFile . '.' . $extension, 'public');
        }

        if ($this->fotoCaptured) {
            $hapusFotoLama();
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $this->fotoCaptured);
            $imageData = base64_decode($imageData);

            $path = 'profile/' . $namaFile . '.jpg';
            Storage::disk('public')->put($path, $imageData);
            return $path;
        }

        return null;
    }

    public function render()
    {
        return view('livewire.user.profile')
            ->layout('layouts.user');
    }
}