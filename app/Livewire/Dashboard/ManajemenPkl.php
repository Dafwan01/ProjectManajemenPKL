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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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

    // Field Autentikasi
    public $password = '';
    public $confirm_password = '';

    // Relasi Sekolah, Mentor, Periode & Skill
    public $sekolah_id = null;
    public $searchSekolah = '';
    public $mentor = '';
    public $tanggal_mulai = null;
    public $tanggal_akhir = null;
    public $skill = '';

    // Bidang & Divisi (bidang_id bersifat transient)
    public $bidang_id = null;
    public $divisi_id = null;
    public $searchBidang = '';
    public $searchDivisi = '';

    // Profil Pelengkap
    public $tempat_lahir = '';
    public $tanggal_lahir = null;
    public $jenis_kelamin = '';
    public $jurusan = '';

    // State Input Sekolah Baru
    public bool $tambahSekolahBaru = false;
    public string $namaSekolahBaru = '';

    // State Input Bidang Baru
    public bool $tambahBidangBaru = false;
    public string $namaBidangBaru = '';

    // State Input Divisi Baru
    public bool $tambahDivisiBaru = false;
    public string $namaDivisiBaru = '';

    // State Konfirmasi Hapus Sekolah
    public bool $showDeleteSekolahConfirm = false;
    public $sekolahIdToDelete = null;
    public string $namaSekolahToDelete = '';
    public array $sekolahUsersToDelete = [];

    // State Konfirmasi Hapus Bidang
    public bool $showDeleteBidangConfirm = false;
    public $bidangIdToDelete = null;
    public string $namaBidangToDelete = '';
    public array $bidangDivisiToDelete = [];

    // State Konfirmasi Hapus Divisi
    public bool $showDeleteDivisiConfirm = false;
    public $divisiIdToDelete = null;
    public string $namaDivisiToDelete = '';
    public array $divisiUsersToDelete = [];

    // Modal Control & Filter
    public bool $showEditProfileModal = false;
    public bool $isEditMode = false;
    public string $search = '';

    // Modal Ekstra (Jadwal & Project)
    public bool $showJadwalModal = false;
    public bool $showProjectModal = false;
    public $selectedUserId = null;

    /**
     * Memastikan locale Carbon diatur ke bahasa Indonesia untuk semua pemrosesan tanggal.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
    }

    /**
     * Memeriksa apakah pengguna yang sedang login ber-role Mentor.
     */
    private function isMentorUser(): bool
    {
        $currentUser = Auth::user();

        return $currentUser->role === UserRole::MENTOR
            || $currentUser->role?->value === UserRole::MENTOR->value;
    }

    /* =========================================================
     |  SEKOLAH
     * ========================================================= */

    public function getDaftarSekolahProperty()
    {
        return Sekolah::query()
            ->when($this->searchSekolah, function ($query) {
                $query->where('nama_sekolah', 'like', '%' . $this->searchSekolah . '%');
            })
            ->orderBy('nama_sekolah')
            ->get();
    }

    public function getSekolahTerpilihProperty()
    {
        return $this->sekolah_id ? Sekolah::find($this->sekolah_id) : null;
    }

    public function pilihSekolah($id, $nama)
    {
        $this->sekolah_id = $id;
        $this->searchSekolah = '';
        $this->tambahSekolahBaru = false;
        $this->namaSekolahBaru = '';
    }

    public function batalTambahSekolah()
    {
        $this->tambahSekolahBaru = false;
        $this->namaSekolahBaru = '';
        $this->sekolah_id = null;
    }

    public function confirmHapusSekolah($id, $nama)
    {
        $this->sekolahIdToDelete = $id;
        $this->namaSekolahToDelete = $nama;

        $this->sekolahUsersToDelete = User::where('sekolah_id', $id)
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();

        $this->showDeleteSekolahConfirm = true;
    }

    public function batalHapusSekolah()
    {
        $this->showDeleteSekolahConfirm = false;
        $this->sekolahIdToDelete = null;
        $this->namaSekolahToDelete = '';
        $this->sekolahUsersToDelete = [];
    }

    public function hapusSekolah()
    {
        if ($this->sekolahIdToDelete) {
            try {
                Sekolah::where('sekolah_id', $this->sekolahIdToDelete)->delete();

                if ($this->sekolah_id == $this->sekolahIdToDelete) {
                    $this->sekolah_id = null;
                    $this->searchSekolah = '';
                }

                session()->flash('message', 'Data sekolah berhasil dihapus!');
            } catch (\Illuminate\Database\QueryException $e) {
                session()->flash('error', 'Sekolah tidak dapat dihapus karena masih digunakan oleh data akun lain.');
            }
        }

        $this->batalHapusSekolah();
    }

    /* =========================================================
     |  BIDANG
     * ========================================================= */

    public function getDaftarBidangProperty()
    {
        return Bidang::query()
            ->when($this->searchBidang, function ($query) {
                $query->where('nama_bidang', 'like', '%' . $this->searchBidang . '%');
            })
            ->orderBy('nama_bidang')
            ->get();
    }

    public function getBidangTerpilihProperty()
    {
        return $this->bidang_id ? Bidang::find($this->bidang_id) : null;
    }

    public function pilihBidang($id, $nama)
    {
        if ($this->isMentorUser()) {
            return;
        }

        $this->bidang_id = $id;
        $this->searchBidang = '';
        $this->tambahBidangBaru = false;
        $this->namaBidangBaru = '';

        // Bidang berubah -> divisi yang sudah dipilih jadi tidak valid lagi
        $this->divisi_id = null;
        $this->searchDivisi = '';
        $this->tambahDivisiBaru = false;
        $this->namaDivisiBaru = '';
    }

    public function batalTambahBidang()
    {
        $this->tambahBidangBaru = false;
        $this->namaBidangBaru = '';
        $this->bidang_id = null;
    }

    public function confirmHapusBidang($id, $nama)
    {
        $this->bidangIdToDelete = $id;
        $this->namaBidangToDelete = $nama;

        // Bidang tidak boleh dihapus jika masih memiliki divisi di dalamnya
        $this->bidangDivisiToDelete = Divisi::where('bidang_id', $id)
            ->orderBy('nama_divisi')
            ->pluck('nama_divisi')
            ->toArray();

        $this->showDeleteBidangConfirm = true;
    }

    public function batalHapusBidang()
    {
        $this->showDeleteBidangConfirm = false;
        $this->bidangIdToDelete = null;
        $this->namaBidangToDelete = '';
        $this->bidangDivisiToDelete = [];
    }

    public function hapusBidang()
    {
        if ($this->bidangIdToDelete) {
            try {
                Bidang::where('bidang_id', $this->bidangIdToDelete)->delete();

                if ($this->bidang_id == $this->bidangIdToDelete) {
                    $this->bidang_id = null;
                    $this->divisi_id = null;
                    $this->searchBidang = '';
                }

                session()->flash('message', 'Data bidang berhasil dihapus!');
            } catch (\Illuminate\Database\QueryException $e) {
                session()->flash('error', 'Bidang tidak dapat dihapus karena masih memiliki divisi di dalamnya.');
            }
        }

        $this->batalHapusBidang();
    }

    /* =========================================================
     |  DIVISI
     * ========================================================= */

    public function getDaftarDivisiProperty()
    {
        if (empty($this->bidang_id)) {
            return collect();
        }

        return Divisi::where('bidang_id', $this->bidang_id)
            ->when($this->searchDivisi, function ($query) {
                $query->where('nama_divisi', 'like', '%' . $this->searchDivisi . '%');
            })
            ->orderBy('nama_divisi')
            ->get();
    }

    public function getDivisiTerpilihProperty()
    {
        return $this->divisi_id ? Divisi::find($this->divisi_id) : null;
    }

    public function pilihDivisi($id, $nama)
    {
        if ($this->isMentorUser()) {
            return;
        }

        $this->divisi_id = $id;
        $this->searchDivisi = '';
        $this->tambahDivisiBaru = false;
        $this->namaDivisiBaru = '';
    }

    public function batalTambahDivisi()
    {
        $this->tambahDivisiBaru = false;
        $this->namaDivisiBaru = '';
        $this->divisi_id = null;
    }

    public function confirmHapusDivisi($id, $nama)
    {
        $this->divisiIdToDelete = $id;
        $this->namaDivisiToDelete = $nama;

        $this->divisiUsersToDelete = User::where('divisi_id', $id)
            ->orderBy('nama')
            ->pluck('nama')
            ->toArray();

        $this->showDeleteDivisiConfirm = true;
    }

    public function batalHapusDivisi()
    {
        $this->showDeleteDivisiConfirm = false;
        $this->divisiIdToDelete = null;
        $this->namaDivisiToDelete = '';
        $this->divisiUsersToDelete = [];
    }

    public function hapusDivisi()
    {
        if ($this->divisiIdToDelete) {
            try {
                Divisi::where('divisi_id', $this->divisiIdToDelete)->delete();

                if ($this->divisi_id == $this->divisiIdToDelete) {
                    $this->divisi_id = null;
                    $this->searchDivisi = '';
                }

                session()->flash('message', 'Data divisi berhasil dihapus!');
            } catch (\Illuminate\Database\QueryException $e) {
                session()->flash('error', 'Divisi tidak dapat dihapus karena masih digunakan oleh data akun lain.');
            }
        }

        $this->batalHapusDivisi();
    }

    /* =========================================================
     |  ROLE & STATUS
     * ========================================================= */

    public function getAvailableRolesProperty()
    {
        if ($this->isMentorUser()) {
            return collect([UserRole::PKL]);
        }

        return collect(UserRole::cases());
    }

    public function getDaftarStatusProperty()
    {
        return collect(UserStatus::cases());
    }

    protected function rules()
    {
        $passwordRule = Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols();

        $rules = [
            'nama'          => 'required|min:3',
            'email'         => 'required|email|unique:users,email,' . $this->userId . ',user_id',
            'role'          => ['required', Rule::enum(UserRole::class)],
            'status'        => ['required', Rule::enum(UserStatus::class)],
            'tempat_lahir'  => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan,laki-laki,perempuan'],
            'jurusan'       => 'nullable|string|max:255',
            'mentor'        => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'skill'         => 'nullable|string',
            'password'      => $this->isEditMode
                ? ['nullable', $passwordRule, 'same:confirm_password']
                : ['required', $passwordRule, 'same:confirm_password'],
        ];

        // Dynamic Validation Rule untuk Asal Sekolah
        if ($this->tambahSekolahBaru) {
            $rules['namaSekolahBaru'] = 'required|string|min:3|unique:sekolahs,nama_sekolah';
            $rules['sekolah_id']      = 'nullable';
        } else {
            $rules['sekolah_id']      = 'required|exists:sekolahs,sekolah_id';
            $rules['namaSekolahBaru'] = 'nullable';
        }

        // Dynamic Validation Rule untuk Bidang
        if ($this->tambahBidangBaru) {
            $rules['namaBidangBaru'] = 'required|string|min:3|unique:bidangs,nama_bidang';
            $rules['bidang_id']      = 'nullable';
        } else {
            $rules['bidang_id']      = 'required|exists:bidangs,bidang_id';
            $rules['namaBidangBaru'] = 'nullable';
        }

        // Dynamic Validation Rule untuk Divisi
        if ($this->tambahDivisiBaru) {
            $rules['namaDivisiBaru'] = 'required|string|min:3|unique:divisis,nama_divisi';
            $rules['divisi_id']      = 'nullable';
        } else {
            $rules['divisi_id']      = 'required|exists:divisis,divisi_id';
            $rules['namaDivisiBaru'] = 'nullable';
        }

        return $rules;
    }

    protected $messages = [
        'nama.required'                => 'Nama lengkap wajib diisi.',
        'nama.min'                     => 'Nama minimal 3 karakter.',
        'email.required'               => 'Alamat email wajib diisi.',
        'email.email'                  => 'Format email tidak valid.',
        'email.unique'                 => 'Email ini sudah terdaftar.',
        'role.required'                => 'Silakan pilih role pengguna.',
        'status.required'              => 'Silakan pilih status akun.',
        'jenis_kelamin.in'             => 'Pilihan jenis kelamin tidak valid.',
        'mentor.required'              => 'Mentor wajib dipilih atau diisi.',
        'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',

        'namaSekolahBaru.required'     => 'Nama sekolah baru wajib diisi.',
        'namaSekolahBaru.min'          => 'Nama sekolah minimal 3 karakter.',
        'namaSekolahBaru.unique'       => 'Sekolah ini sudah terdaftar di sistem.',
        'sekolah_id.required'          => 'Asal sekolah wajib dipilih.',
        'sekolah_id.exists'            => 'Pilihan sekolah tidak valid.',

        'namaBidangBaru.required'      => 'Nama bidang baru wajib diisi.',
        'namaBidangBaru.min'           => 'Nama bidang minimal 3 karakter.',
        'namaBidangBaru.unique'        => 'Bidang ini sudah terdaftar di sistem.',
        'bidang_id.required'           => 'Silakan pilih bidang.',
        'bidang_id.exists'             => 'Bidang tidak valid.',

        'namaDivisiBaru.required'      => 'Nama divisi baru wajib diisi.',
        'namaDivisiBaru.min'           => 'Nama divisi minimal 3 karakter.',
        'namaDivisiBaru.unique'        => 'Divisi ini sudah terdaftar di sistem.',
        'divisi_id.required'           => 'Silakan pilih divisi.',
        'divisi_id.exists'             => 'Divisi tidak valid.',

        'password.required'            => 'Password wajib diisi.',
        'password.same'                => 'Konfirmasi password tidak cocok.',
    ];

    public function resetFields()
    {
        $this->userId = null;
        $this->nama = '';
        $this->email = '';
        $this->role = UserRole::PKL->value;
        $this->status = UserStatus::AKTIF->value;
        $this->password = '';
        $this->confirm_password = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = null;
        $this->jenis_kelamin = '';
        $this->jurusan = '';
        $this->sekolah_id = null;
        $this->searchSekolah = '';
        $this->bidang_id = null;
        $this->divisi_id = null;
        $this->searchBidang = '';
        $this->searchDivisi = '';
        $this->mentor = '';
        $this->tanggal_mulai = null;
        $this->tanggal_akhir = null;
        $this->skill = '';

        $this->tambahSekolahBaru = false;
        $this->namaSekolahBaru = '';
        $this->tambahBidangBaru = false;
        $this->namaBidangBaru = '';
        $this->tambahDivisiBaru = false;
        $this->namaDivisiBaru = '';

        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    private function lockDivisiToMentor(User $currentUser): void
    {
        $this->divisi_id = $currentUser->divisi_id;

        $divisiMentor = $this->divisi_id
            ? Divisi::find($this->divisi_id)
            : null;

        $this->bidang_id = $divisiMentor?->bidang_id;
    }

    public function save()
    {
        $currentUser = Auth::user();

        if ($this->isMentorUser()) {
            $this->mentor = $currentUser->nama;
            $this->lockDivisiToMentor($currentUser);
        }

        $this->validate();

        if ($this->tambahSekolahBaru && !empty($this->namaSekolahBaru)) {
            $sekolahBaru = Sekolah::create(['nama_sekolah' => trim($this->namaSekolahBaru)]);
            $this->sekolah_id = $sekolahBaru->sekolah_id;
        }

        if ($this->tambahBidangBaru && !empty($this->namaBidangBaru)) {
            $bidangBaru = Bidang::create(['nama_bidang' => trim($this->namaBidangBaru)]);
            $this->bidang_id = $bidangBaru->bidang_id;
        }

        if ($this->tambahDivisiBaru && !empty($this->namaDivisiBaru)) {
            $divisiBaru = Divisi::create([
                'nama_divisi' => trim($this->namaDivisiBaru),
                'bidang_id'   => $this->bidang_id,
            ]);
            $this->divisi_id = $divisiBaru->divisi_id;
        }

        $data = [
            'nama'          => $this->nama,
            'email'         => $this->email,
            'role'          => $this->role,
            'status'        => $this->status,
            'tempat_lahir'  => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'jenis_kelamin' => $this->jenis_kelamin ?: null,
            'jurusan'       => $this->jurusan ?: null,
            'sekolah_id'    => $this->sekolah_id ?: null,
            'divisi_id'     => $this->divisi_id ?: null,
            'mentor'        => $this->mentor ?: null,
            'tanggal_mulai' => $this->tanggal_mulai ?: null,
            'tanggal_akhir' => $this->tanggal_akhir ?: null,
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
            $this->ensureDefaultScheduleForPkl($user);
            session()->flash('message', 'Akun pengguna berhasil diperbarui!');
        } else {
            $data['password'] = bcrypt($this->password);

            if (empty($data['tanggal_mulai'])) {
                $data['tanggal_mulai'] = now()->format('Y-m-d');
            }

            $user = User::create($data);
            $this->ensureDefaultScheduleForPkl($user);
            session()->flash('message', 'Akun pengguna berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function openCreateModal()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->selectedUserId = null;

        $currentUser = Auth::user();
        if ($this->isMentorUser()) {
            $this->mentor = $currentUser->nama;
            $this->lockDivisiToMentor($currentUser);
        }

        $this->showEditProfileModal = true;
    }

    public function openEditProfile($id)
    {
        $this->resetFields();
        $this->userId = $id;
        $this->selectedUserId = $id;
        $user = User::findOrFail($id);

        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->role = $user->role->value ?? $user->role;
        $this->status = $user->status->value ?? $user->status;
        $this->tempat_lahir = $user->tempat_lahir;
        $this->tanggal_lahir = $this->formatDateForInput($user->tanggal_lahir);
        $this->jenis_kelamin = strtolower($user->jenis_kelamin ?? '');
        $this->jurusan = $user->jurusan;
        $this->sekolah_id = $user->sekolah_id;
        $this->searchSekolah = '';

        $currentUser = Auth::user();
        if ($this->isMentorUser()) {
            $this->mentor = $currentUser->nama;
            $this->lockDivisiToMentor($currentUser);
        } else {
            $this->mentor = $user->mentor;
            $this->divisi_id = $user->divisi_id;

            $divisiUser = $this->divisi_id
                ? Divisi::find($this->divisi_id)
                : null;

            $this->bidang_id = $divisiUser?->bidang_id;
        }

        $this->tanggal_mulai = $this->formatDateForInput($user->tanggal_mulai);
        $this->tanggal_akhir = $this->formatDateForInput($user->tanggal_akhir);
        $this->skill = $user->skill ?? '';

        $this->isEditMode = true;
        $this->showEditProfileModal = true;
    }

    /**
     * Memformat tanggal khusus untuk nilai atribut HTML <input type="date">.
     * Harus dalam format standar ISO 'Y-m-d' agar dapat dibaca oleh browser.
     */
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
        $this->selectedUserId = null;
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

    /**
     * Membuat jadwal kerja default (Senin–Jumat) untuk pengguna PKL.
     */
    protected function ensureDefaultScheduleForPkl(User $user): void
    {
        $userRoleValue = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;

        if ($userRoleValue !== UserRole::PKL->value) {
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
                    'jam_masuk'    => $jamMasuk,
                    'jam_keluar'   => $jamKeluar,
                    'status_kerja' => JadwalStatusKerja::WFO->value,
                ]);

                DetailJadwal::updateOrCreate(
                    [
                        'user_id' => $user->user_id,
                        'hari'    => $hari,
                    ],
                    [
                        'jadwal_id' => $jadwal->jadwal_id,
                    ]
                );
            }
        });
    }

    public function render()
    {
        $currentUser = Auth::user();

        $mentors = User::query()
            ->where('role', UserRole::MENTOR->value)
            ->where('status', UserStatus::AKTIF->value)
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
                        ->orWhereHas('sekolah', function ($subQuery) {
                            $subQuery->where('nama_sekolah', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderByRaw("CASE 
                WHEN status = 'aktif' THEN 1 
                WHEN status = 'lulus' THEN 2 
                ELSE 3 
            END ASC")
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.manajemen-pkl', compact('users', 'mentors'));
    }
}