<?php

namespace App\Livewire\User;

use App\Models\project as ProjectModel;
use App\Models\User;
use App\Enums\UserRole;
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

    protected $messages = [
        'nama_project.required' => 'Nama project/laporan wajib diisi!',
        'link_github.url'       => 'Format URL GitHub tidak valid (contoh: https://github.com/username/repo)!',
        'file_project.required' => 'File project/laporan wajib diunggah!',
        'file_project.max'      => 'Ukuran file project maksimal 20 MB!',
        'file_project.mimes'    => 'Format file project harus ZIP, RAR, PDF, atau DOCX!',
    ];

    public function mount()
    {
        $this->loadDataProject();
    }

    private function currentUser()
    {
        return Auth::user() ?? User::where('role', UserRole::PKL)->first();
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
        $user = $this->currentUser();
        if (!$user) {
            session()->flash('warning', 'User tidak ditemukan.');
            return;
        }

        // Rule Validasi
        $rules = [
            'nama_project' => 'required|string|max:255',
            'link_github'  => 'nullable|url',
        ];

        // Jika belum pernah upload file, maka file wajib diisi
        if (!$this->existing_file) {
            $rules['file_project'] = 'required|file|mimes:zip,rar,pdf,docx|max:20480';
        } else {
            $rules['file_project'] = 'nullable|file|mimes:zip,rar,pdf,docx|max:20480';
        }

        $this->validate($rules);

        try {
            $filePath = $this->existing_file;

            // Jika mengunggah file baru
            if ($this->file_project) {
                // Hapus file lama dari storage jika ada
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

            $this->file_project = null; // Reset input file temporary
            $this->loadDataProject(); // Reload state terbaru

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