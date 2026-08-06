<?php

namespace App\Livewire\Dashboard;

use App\Enums\JadwalStatusKerja;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Bidang;
use App\Models\DetailJadwal;
use App\Models\Divisi;
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
    
    // State Sekolah
    public $sekolah_id = null;
    public $searchSekolah = '';
    public bool $tambahSekolahBaru = false;
    public string $namaSekolahBaru = '';

    public $mentor = '';
    public $password = '';
    public $confirm_password = '';
    public $status = '';
    
    // Bidang & Divisi
    public $bidang_id = null;
    public $divisi_id = null;

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
     * Filter daftar sekolah berdasarkan input pencarian user.
     */
    public function getDaftarSekolahProperty()
    {
        return Sekolah::query()
            ->when($this->searchSekolah, function ($query) {
                $query->where('nama_sekolah', 'like', '%' . $this->searchSekolah . '%');
            })
            ->orderBy('nama_sekolah')
            ->get();
    }

    /**
     * Pilih sekolah dari list dropdown.
     */
    public function pilihSekolah($id, $nama)
    {
        $this->sekolah_id = $id;
        $this->searchSekolah = $nama;
          $this->tambahSekolahBaru = false;   // ◄ tambahkan
    $this->namaSekolahBaru = '';    
    }

    public function updatedSearchSekolah($value)
    {
        if (empty($value)) {
            $this->sekolah_id = null;
        }
    }

    public function getDaftarBidangProperty()
    {
        return Bidang::orderBy('nama_bidang')->get();
    }

    public function getDaftarDivisiProperty()
    {
        if (!$this->bidang_id) {
            return collect();
        }

        return Divisi::where('bidang_id', $this->bidang_id)
            ->orderBy('nama_divisi')
            ->get();
    }

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
            'bidang_id' => 'required|exists:bidangs,bidang_id',
            'divisi_id' => 'required|exists:divisis,divisi_id',
            'password' => $this->isEditMode
                ? ['nullable', $passwordRule, 'same:confirm_password']
                : ['required', $passwordRule, 'same:confirm_password'],
        ];

        if ($this->isEditMode) {
            $rules['status'] = ['required', Rule::enum(UserStatus::class)];
        }

        // Mentor & Asal Sekolah khusus role PKL
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
        'bidang_id.required' => 'Silakan pilih bidang.',
        'bidang_id.exists' => 'Bidang tidak valid.',
        'divisi_id.required' => 'Silakan pilih divisi.',
        'divisi_id.exists' => 'Divisi tidak valid.',
        'mentor.required' => 'Mentor wajib dipilih atau diisi.',
        'sekolah_id.required' => 'Asal sekolah wajib dipilih dari daftar.',
        'sekolah_id.exists' => 'Pilihan sekolah tidak valid.',
        'namaSekolahBaru.required' => 'Nama sekolah baru wajib diisi.',
        'namaSekolahBaru.min' => 'Nama sekolah minimal 3 karakter.',
        'namaSekolahBaru.unique' => 'Sekolah ini sudah ada di sistem, silakan pilih dari daftar.',
        'password.required' => 'Password wajib diisi.',
        'password.same' => 'Konfirmasi password tidak cocok.',
        'status.required' => 'Status akun wajib dipilih.',
    ];

    public function updatedRole($value)
    {
        if ($value !== UserRole::PKL->value) {
            $this->mentor = '';
            $this->sekolah_id = null;
            $this->searchSekolah = '';
            $this->tambahSekolahBaru = false;
            $this->namaSekolahBaru = '';
        }
    }

    public function updatedBidangId($value)
    {
        $this->divisi_id = null;
    }

    public function resetFields()
    {
        $this->userId = null;
        $this->nama = '';
        $this->email = '';
        $this->role = '';
        $this->status = '';
        $this->bidang_id = null;
        $this->divisi_id = null;
        $this->sekolah_id = null;
        $this->searchSekolah = '';
        $this->tambahSekolahBaru = false;
        $this->namaSekolahBaru = '';
        $this->mentor = '';
        $this->password = '';
        $this->confirm_password = '';
        $this->resetValidation();
    }

    private function applyMentorBidangDivisi(User $mentorUser): void
    {
        $this->mentor = $mentorUser->nama;
        $this->divisi_id = $mentorUser->divisi_id;

        $divisi = Divisi::find($mentorUser->divisi_id);
        $this->bidang_id = $divisi?->bidang_id;
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
            $this->applyMentorBidangDivisi($currentUser);
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
        $this->status = $user->status instanceof \UnitEnum ? $user->status->value : $user->status;
        
        $this->sekolah_id = $user->sekolah_id;
        $this->searchSekolah = $user->sekolah?->nama_sekolah ?? '';

        $currentUser = Auth::user();
        if ($this->isMentor()) {
            $this->applyMentorBidangDivisi($currentUser);
        } else {
            $this->mentor = $user->mentor;
            $this->divisi_id = $user->divisi_id;

            $divisi = Divisi::find($user->divisi_id);
            $this->bidang_id = $divisi?->bidang_id;
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
            $this->applyMentorBidangDivisi($currentUser);
        }

        $this->validate();

        // Opsi Buat Sekolah Baru jika dicentang/pilih tambah
        if ($this->isRolePkl && $this->tambahSekolahBaru && !empty($this->namaSekolahBaru)) {
            $sekolahBaru = Sekolah::create(['nama_sekolah' => trim($this->namaSekolahBaru)]);
            $this->sekolah_id = $sekolahBaru->sekolah_id;
        }

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'nama' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
                'divisi_id' => $this->divisi_id,
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
                'divisi_id' => $this->divisi_id,
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

    public function getDaftarStatusProperty(): array
    {
        return UserStatus::cases();
    }
}