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

class Project extends Component
{
    use WithFileUploads;

    public $nama_project = '';
    public $link_github = '';
    public $file_project = null; // Menyimpan temporary uploaded file
    public $existing_file = null; // Path file lama jika sudah pernah upload
    public $sudahUpload = false;

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
        $this->loadDataProject();
        $this->cekUserStatus(); // 3. Panggil pengecekan status lulus saat komponen dimuat
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
        }
    }

    public function simpanProject()
    {
        // 4. Guard Clause: Blokir jika user sudah LULUS
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
            'nama_project' => 'required|string|max:255',
            'link_github'  => 'nullable|url',
            'file_project' => 'nullable|file|mimes:zip,rar,pdf,docx|max:20480',
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

            ProjectModel::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nama_project'  => $this->nama_project,
                    'link_github'   => $this->link_github,
                    'file_project'  => $filePath,
                ]
            );

            $this->file_project = null;
            $this->loadDataProject();

            session()->flash('message', 'Project Akhir berhasil disimpan!');

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