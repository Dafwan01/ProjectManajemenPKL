<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Sekolah; // <-- Wajib ditambahkan
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class ManajemenPkl extends Component
{
    use WithPagination;

    public $userId = null;
    public $nama = '';
    public $email = '';
    public $role = UserRole::PKL->value;
    public $status = UserStatus::AKTIF->value;
    
    // Perubahan: asal_sekolah dihapus, diganti sekolah_id
    public $sekolah_id = null; 
    public $mentor = '';
    public $tanggal_mulai = null;
    public $tanggal_akhir = null;
    public $skill = '';

    // Field Tambahan Profil
    public $tempat_lahir = '';
    public $tanggal_lahir = null;
    public $jenis_kelamin = '';
    public $jurusan = '';

    // State Sekolah Baru
    public bool $tambahSekolahBaru = false;
    public string $namaSekolahBaru = '';

    public bool $showEditProfileModal = false;
    public bool $isEditMode = false;
    public string $search = '';
    public bool $showJadwalModal = false;
    public bool $showProjectModal = false;
    public $selectedUserId = null;

    /**
     * Mengambil daftar sekolah untuk Dropdown
     */
    public function getDaftarSekolahProperty()
    {
        return Sekolah::orderBy('nama_sekolah')->get();
    }

    protected function rules()
    {
        $rules = [
            'nama' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId . ',user_id',
            'role' => ['required', Rule::enum(UserRole::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan,laki-laki,perempuan'],
            'jurusan' => 'nullable|string|max:255',
            'mentor' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'skill' => 'nullable|string',
        ];

        // Validasi Dinamis untuk Sekolah
        if ($this->tambahSekolahBaru) {
            $rules['namaSekolahBaru'] = 'required|string|min:3|unique:sekolahs,nama_sekolah';
        } else {
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
        'status.required' => 'Silakan pilih status akun.',
        'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
        'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        'namaSekolahBaru.required' => 'Nama sekolah baru wajib diisi.',
        'namaSekolahBaru.min' => 'Nama sekolah minimal 3 karakter.',
        'namaSekolahBaru.unique' => 'Sekolah ini sudah terdaftar, silakan pilih dari daftar dropdown.',
        'sekolah_id.exists' => 'Asal sekolah tidak valid.',
    ];

    /**
     * Trigger ketika dropdown sekolah dipilih
     */
    public function updatedSekolahId($value)
    {
        if ($value === '__tambah_baru__') {
            $this->tambahSekolahBaru = true;
            $this->sekolah_id = null;
        }
    }

    /**
     * Batal menambahkan sekolah baru
     */
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
        $this->role = UserRole::PKL->value;
        $this->status = UserStatus::AKTIF->value;
        $this->tempat_lahir = '';
        $this->tanggal_lahir = null;
        $this->jenis_kelamin = '';
        $this->jurusan = '';
        $this->sekolah_id = null;
        $this->mentor = '';
        $this->tanggal_mulai = null;
        $this->tanggal_akhir = null;
        $this->skill = '';
        
        $this->tambahSekolahBaru = false;
        $this->namaSekolahBaru = '';
        
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $currentUser = Auth::user();

        if ($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value) {
            $this->mentor = $currentUser->nama;
        }

        $this->validate();

        // Jika mode tambah sekolah baru aktif, simpan datanya ke tabel Sekolah terlebih dulu
        if ($this->tambahSekolahBaru && !empty($this->namaSekolahBaru)) {
            $sekolahBaru = Sekolah::create(['nama_sekolah' => $this->namaSekolahBaru]);
            $this->sekolah_id = $sekolahBaru->sekolah_id;
        }

        $data = [
            'nama' => $this->nama,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'jenis_kelamin' => $this->jenis_kelamin ?: null,
            'jurusan' => $this->jurusan ?: null,
            'sekolah_id' => $this->sekolah_id ?: null, // Simpan sekolah_id
            'mentor' => $this->mentor ?: null,
            'tanggal_mulai' => $this->tanggal_mulai ?: null,
            'tanggal_akhir' => $this->tanggal_akhir ?: null,
        ];

        if (Schema::hasColumn('users', 'skill')) {
            $data['skill'] = $this->skill ?: null;
        }

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            session()->flash('message', 'Akun berhasil diperbarui!');
        } else {
            $data['password'] = bcrypt('password123');

            if (empty($data['tanggal_mulai'])) {
                $data['tanggal_mulai'] = now()->format('Y-m-d');
            }

            User::create($data);
            session()->flash('message', 'Akun berhasil dibuat! Password default: password123');
        }

        $this->closeModal();
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->isEditMode = false;

        $currentUser = Auth::user();
        if ($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value) {
            $this->mentor = $currentUser->nama;
        }

        $this->showEditProfileModal = true;
    }

    public function openEditProfile($id)
    {
        $this->userId = $id;
        $user = User::findOrFail($id);

        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->role = $user->role->value ?? $user->role;
        $this->status = $user->status->value ?? $user->status;
        $this->tempat_lahir = $user->tempat_lahir;
        $this->tanggal_lahir = $this->formatDateForInput($user->tanggal_lahir);
       $this->jenis_kelamin = strtolower($user->jenis_kelamin ?? '');
        $this->jurusan = $user->jurusan;
        $this->sekolah_id = $user->sekolah_id; // Tarik sekolah_id saat edit

        $currentUser = Auth::user();
        if ($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value) {
            $this->mentor = $currentUser->nama;
        } else {
            $this->mentor = $user->mentor;
        }

        $this->tanggal_mulai = $this->formatDateForInput($user->tanggal_mulai);
        $this->tanggal_akhir = $this->formatDateForInput($user->tanggal_akhir);
        $this->skill = $user->skill ?? '';

        $this->isEditMode = true;
        $this->showEditProfileModal = true;
    }

    private function formatDateForInput($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Akun berhasil dihapus!');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->showEditProfileModal = false;
        $this->isEditMode = false;
    }

    #[On('close-edit-profile')]
    public function closeEditProfile()
    {
        $this->closeModal();
    }

    public function openJadwalModal($id)
    {
        $this->selectedUserId = $id;
        $this->showJadwalModal = true;
    }

    #[On('close-jadwal-modal')]
    public function closeJadwalModal()
    {
        $this->showJadwalModal = false;
        $this->selectedUserId = null;
    }

    public function openProjectModal($id)
    {
        $this->selectedUserId = $id;
        $this->showProjectModal = true;
    }

    #[On('close-project-modal')]
    public function closeProjectModal()
    {
        $this->showProjectModal = false;
        $this->selectedUserId = null;
    }

    public function render()
    {
        $currentUser = Auth::user();

        $mentors = User::query()
            ->where('role', UserRole::MENTOR->value)
            ->orderBy('nama', 'asc')
            ->get();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
            ->when($currentUser->role === UserRole::MENTOR || $currentUser->role?->value === UserRole::MENTOR->value, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('jurusan', 'like', '%' . $this->search . '%')
                        // Pencarian ke tabel relasi sekolah jika kolom asal_sekolah dihapus
                        ->orWhereHas('sekolah', function($subQuery) {
                            $subQuery->where('nama_sekolah', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.manajemen-pkl', compact('users', 'mentors'));
    }
}