<?php

namespace App\Livewire\User;

use App\Models\file as FileModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dokumen extends Component
{
    use WithFileUploads;

    public $fileProject;
    public $nama = '';
    public $uploadedFiles = [];

    // State Modal & Preview
    public $showModal = false;
    public $selectedFile = null;
    public $previewUrl = '';
    public $fileExtension = '';

    protected $rules = [
        'fileProject' => 'required|file|mimes:zip,rar,pdf,png,jpg,jpeg|max:51200',
        'nama' => 'required|string|max:255',
    ];

    protected $messages = [
        'fileProject.mimes' => 'Format file yang diperbolehkan: ZIP, RAR, PDF, PNG, JPG.',
        'fileProject.max' => 'File maksimal 50MB.',
        'fileProject.required' => 'Silakan unggah file.',
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

        $extension = $this->fileProject->getClientOriginalExtension();

        $userNameSanitized = Str::slug($user->nama ?? $user->name ?? 'user', '-');
        $fileNameSanitized = Str::slug($this->nama, '-');

        $customFileName = $userNameSanitized . '-' . $fileNameSanitized . '-' . time() . '.' . $extension;

        $path = $this->fileProject->storeAs('files', $customFileName, 'public');

        FileModel::create([
            'user_id'   => $user->user_id,
            'nama_file' => $this->nama,
            'file'      => $path,
        ]);

        $this->reset(['fileProject', 'nama']);
        $this->loadUploadedFiles();
        session()->flash('message', 'File berhasil disimpan di storage pribadi Anda.');
    }

    // Modal / Direct Preview Action
    public function openPreviewModal($fileId)
    {
        $this->selectedFile = FileModel::where('file_id', $fileId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (Storage::disk('public')->exists($this->selectedFile->file)) {
            $url = Storage::disk('public')->url($this->selectedFile->file);
            $extension = strtolower(pathinfo($this->selectedFile->file, PATHINFO_EXTENSION));

            // Jika PDF: Buka langsung di tab baru (Native Browser Viewer)
            if ($extension === 'pdf') {
                $this->js("window.open('{$url}', '_blank');");
                return;
            }

            // Selain PDF: Tampilkan Modal Preview (PNG, JPG, ZIP, RAR, dll)
            $this->previewUrl = $url;
            $this->fileExtension = $extension;
            $this->showModal = true;
        } else {
            session()->flash('error', 'File tidak ditemukan di server.');
        }
    }

    // Modal Action: Tutup modal
    public function closePreviewModal()
    {
        $this->showModal = false;
        $this->selectedFile = null;
        $this->previewUrl = '';
        $this->fileExtension = '';
    }

    public function downloadFile($fileId)
    {
        $fileRecord = FileModel::where('file_id', $fileId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (Storage::disk('public')->exists($fileRecord->file)) {
            return Storage::disk('public')->download($fileRecord->file);
        }

        session()->flash('error', 'File tidak ditemukan di server.');
    }

    public function loadUploadedFiles()
    {
        $user = Auth::user();
        $this->uploadedFiles = $user 
            ? FileModel::where('user_id', $user->user_id)->orderByDesc('file_id')->get() 
            : collect();
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