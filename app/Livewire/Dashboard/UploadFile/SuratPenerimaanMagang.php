<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\file as FileModel;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Notifications\BerkasUploadedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class SuratPenerimaanMagang extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public $files = [];

    public bool $showPreviewModal = false;
    public ?string $previewUrl = null;
    public ?string $previewUserName = null;
    public ?string $previewFileType = 'pdf';

    protected $namaFileKategori = 'surat_penerimaan_magang';

    public function boot(): void
    {
        Carbon::setLocale('id');
    }

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

            $extension = pathinfo($suratFile->file, PATHINFO_EXTENSION);
            $this->previewFileType = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp']) ? 'image' : 'pdf';

            $this->showPreviewModal = true;
        }
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewUrl = null;
        $this->previewUserName = null;
        $this->previewFileType = 'pdf';
    }

    public function updatedFiles($value, $key)
    {
        $userId = $key;

        $this->validateOnly("files.$userId", [
            "files.$userId" => 'file|mimes:pdf|max:5120',
        ], [
            "files.$userId.mimes" => 'Berkas harus berformat PDF.',
            "files.$userId.max"   => 'Ukuran berkas tidak boleh lebih dari 5MB.',
        ], [
            "files.$userId" => 'Berkas',
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

        $uploader = Auth::user();
        $user->notify(new BerkasUploadedNotification('Surat Penerimaan Magang', $uploader->nama ?? 'Admin/Mentor'));

        unset($this->files[$userId]);

        session()->flash('message', 'Surat penerimaan magang (PDF) untuk ' . $user->nama . ' berhasil diunggah!');
    }

    public function render()
    {
        Carbon::setLocale('id');

        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
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
            // ✅ Hitung apakah user sudah punya surat penerimaan magang (0 = belum ada)
            ->addSelect(['sudah_ada_surat' => FileModel::selectRaw('COUNT(*)')
                ->whereColumn('user_id', 'users.user_id')
                ->where('nama_file', $this->namaFileKategori)
            ])
            // ✅ Urutan: yang BELUM upload (0) tampil duluan
            ->orderBy('sudah_ada_surat', 'asc')
            ->orderByRaw("CASE 
                WHEN status = 'aktif' THEN 1 
                WHEN status = 'lulus' THEN 2 
                ELSE 3 
            END ASC")
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.surat-penerimaan-magang', compact('users'));
    }
}