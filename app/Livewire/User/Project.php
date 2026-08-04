<?php

namespace App\Livewire\User;

use App\Models\project as ProjectModel;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus; // 1. Import Enum UserStatus
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Traits\Toastable;

class Project extends Component
{
    use Toastable;

    use WithFileUploads;

    public $nama_project = '';
    public $link_github = '';
    public $file_project = null; // Menyimpan temporary uploaded file
    public $existing_file = null; // Path file lama jika sudah pernah upload
    public $sudahUpload = false;
    public $kolaborator_ids = [];
    public $availableCollaborators = [];
    public $project_status = 'Diajukan';
    public bool $isProjectOwner = true;

    // Flag status kelulusan user
    public bool $isLulus = false; // 2. State untuk mengecek apakah user sudah lulus

    protected $messages = [
        'nama_project.required' => 'Nama project/laporan wajib diisi!',
        'link_github.url'       => 'Format URL GitHub tidak valid (contoh: https://github.com/username/repo)!',
        'file_project.max'      => 'Ukuran file project maksimal 20 MB!',
        'file_project.mimes'    => 'Format file project harus ZIP, RAR, PDF, atau DOCX!',
    ];

    public function mount()
    {
        $this->loadAvailableCollaborators();
        $this->loadDataProject();
        $this->cekUserStatus(); // 3. Panggil pengecekan status lulus saat komponen dimuat
    }

    private function loadAvailableCollaborators(): void
    {
        $currentUser = $this->currentUser();

        $this->availableCollaborators = User::query()
            ->where('role', UserRole::PKL->value)
            ->when($currentUser, function ($query) use ($currentUser) {
                $query->where('user_id', '!=', $currentUser->user_id);
            })
            ->orderBy('nama')
            ->get();
    }

    private function currentUser()
    {
        return Auth::user() ?? User::where('role', UserRole::PKL)->first();
    }

    /**
     * Pengecekan apakah status user saat ini adalah LULUS
     */
    private function cekUserStatus()
    {
        $user = $this->currentUser();
        if (!$user) return;

        $userStatus = $user->status instanceof \UnitEnum ? $user->status->value : $user->status;

        // Cek jika status user adalah 'lulus' (baik via Enum maupun String)
        if (strtolower((string) $userStatus) === 'lulus' || $userStatus === UserStatus::LULUS->value) {
            $this->isLulus = true;
            $this->toastWarning( 'Status akun Anda adalah LULUS. Anda tidak dapat mengunggah atau mengubah project lagi.');
        }
    }

    public function loadDataProject()
    {
        $user = $this->currentUser();
        if (!$user) return;

        $project = ProjectModel::where(function ($query) use ($user) {
                $query->where('user_id', $user->user_id)
                      ->orWhereJsonContains('kolaborator_ids', $user->user_id);
            })
            ->first();

        if ($project) {
            $this->nama_project      = $project->nama_project;
            $this->link_github       = $project->link_github;
            $this->existing_file     = $project->file_project;
            $this->sudahUpload       = true;
            $this->kolaborator_ids   = $project->kolaborator_ids ?? [];
            $this->project_status    = $project->project_status ?? 'Diajukan';
            $this->isProjectOwner    = $project->user_id === $user->user_id;
        }
    }

    public function simpanProject()
    {
        // 4. Guard Clause: Blokir jika user sudah LULUS
        if ($this->isLulus) {
            $this->toastWarning( 'Gagal menyimpan! Akun Anda telah berstatus LULUS.');
            return;
        }

        $user = $this->currentUser();
        if (!$user) {
            $this->toastWarning( 'User tidak ditemukan.');
            return;
        }

        if (!$this->isProjectOwner && $this->sudahUpload) {
            $this->toastWarning('Anda bukan pemilik project ini, sehingga tidak dapat mengubah data.');
            return;
        }

        $rules = [
            'nama_project' => 'required|string|max:255',
            'link_github'  => 'nullable|url',
            'file_project' => 'nullable|file|mimes:zip,rar,pdf,docx|max:20480',
            'kolaborator_ids' => 'nullable|array',
            'kolaborator_ids.*' => 'integer|exists:users,user_id',
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

            $existingProject = ProjectModel::where('user_id', $user->user_id)->first();
            $status = $existingProject ? ($existingProject->project_status ?: 'Diperbarui') : 'Diajukan';

            $project = ProjectModel::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nama_project'    => $this->nama_project,
                    'link_github'     => $this->link_github,
                    'file_project'    => $filePath,
                    'project_status'  => $status,
                    'kolaborator_ids' => $this->kolaborator_ids ?: null,
                ]
            );

            $this->file_project = null;
            $this->project_status = $project->project_status;
            $this->loadDataProject();

            $this->toastSuccess( 'Project Akhir berhasil disimpan!');

        } catch (\Exception $e) {
            $this->toastWarning( 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.project')
            ->layout('layouts.user');
    }
}