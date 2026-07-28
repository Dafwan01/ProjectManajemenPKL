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

        // 1. Ambil ekstensi file asli
        $extension = $this->fileProject->getClientOriginalExtension();

        // 2. Sanitasi Nama User dan Nama File dari Input
        // Mengubah spasi/karakter aneh menjadi dash (-)
        $userNameSanitized = Str::slug($user->nama ?? $user->name ?? 'user', '-');
        $fileNameSanitized = Str::slug($this->nama, '-');

        // 3. Format: user-namafile.ext (contoh: john-doe-proposal-kerja.zip)
        $customFileName = $userNameSanitized . '-' . $fileNameSanitized . '.' . $extension;

        // Jika ingin tetap ada timestamp unik di tengah biar tidak saling menimpa jika namanya sama:
        // $customFileName = $userNameSanitized . '-' . $fileNameSanitized . '-' . time() . '.' . $extension;

        // 4. Simpan ke storage
        $path = $this->fileProject->storeAs('files', $customFileName, 'public');

        // 5. Insert ke Database
        FileModel::create([
            'user_id'   => $user->user_id,
            'nama_file' => $this->nama,
            'file'      => $path,
        ]);

        $this->reset(['fileProject', 'nama']);
        $this->loadUploadedFiles();
        session()->flash('message', 'File berhasil disimpan di storage pribadi Anda.');
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