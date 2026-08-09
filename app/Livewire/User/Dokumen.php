<?php

namespace App\Livewire\User;

use App\Models\file as FileModel;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Dokumen extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $fileProject;
    public $nama = '';
    public $filterName = '';
    public $filterExtension = '';
    public $filterUploadAt = '';

    public bool $isLulus = false;

    public $showModal = false;
    public $selectedFile = null;
    public $previewUrl = '';
    public $fileExtension = '';
    public $confirmDeleteId = null;

    protected $messages = [
        'fileProject.mimes'    => 'Format berkas yang diperbolehkan: ZIP, RAR, PDF, PNG, JPG, JPEG.',
        'fileProject.max'      => 'Ukuran berkas maksimal 50MB.',
        'fileProject.required' => 'Silakan pilih berkas yang ingin diunggah.',
        'nama.required'        => 'Nama berkas wajib diisi.',
        'nama.max'             => 'Nama berkas maksimal 255 karakter.',
        'nama.unique'          => 'Nama berkas ini sudah pernah Anda gunakan. Silakan gunakan nama lain.',
    ];

    public function mount()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications()
                ->whereIn('data->title', [
                    'Berkas Baru Diunggah', 
                    'Surat Penerimaan Magang', 
                    'Nilai', 
                    'Nilai Baru', 
                    'Pembaruan Nilai',
                    'Sertifikat'
                ])
                ->get()
                ->each(fn ($notification) => $notification->markAsRead());
        }

        $this->cekUserStatus();
    }

    private function cekUserStatus()
    {
        $user = Auth::user();
        if (!$user) return;

        $userStatus = $user->status instanceof \UnitEnum ? $user->status->value : $user->status;

        if (strtolower((string) $userStatus) === 'lulus' || $userStatus === UserStatus::LULUS->value) {
            $this->isLulus = true;
            session()->flash('warning', 'Status akun Anda adalah LULUS. Anda tidak dapat mengunggah berkas baru lagi.');
        }
    }

    /**
     * Rules dibuat dinamis (method) supaya bisa akses $userId untuk unique-per-user.
     */
    protected function rules(): array
    {
        $user = Auth::user();
        $userId = $user ? ($user->user_id ?? $user->id) : null;

        return [
            'fileProject' => 'required|file|mimes:zip,rar,pdf,png,jpg,jpeg|max:51200',
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('files', 'nama_file')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                }),
            ],
        ];
    }

    public function updatedFileProject()
    {
        $this->validateOnly('fileProject');
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'filter')) {
            $this->resetPage();
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
            session()->flash('error', 'Silakan masuk terlebih dahulu sebelum mengunggah berkas.');
            return;
        }

        $extension = $this->fileProject->getClientOriginalExtension();

        $userNameSanitized = Str::slug($user->nama ?? $user->name ?? 'pengguna', '-');
        $fileNameSanitized = Str::slug($this->nama, '-');

        $customFileName = $userNameSanitized . '-' . $fileNameSanitized . '-' . time() . '.' . $extension;

        $path = $this->fileProject->storeAs('files', $customFileName, 'public');

        FileModel::create([
            'user_id'   => $user->user_id ?? $user->id,
            'nama_file' => $this->nama,
            'file'      => $path,
        ]);

        $this->reset(['fileProject', 'nama']);
        $this->resetPage();
        session()->flash('message', 'Berkas berhasil disimpan di penyimpanan pribadi Anda.');
    }

    public function openPreviewModal($fileId)
    {
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id ?? Auth::id();

        $this->selectedFile = FileModel::where('file_id', $fileId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (Storage::disk('public')->exists($this->selectedFile->file)) {
            $extension = strtolower(pathinfo($this->selectedFile->file, PATHINFO_EXTENSION));

            $this->previewUrl = '/storage/' . ltrim($this->selectedFile->file, '/');
            $this->fileExtension = $extension;
            $this->showModal = true;
        } else {
            session()->flash('error', 'Berkas tidak ditemukan di server.');
        }
    }

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

        session()->flash('error', 'Berkas tidak ditemukan di server.');
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

        if ($this->selectedFile && $this->selectedFile->file_id === $fileRecord->file_id) {
            $this->closePreviewModal();
        }

        session()->flash('message', 'Berkas berhasil dihapus.');
    }

    protected function getUploadedFilesQuery()
    {
        $user = Auth::user();
        $userId = $user ? ($user->user_id ?? $user->id) : null;

        $query = FileModel::query();

        if (! $userId) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('user_id', $userId);

        if ($this->filterName) {
            $query->where('nama_file', 'like', '%' . $this->filterName . '%');
        }

        if ($this->filterExtension) {
            $query->where('file', 'like', '%.' . strtolower($this->filterExtension));
        }

        if ($this->filterUploadAt && strtotime($this->filterUploadAt) !== false) {
            $query->whereDate('created_at', date('Y-m-d', strtotime($this->filterUploadAt)));
        }

        return $query->orderByDesc('created_at');
    }

    public function render()
    {
        $uploadedFiles = $this->getUploadedFilesQuery()->paginate(10);

        return view('livewire.user.dokumen', [
            'uploadedFiles' => $uploadedFiles,
        ])->layout('layouts.user');
    }
}