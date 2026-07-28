<?php

namespace App\Livewire\User;

use App\Models\project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dokumen extends Component
{
    use WithFileUploads;

    public $fileProject;
    public $nama = '';
    public $message = '';
    public $uploadedFiles = [];

    protected $rules = [
        'fileProject' => 'required|file|mimes:zip,rar|max:51200',
        'nama' => 'required|string|max:255',
    ];

    protected $messages = [
        'fileProject.mimes' => 'File harus berformat ZIP atau RAR.',
        'fileProject.max' => 'File maksimal 50MB.',
        'fileProject.required' => 'Silakan unggah file ZIP/RAR.',
        'nama.required' => 'Nama file wajib diisi.',
        'nama.max' => 'Nama file maksimal 255 karakter.',
    ];

    public function updatedFileProject()
    {
        $this->validateOnly('fileProject');
    }

    public function submitDocument()
    {
        $this->validate();

        $user = Auth::user();

        if (! $user) {
            session()->flash('error', 'Silakan login terlebih dahulu sebelum mengunggah file.');
            return;
        }

        $project = new project();
        $project->user_id = $user->user_id;
        $project->nama_project = $this->nama;

        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $this->fileProject->getClientOriginalName());
        $path = $this->fileProject->storeAs('projects', $filename, 'public');
        $project->file_project = $path;

        $project->save();

        $this->reset(['fileProject', 'nama']);
        $this->loadUploadedFiles();
        session()->flash('message', 'File berhasil disimpan di storage pribadi Anda.');
    }

    public function loadUploadedFiles()
    {
        $user = Auth::user();
        $this->uploadedFiles = $user ? project::where('user_id', $user->user_id)->orderByDesc('project_id')->get() : collect();
    }

    public function mount()
    {
        $this->loadUploadedFiles();
    }

    public function render()
    {
        return view('livewire.user.dokumen')->layout('layouts.user');
    }
}
