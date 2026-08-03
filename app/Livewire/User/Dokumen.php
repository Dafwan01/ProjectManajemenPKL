<?php

namespace App\Livewire\User;

use App\Models\file as FileModel;
use App\Enums\UserStatus;
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
    public $filterName = '';
    public $filterExtension = '';
    public $filterUploadAt = '';

    // Flag status kelulusan user
    public bool $isLulus = false;

    // State Modal & Preview
    public $showModal = false;
    public $selectedFile = null;
    public $previewUrl = '';
    public $fileExtension = '';
    public $confirmDeleteId = null;

    protected $rules = [
        'fileProject' => 'required|file|mimes:zip,rar,pdf,png,jpg,jpeg|max:51200',
        'nama'        => 'required|string|max:255',
    ];

    protected $messages = [
        'fileProject.mimes'    => 'Format file yang diperbolehkan: ZIP, RAR, PDF, PNG, JPG.',
        'fileProject.max'      => 'File maksimal 50MB.',
        'fileProject.required' => 'Silakan unggah file.',
        'nama.required'        => 'Nama file wajib diisi.',
        'nama.max'             => 'Nama file maksimal 255 karakter.',
    ];

    public function mount()
    {
        $this->loadUploadedFiles();
        $this->cekUserStatus();
    }

    /**
     * Pengecekan apakah status user saat ini adalah LULUS
     */
    private function cekUserStatus()
    {
        $user = Auth::user();
        if (!$user) return;

        $userStatus = $user->status instanceof \UnitEnum ? $user->status->value : $user->status;

        if (strtolower((string) $userStatus) === 'lulus' || $userStatus === UserStatus::LULUS->value) {
            $this->isLulus = true;
            session()->flash('warning', 'Status akun Anda adalah LULUS. Anda tidak dapat mengunggah dokumen baru lagi.');
        }
    }

    public function updatedFileProject()
    {
        $this->validateOnly('fileProject');
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'filter')) {
            $this->loadUploadedFiles();
            return;
        }

        if ($propertyName === 'fileProject') {
            return;
        }

        $this->validateOnly($propertyName);
    }

    public function submitDocument()
    {
        if ($this->isLulus) {
            session()->flash('warning', 'Gagal mengunggah! Akun Anda telah berstatus LULUS.');
            return;
        }

        $this->validate();

        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'Silakan login terlebih dahulu sebelum mengunggah file.');
            return;
        }

        $extension = $this->fileProject->getClientOriginalExtension();

        $userNameSanitized = Str::slug($user->nama ?? $user->name ?? 'user', '-');
        $fileNameSanitized = Str::slug($this->nama, '-');

        $customFileName = $userNameSanitized . '-' . $fileNameSanitized . '-' . time() . '.' . $extension;

        $path = $this->fileProject->storeAs('files', $customFileName, 'public');

        FileModel::create([
            'user_id'   => $user->user_id ?? $user->id,
            'nama_file' => $this->nama,
            'file'      => $path,
        ]);

        $this->reset(['fileProject', 'nama']);
        $this->loadUploadedFiles();
        session()->flash('message', 'File berhasil disimpan di storage pribadi Anda.');
    }

    /**
     * Membuka Modal Preview untuk Gambar & PDF
     */
    public function openPreviewModal($fileId)
    {
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id ?? Auth::id();

        $this->selectedFile = FileModel::where('file_id', $fileId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (Storage::disk('public')->exists($this->selectedFile->file)) {
            $extension = strtolower(pathinfo($this->selectedFile->file, PATHINFO_EXTENSION));

            // Use a relative storage path to preserve the current host and port
            $this->previewUrl = '/storage/' . ltrim($this->selectedFile->file, '/');
            $this->fileExtension = $extension;
            $this->showModal = true;
        } else {
            session()->flash('error', 'File tidak ditemukan di server.');
        }
    }

    /**
     * Tutup Modal Preview
     */
    public function closePreviewModal()
    {
        $this->showModal = false;
        $this->selectedFile = null;
        $this->previewUrl = '';
        $this->fileExtension = '';
    }

    public function downloadFile($fileId)
    {
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id ?? Auth::id();

        $fileRecord = FileModel::where('file_id', $fileId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (Storage::disk('public')->exists($fileRecord->file)) {
            return Storage::disk('public')->download($fileRecord->file);
        }

        session()->flash('error', 'File tidak ditemukan di server.');
    }

    public function confirmDelete($fileId)
    {
        $this->confirmDeleteId = $fileId;
    }

    public function cancelDelete()
    {
        $this->confirmDeleteId = null;
    }

    public function deleteFile()
    {
        if (!$this->confirmDeleteId) {
            return;
        }

        $user = Auth::user();
        $userId = $user->user_id ?? $user->id ?? Auth::id();

        $fileRecord = FileModel::where('file_id', $this->confirmDeleteId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (Storage::disk('public')->exists($fileRecord->file)) {
            Storage::disk('public')->delete($fileRecord->file);
        }

        $fileRecord->delete();
        $this->confirmDeleteId = null;
        $this->loadUploadedFiles();

        if ($this->selectedFile && $this->selectedFile->file_id === $fileRecord->file_id) {
            $this->closePreviewModal();
        }

        session()->flash('message', 'File berhasil dihapus.');
    }

    public function loadUploadedFiles()
    {
        $user = Auth::user();
        $userId = $user ? ($user->user_id ?? $user->id) : null;

        if (! $userId) {
            $this->uploadedFiles = collect();
            return;
        }

        $query = FileModel::where('user_id', $userId);

        if ($this->filterName) {
            $query->where('nama_file', 'like', '%' . $this->filterName . '%');
        }

        if ($this->filterExtension) {
            $query->where('file', 'like', '%.' . strtolower($this->filterExtension));
        }

        if ($this->filterUploadAt && strtotime($this->filterUploadAt) !== false) {
            $query->whereDate('created_at', date('Y-m-d', strtotime($this->filterUploadAt)));
        }

        $this->uploadedFiles = $query->orderByDesc('created_at')->get();
    }

    public function render()
    {
        return view('livewire.user.dokumen')->layout('layouts.user');
    }
}