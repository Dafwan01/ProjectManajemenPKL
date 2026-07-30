<?php

namespace App\Livewire\Dashboard;

use App\Enums\JadwalStatusKerja;
use App\Enums\UserDivisi;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\DetailJadwal;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class ManajemenAkun extends Component
{
    use WithPagination;

    // Property Form
    public $userId = null;
    public $nama = '';
    public $email = '';
    public $role = '';
    public $asal_sekolah = '';
    public $mentor = '';
    public $password = '';
    public $confirm_password = '';
    public $divisi = '';

    // UI States
    public bool $showModal = false;
    public bool $isEditMode = false;
    public string $search = '';

    /**
     * Helper untuk mengambil string value role pengguna secara aman
     */
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

    protected function rules()
    {
        $allowedRoleValues = array_map(fn($role) => $role->value, $this->availableRoles);

        return [
            'nama' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId . ',user_id',
            'role' => ['required', Rule::in($allowedRoleValues)],
            'divisi' => ['required', Rule::enum(UserDivisi::class)],
            'asal_sekolah' => 'nullable|string',
            'mentor' => 'required|string',
            'password' => $this->isEditMode 
                ? 'nullable|min:8|same:confirm_password' 
                : 'required|min:8|same:confirm_password',
        ];
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
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal harus 8 karakter.',
        'password.same' => 'Konfirmasi password tidak cocok.',
    ];

    public function resetFields()
    {
        $this->userId = null;
        $this->nama = '';
        $this->email = '';
        $this->role = '';
        $this->divisi = '';
        $this->asal_sekolah = '';
        $this->mentor = '';
        $this->password = '';
        $this->confirm_password = '';
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
            $this->mentor = $currentUser->nama;
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
        $this->asal_sekolah = $user->asal_sekolah;
        $this->mentor = $user->mentor;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function save()
    {
        if ($this->isMentor()) {
            $this->role = UserRole::PKL->value;
        }

        $this->validate();

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'nama' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
                'divisi' => $this->divisi,
                'asal_sekolah' => $this->asal_sekolah,
                'mentor' => $this->mentor,
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
                'asal_sekolah' => $this->asal_sekolah,
                'mentor' => $this->mentor,
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
                      ->orWhere('asal_sekolah', 'like', '%' . $this->search . '%');
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