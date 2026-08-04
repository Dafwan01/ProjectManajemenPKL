<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\file as FileModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\Toastable;

#[Layout('layouts.dashboard')]
class SuratPenerimaanMagang extends Component
{
    use Toastable;

    use WithPagination, WithFileUploads;

    public string $search = '';
    public $files = [];

    // State Modal Preview PDF
    public bool $showPreviewModal = false;
    public ?string $previewUrl = null;
    public ?string $previewUserName = null;

    protected $namaFileKategori = 'surat_penerimaan_magang';

    /**
     * Helper untuk mengecek apakah user yang login adalah Mentor secara aman
     */
    private function isMentorUser(): bool
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return false;
        }

        $userRole = $currentUser->role instanceof \UnitEnum 
            ? $currentUser->role->value 
            : $currentUser->role;

        return $userRole === UserRole::MENTOR->value;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openPreview($userId): void
    {
        $user = User::with(['files' => function ($query) {
            $query->where('nama_file', $this->namaFileKategori);
        }])->findOrFail($userId);

        $suratFile = $user->files->first();

        if ($suratFile && Storage::disk('public')->exists($suratFile->file)) {
            $this->previewUrl = Storage::url($suratFile->file);
            $this->previewUserName = $user->nama;
            $this->showPreviewModal = true;
        }
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewUrl = null;
        $this->previewUserName = null;
    }

    public function updatedFiles($value, $key)
    {
        $userId = $key;

        // Validasi Dikhususkan Hanya Untuk PDF
        $this->validateOnly("files.$userId", [
            "files.$userId" => 'file|mimes:pdf|max:5120',
        ], [
            "files.$userId.mimes" => 'Berkas harus berformat PDF.',
            "files.$userId.max"   => 'Ukuran file tidak boleh lebih dari 5MB.',
        ], [
            "files.$userId" => 'File',
        ]);

        $user = User::findOrFail($userId);
        $file = $this->files[$userId];

        $fileLama = FileModel::where('user_id', $userId)
            ->where('nama_file', $this->namaFileKategori)
            ->first();

        if ($fileLama && Storage::disk('public')->exists($fileLama->file)) {
            Storage::disk('public')->delete($fileLama->file);
        }

        $extension = $file->getClientOriginalExtension();
        $namaFile = Str::slug($user->nama) . '-suratpenerimaanmagang.' . $extension;
        $path = $file->storeAs('files', $namaFile, 'public');

        FileModel::updateOrCreate(
            [
                'user_id' => $userId,
                'nama_file' => $this->namaFileKategori,
            ],
            [
                'file' => $path,
            ]
        );

        unset($this->files[$userId]);

        $this->toastSuccess( 'Surat penerimaan magang (PDF) untuk ' . $user->nama . ' berhasil diupload!');
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
            // Filter anak bimbingan jika pengakses adalah Mentor
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->with(['files' => function ($query) {
                $query->where('nama_file', $this->namaFileKategori);
            }])
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.surat-penerimaan-magang', compact('users'));
    }
}