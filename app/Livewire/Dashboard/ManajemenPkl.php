<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard')]
class ManajemenPkl extends Component
{
    use WithPagination;

    public $userId = null;
    public $nama = '';
    public $email = '';
    public $role = '';
    public $status = UserStatus::AKTIF->value;
    public $asal_sekolah = '';
    public $mentor = '';
    public $tanggal_mulai = null;
    public $skill = '';
    public $password = '';
    public $confirm_password = '';

    public bool $showEditProfileModal = false;
    public bool $isEditMode = false;
    public string $search = '';
    public bool $showJadwalModal = false;
    public $selectedUserId = null;

    protected function rules()
    {
        return [
            'nama' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId . ',user_id',
            'role' => ['required', Rule::enum(UserRole::class)],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'asal_sekolah' => 'nullable|string',
            'mentor' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
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
        $this->status = UserStatus::AKTIF->value;
        $this->asal_sekolah = '';
        $this->mentor = '';
        $this->tanggal_mulai = null;
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
        $this->validate();

        if ($this->isEditMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'nama' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
                'asal_sekolah' => $this->asal_sekolah,
                'mentor' => $this->mentor,
            ];

            if (!empty($this->password)) {
                $data['password'] = bcrypt($this->password);
            }

            if (!empty($this->tanggal_mulai)) {
                $data['tanggal_mulai'] = $this->tanggal_mulai;
            }

            if (Schema::hasColumn('users', 'skill')) {
                $data['skill'] = $this->skill;
            }

            $user->update($data);
            session()->flash('message', 'Akun berhasil diperbarui!');
        } else {
            $data = [
                'nama' => $this->nama,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
                'asal_sekolah' => $this->asal_sekolah,
                'mentor' => $this->mentor,
                'password' => bcrypt($this->password),
                'tanggal_mulai' => now(),
            ];

            if (Schema::hasColumn('users', 'skill')) {
                $data['skill'] = $this->skill;
            }

            User::create($data);
            session()->flash('message', 'Akun berhasil dibuat!');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Akun berhasil dihapus!');
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

      public function openEditProfile($id)
      {
          $this->userId = $id;
          $user = User::findOrFail($id);

          $this->nama = $user->nama;
          $this->email = $user->email;
          $this->role = $user->role;
          $this->status = $user->status;
          $this->asal_sekolah = $user->asal_sekolah;
          $this->mentor = $user->mentor;

          $this->isEditMode = true;
          $this->showEditProfileModal = true;
          $this->tanggal_mulai = optional($user->tanggal_mulai)->format('Y-m-d');
          $this->skill = $user->skill ?? '';
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

        return view('livewire.dashboard.manajemen-pkl', compact('users'));
    }
}
