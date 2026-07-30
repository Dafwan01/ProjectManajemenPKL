<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserDivisi;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Enums\JadwalStatusKerja;
use App\Models\DetailJadwal;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard')]
class ManajemenAkun extends Component
{
    use WithPagination;

    // Property Form
    public $userId = null;
    public $nama = '';
    public $email = '';
    public $role = '';
    public $status = 'Aktif';
    public $asal_sekolah = '';
    public $mentor = '';
    public $password = '';
    public $confirm_password = '';
    public $divisi = '';

    // UI States
    public bool $showModal = false;
    public bool $isEditMode = false;
    public string $search = '';

    protected function rules()
    {
        return [
            'nama' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId . ',user_id',
            'role' => ['required', Rule::enum(UserRole::class)],
            'divisi' => ['required', Rule::enum(UserDivisi::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'asal_sekolah' => 'nullable|string',
            'mentor' => 'nullable|string',
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
        'divisi.required' => 'Silakan pilih divisi pengguna.',
        'status.required' => 'Silakan pilih status akun.',
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
        $this->status = UserStatus::AKTIF->value;
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
        $this->role = $user->role->value;
        $this->divisi = $user->divisi->value;
        $this->status = $user->status->value;
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
        $this->validate();

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'nama' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
                'divisi' => $this->divisi,
                'status' => $this->status,
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
                'status' => $this->status,
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
        $this->closeJadwalModal();
    }

 public function render()
{
    $users = User::query()
        ->when($this->search, function ($query) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('asal_sekolah', 'like', '%' . $this->search . '%');
        })
        ->latest('tanggal_mulai')
        ->paginate(10);

    $mentors = User::where('role', UserRole::MENTOR->value)
        ->orderBy('nama')
        ->get();

    return view('livewire.dashboard.manajemen-akun', compact('users', 'mentors'));
}

    protected function ensureDefaultScheduleForPkl(User $user): void
    {
        if (($this->role ?: $user->role?->value) !== UserRole::PKL->value) {
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
