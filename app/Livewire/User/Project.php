<?php

namespace App\Livewire\User;

use App\Models\project as ProjectModel;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Project extends Component
{
    use WithFileUploads;

    public $nama_project = '';
    public $link_github = '';
    public $file_project = null;
    public $existing_file = null;
    public $sudahUpload = false;

    public bool $isLulus = false;

    // Fitur Kerja Kelompok
    public array $anggotaLain = [];           // user_id yang dipilih sebagai anggota tim
    public array $daftarAnggotaTerpilih = [];  // anggota yang tersimpan sebelumnya di DB

    // Modal Pilih Anggota
    public bool $showAnggotaModal = false;
    public string $searchAnggota = '';

    protected $messages = [
        'nama_project.required' => 'Nama project/laporan wajib diisi!',
        'link_github.url'       => 'Format URL GitHub tidak valid (contoh: https://github.com/username/repo)!',
        'file_project.max'      => 'Ukuran file project maksimal 20 MB!',
        'file_project.mimes'    => 'Format file project harus ZIP, RAR, PDF, atau DOCX!',
    ];

    public function mount()
    {
        $this->loadDataProject();
        $this->cekUserStatus();
    }

    private function currentUser()
    {
        return Auth::user() ?? User::where('role', UserRole::PKL)->first();
    }

    private function cekUserStatus()
    {
        $user = $this->currentUser();
        if (!$user) return;

        $userStatus = $user->status instanceof \UnitEnum ? $user->status->value : $user->status;

        if (strtolower((string) $userStatus) === 'lulus' || $userStatus === UserStatus::LULUS->value) {
            $this->isLulus = true;
            session()->flash('warning', 'Status akun Anda adalah LULUS. Anda tidak dapat mengunggah atau mengubah project lagi.');
        }
    }

    public function loadDataProject()
    {
        $user = $this->currentUser();
        if (!$user) return;

        $project = ProjectModel::where('user_id', $user->user_id)->first();

        if ($project) {
            $this->nama_project  = $project->nama_project;
            $this->link_github   = $project->link_github;
            $this->existing_file = $project->file_project;
            $this->sudahUpload   = true;

            if (!empty($project->nama_project)) {
                $anggota = ProjectModel::where('nama_project', $project->nama_project)
                    ->where('user_id', '!=', $user->user_id)
                    ->pluck('user_id')
                    ->toArray();

                $this->anggotaLain = $anggota;
                $this->daftarAnggotaTerpilih = $anggota;
            }
        }
    }

    /**
     * Daftar user PKL lain yang cocok dengan pencarian, untuk ditampilkan di modal.
     */
    public function getHasilPencarianAnggotaProperty()
    {
        $user = $this->currentUser();
        if (!$user) return collect();

        return User::where('role', UserRole::PKL)
            ->where('user_id', '!=', $user->user_id)
            ->when($this->searchAnggota, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->searchAnggota . '%')
                      ->orWhere('asal_sekolah', 'like', '%' . $this->searchAnggota . '%');
                });
            })
            ->orderBy('nama')
            ->limit(30)
            ->get(['user_id', 'nama', 'asal_sekolah', 'foto']);
    }

    /**
     * Daftar lengkap user yang sudah terpilih (untuk ditampilkan sebagai chip di luar modal).
     */
    public function getAnggotaTerpilihDetailProperty()
    {
        if (empty($this->anggotaLain)) return collect();

        return User::whereIn('user_id', $this->anggotaLain)->get(['user_id', 'nama', 'foto']);
    }

    public function openAnggotaModal()
    {
        $this->searchAnggota = '';
        $this->showAnggotaModal = true;
    }

    public function closeAnggotaModal()
    {
        $this->showAnggotaModal = false;
    }

    public function toggleAnggota($userId)
    {
        if (in_array($userId, $this->anggotaLain)) {
            $this->anggotaLain = array_values(array_diff($this->anggotaLain, [$userId]));
        } else {
            $this->anggotaLain[] = $userId;
        }
    }

    public function hapusAnggota($userId)
    {
        $this->anggotaLain = array_values(array_diff($this->anggotaLain, [$userId]));
    }

    public function simpanProject()
    {
        if ($this->isLulus) {
            session()->flash('warning', 'Gagal menyimpan! Akun Anda telah berstatus LULUS.');
            return;
        }

        $user = $this->currentUser();
        if (!$user) {
            session()->flash('warning', 'User tidak ditemukan.');
            return;
        }

        $rules = [
            'nama_project'   => 'required|string|max:255',
            'link_github'    => 'nullable|url',
            'file_project'   => 'nullable|file|mimes:zip,rar,pdf,docx|max:20480',
            'anggotaLain'    => 'nullable|array',
            'anggotaLain.*'  => 'exists:users,user_id',
        ];

        $this->validate($rules);

        try {
            $filePath = $this->existing_file;

            if ($this->file_project) {
                if ($this->existing_file && Storage::disk('public')->exists($this->existing_file)) {
                    Storage::disk('public')->delete($this->existing_file);
                }

                $namaUserSlug = Str::slug($user->nama ?? 'user');
                $filename = "project_{$namaUserSlug}_" . time() . '.' . $this->file_project->getClientOriginalExtension();
                $filePath = $this->file_project->storeAs('projects', $filename, 'public');
            }

            $dataProject = [
                'nama_project' => $this->nama_project,
                'link_github'  => $this->link_github,
                'file_project' => $filePath,
            ];

            ProjectModel::updateOrCreate(
                ['user_id' => $user->user_id],
                $dataProject
            );

            if (!empty($this->anggotaLain)) {
                foreach ($this->anggotaLain as $anggotaId) {
                    $anggotaUser = User::find($anggotaId);
                    if (!$anggotaUser) continue;

                    $statusAnggota = $anggotaUser->status instanceof \UnitEnum
                        ? $anggotaUser->status->value
                        : $anggotaUser->status;

                    if (strtolower((string) $statusAnggota) === 'lulus') {
                        continue;
                    }

                    ProjectModel::updateOrCreate(
                        ['user_id' => $anggotaId],
                        $dataProject
                    );
                }
            }

            $anggotaDihapus = array_diff($this->daftarAnggotaTerpilih, $this->anggotaLain);
            if (!empty($anggotaDihapus)) {
                foreach ($anggotaDihapus as $anggotaId) {
                    ProjectModel::where('user_id', $anggotaId)
                        ->where('nama_project', $this->nama_project)
                        ->delete();
                }
            }

            $this->file_project = null;
            $this->loadDataProject();

            $jumlahAnggota = count($this->anggotaLain);
            $pesanTambahan = $jumlahAnggota > 0
                ? " Project juga otomatis ditambahkan untuk {$jumlahAnggota} anggota tim yang dipilih."
                : '';

            session()->flash('message', 'Project Akhir berhasil disimpan!' . $pesanTambahan);

        } catch (\Exception $e) {
            session()->flash('warning', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.project')
            ->layout('layouts.user');
    }
}