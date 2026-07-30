<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Carbon;
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
    public $role = UserRole::PKL->value; // Default role PKL
    public $status = UserStatus::AKTIF->value;
    public $asal_sekolah = '';
    public $mentor = '';
    public $tanggal_mulai = null;
    public $tanggal_akhir = null; // Penambahan tanggal_akhir
    public $skill = '';
    public $password = '';
    public $confirm_password = '';

    // Field Tambahan Profil
    public $tempat_lahir = '';
    public $tanggal_lahir = null;
    public $jenis_kelamin = '';
    public $jurusan = '';

    public bool $showEditProfileModal = false;
    public bool $isEditMode = false;
    public string $search = '';
    public bool $showJadwalModal = false;
    public bool $showProjectModal = false;
    public $selectedUserId = null;

    protected function rules()
    {
        return [
            'nama' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId . ',user_id',
            'role' => ['required', Rule::enum(UserRole::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => ['nullable', 'string', 'in:laki-laki,perempuan'],
            'jurusan' => 'nullable|string|max:255',
            'asal_sekolah' => 'nullable|string|max:255',
            'mentor' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'skill' => 'nullable|string',
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
        'status.required' => 'Silakan pilih status akun.',
        'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
        'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal harus 8 karakter.',
        'password.same' => 'Konfirmasi password tidak cocok.',
    ];

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
        $this->asal_sekolah = '';
        $this->mentor = '';
        $this->tanggal_mulai = null;
        $this->tanggal_akhir = null;
        $this->skill = '';
        $this->password = '';
        $this->confirm_password = '';
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $validated = $this->validate();

        $data = [
            'nama' => $this->nama,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'jenis_kelamin' => $this->jenis_kelamin ?: null,
            'jurusan' => $this->jurusan ?: null,
            'asal_sekolah' => $this->asal_sekolah ?: null,
            'mentor' => $this->mentor ?: null,
            'tanggal_mulai' => $this->tanggal_mulai ?: null,
            'tanggal_Akhir' => $this->tanggal_akhir ?: null, // Menyesuaikan nama kolom A besar di Model
        ];

        if (Schema::hasColumn('users', 'skill')) {
            $data['skill'] = $this->skill ?: null;
        }

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);

            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            }

            $user->update($data);
            session()->flash('message', 'Akun berhasil diperbarui!');
        } else {
            $data['password'] = bcrypt($this->password);
            if (empty($data['tanggal_mulai'])) {
                $data['tanggal_mulai'] = now()->format('Y-m-d');
            }

            User::create($data);
            session()->flash('message', 'Akun berhasil dibuat!');
        }

        $this->closeModal();
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->isEditMode = false;
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
        $this->jenis_kelamin = $user->jenis_kelamin;
        $this->jurusan = $user->jurusan;
        $this->asal_sekolah = $user->asal_sekolah;
        $this->mentor = $user->mentor;
        $this->tanggal_mulai = $this->formatDateForInput($user->tanggal_mulai);
        $this->tanggal_akhir = $this->formatDateForInput($user->tanggal_Akhir); // Ambil dari tanggal_Akhir Model
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
        $users = User::query()
            ->where('role', UserRole::PKL->value)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('asal_sekolah', 'like', '%' . $this->search . '%')
                        ->orWhere('jurusan', 'like', '%' . $this->search . '%');
                });
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.manajemen-pkl', compact('users'));
    }
}