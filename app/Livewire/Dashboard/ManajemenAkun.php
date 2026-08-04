<?php

namespace App\Livewire\Dashboard;

use App\Enums\JadwalStatusKerja;
use App\Enums\UserDivisi;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\DetailJadwal;
use App\Models\Jadwal;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class ManajemenAkun extends Component
{
    use WithPagination;

    public $userId = null;
    public $nama = '';
    public $email = '';
    public $role = '';
    public $sekolah_id = null;
    public $mentor = '';
    public $password = '';
    public $confirm_password = '';
    public $divisi = '';

    // State Sekolah
    public bool $tambahSekolahBaru = false;
    public string $namaSekolahBaru = '';

    public bool $showModal = false;
    public bool $isEditMode = false;
    public string $search = '';

    private function getUserRole(): string
    {
        $role = Auth::user()?->role;

        return $role instanceof \UnitEnum ? $role->value : (string) $role;
    }

    private function isAdmin(): bool
    {
        return $this->getUserRole() === UserRole::ADMIN->value;
    }

    private function isMentor(): bool
    {
        return $this->getUserRole() === UserRole::MENTOR->value;
    }

    public function getAvailableRolesProperty(): array
    {
        if ($this->isAdmin()) {
            return UserRole::cases();
        }

        if ($this->isMentor()) {
            return [UserRole::PKL];
        }

        return [];
    }

    /**
     * Daftar sekolah untuk dropdown (diambil dari tabel sekolahs).
     */
    public function getDaftarSekolahProperty()
    {
        return Sekolah::orderBy('nama_sekolah')->get();
    }

    /**
     * Cek apakah role yang sedang dipilih di form adalah PKL.
     * Dipakai untuk menentukan apakah field Mentor & Asal Sekolah wajib diisi / dikunci.
     */
    public function getIsRolePklProperty(): bool
    {
        return $this->role === UserRole::PKL->value;
    }

    protected function rules()
    {
        $allowedRoleValues = array_map(fn($role) => $role->value, $this->availableRoles);

        $passwordRule = Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols();

        $rules = [
            'nama' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId . ',user_id',
            'role' => ['required', Rule::in($allowedRoleValues)],
            'divisi' => ['required', Rule::enum(UserDivisi::class)],
            'password' => $this->isEditMode
                ? ['nullable', $passwordRule, 'same:confirm_password']
                : ['required', $passwordRule, 'same:confirm_password'],
        ];

        // Mentor & Asal Sekolah hanya wajib/divalidasi jika role-nya PKL
        if ($this->isRolePkl) {
            $rules['mentor'] = 'required|string';

            if ($this->tambahSekolahBaru) {
                $rules['namaSekolahBaru'] = 'required|string|min:3|unique:sekolahs,nama_sekolah';
            } else {
                $rules['sekolah_id'] = 'required|exists:sekolahs,sekolah_id';
            }
        } else {
            $rules['mentor'] = 'nullable|string';
            $rules['sekolah_id'] = 'nullable|exists:sekolahs,sekolah_id';
        }

        return $rules;
    }

    protected $messages = [
        'nama.required' => 'Nama lengkap wajib diisi.',
        'nama.min' => 'Nama minimal 3 karakter.',
        'email.required' => 'Alamat email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email ini sudah terdaftar.',
        'role.required' => 'Silakan pilih role pengguna.',
        'role.in' => 'Anda tidak memiliki hak akses untuk memilih role tersebut.',
        'divisi.required' => 'Silakan pilih divisi pengguna.',
        'mentor.required' => 'Mentor wajib dipilih atau diisi.',
        'sekolah_id.required' => 'Asal sekolah wajib dipilih.',
        'sekolah_id.exists' => 'Asal sekolah tidak valid.',
        'namaSekolahBaru.required' => 'Nama sekolah baru wajib diisi.',
        'namaSekolahBaru.min' => 'Nama sekolah minimal 3 karakter.',
        'namaSekolahBaru.unique' => 'Sekolah ini sudah terdaftar, silakan pilih dari daftar.',
        'password.required' => 'Password wajib diisi.',
        'password.same' => 'Konfirmasi password tidak cocok.',
    ];

    /**
     * Dipanggil otomatis tiap kali dropdown Role berubah.
     * Kalau role bukan PKL, kosongkan mentor & asal sekolah + reset toggle tambah sekolah.
     */
    public function updatedRole($value)
    {
        if ($value !== UserRole::PKL->value) {
            $this->mentor = '';
            $this->sekolah_id = null;
            $this->tambahSekolahBaru = false;
            $this->namaSekolahBaru = '';
        }
    }

    public function updatedSekolahId($value)
    {
        if ($value === '__tambah_baru__') {
            $this->tambahSekolahBaru = true;
            $this->sekolah_id = null;
        }
    }

    public function batalTambahSekolah()
    {
        $this->tambahSekolahBaru = false;
        $this->namaSekolahBaru = '';
        $this->sekolah_id = null;
    }

    public function resetFields()
    {
        $this->userId = null;
        $this->nama = '';
        $this->email = '';
        $this->role = '';
        $this->divisi = '';
        $this->sekolah_id = null;
        $this->mentor = '';
        $this->password = '';
        $this->confirm_password = '';
        $this->tambahSekolahBaru = false;
        $this->namaSekolahBaru = '';
        $this->resetValidation();
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->isEditMode = false;

        $availableRoles = $this->availableRoles;
        if (count($availableRoles) === 1) {
            $this->role = $availableRoles[0]->value;
        }

        $currentUser = Auth::user();
        if ($this->isMentor()) {
            $this->role = UserRole::PKL->value;
            $this->mentor = $currentUser->nama;
            $this->divisi = $currentUser->divisi instanceof \UnitEnum
                ? $currentUser->divisi->value
                : (string) $currentUser->divisi;
        }

        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetFields();
        $this->isEditMode = true;

        $user = User::findOrFail($id);
        $this->userId = $user->user_id;
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->role = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;
        $this->divisi = $user->divisi instanceof \UnitEnum ? $user->divisi->value : $user->divisi;
        $this->sekolah_id = $user->sekolah_id;

        $currentUser = Auth::user();
        if ($this->isMentor()) {
            $this->mentor = $currentUser->nama;
            $this->divisi = $currentUser->divisi instanceof \UnitEnum
                ? $currentUser->divisi->value
                : (string) $currentUser->divisi;
        } else {
            $this->mentor = $user->mentor;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function save()
    {
        $currentUser = Auth::user();

        if ($this->isMentor()) {
            $this->role = UserRole::PKL->value;
            $this->mentor = $currentUser->nama;
            $this->divisi = $currentUser->divisi instanceof \UnitEnum
                ? $currentUser->divisi->value
                : (string) $currentUser->divisi;
        }

        $this->validate();

        // Jika user menambahkan sekolah baru, simpan ke tabel sekolahs & pakai sebagai sekolah_id
        if ($this->isRolePkl && $this->tambahSekolahBaru && !empty($this->namaSekolahBaru)) {
            $sekolahBaru = Sekolah::create(['nama_sekolah' => $this->namaSekolahBaru]);
            $this->sekolah_id = $sekolahBaru->sekolah_id;
        }

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'nama' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
                'divisi' => $this->divisi,
                'sekolah_id' => $this->isRolePkl ? $this->sekolah_id : null,
                'mentor' => $this->isRolePkl ? $this->mentor : null,
            ];

            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            }

            $user->update($data);
            $this->ensureDefaultScheduleForPkl($user);
            session()->flash('message', 'Akun berhasil diperbarui!');
        } else {
            $user = User::create([
                'nama' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
                'divisi' => $this->divisi,
                'status' => UserStatus::AKTIF->value,
                'sekolah_id' => $this->isRolePkl ? $this->sekolah_id : null,
                'mentor' => $this->isRolePkl ? $this->mentor : null,
                'password' => bcrypt($this->password),
                'tanggal_mulai' => now(),
            ]);
            $this->ensureDefaultScheduleForPkl($user);
            session()->flash('message', 'Akun berhasil dibuat!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Akun berhasil dihapus!');
    }

    #[On('close-jadwal-modal')]
    public function handleCloseJadwalModal()
    {
        $this->closeModal();
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentor();

        $users = User::query()
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('sekolah', function ($q2) {
                          $q2->where('nama_sekolah', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        $mentors = User::where('role', UserRole::MENTOR->value)
            ->where('status', UserStatus::AKTIF->value)
            ->orderBy('nama', 'asc')
            ->get();

        return view('livewire.dashboard.manajemen-akun', compact('users', 'mentors'));
    }

    protected function ensureDefaultScheduleForPkl(User $user): void
    {
        $userRoleValue = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;
        $currentRoleValue = $this->role ?: $userRoleValue;

        if ($currentRoleValue !== UserRole::PKL->value) {
            return;
        }

        $jadwalConfig = [
            'Senin' => ['07:30', '16:00'],
            'Selasa' => ['07:30', '16:00'],
            'Rabu' => ['07:30', '16:00'],
            'Kamis' => ['07:30', '16:00'],
            'Jumat' => ['07:30', '16:30'],
        ];

        DB::transaction(function () use ($user, $jadwalConfig): void {
            foreach ($jadwalConfig as $hari => [$jamMasuk, $jamKeluar]) {
                $jadwal = Jadwal::firstOrCreate([
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,
                    'status_kerja' => JadwalStatusKerja::WFO->value,
                ]);

                DetailJadwal::updateOrCreate(
                    [
                        'user_id' => $user->user_id,
                        'hari' => $hari,
                    ],
                    [
                        'jadwal_id' => $jadwal->jadwal_id,
                    ]
                );
            }
        });
    }
}