<?php
namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Sertifikat extends Component
{
    use WithPagination;

    // Property untuk Search Bar
    public $search = '';

    // State Modal & Form Generate Sertifikat
    public $showModal = false;
    public $selectedUser = null;
    public $nomorSertifikat = '';
    public $tanggalTerbit = '';

    // State Modal Preview PDF
    public bool $showPdfModal = false;
    public ?string $previewUrl = null;
    public ?string $previewUserName = null;

    public function mount(): void
    {
        $this->tanggalTerbit = date('Y-m-d');
    }

    // Reset halaman pagination saat mengetik di search bar
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Membuka Modal Generate & Menyiapkan Data Peserta
     */
   public function openUploadModal($userId): void
    {
        // Hanya query berdasarkan user_id
        $this->selectedUser = User::where('user_id', $userId)->firstOrFail();

        // Format nomor sertifikat otomatis menggunakan user_id
        $idNumber = $this->selectedUser->user_id;
        $this->nomorSertifikat = 'SERT/' . date('Y') . '/' . str_pad($idNumber, 4, '0', STR_PAD_LEFT);

        $this->showModal = true;
    }

    /**
     * Menutup Modal Generate
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedUser = null;
    }

    /**
     * Membuka Modal Preview PDF Sertifikat
     */
  public function openPdfModal($userId): void
    {
        // Hanya query berdasarkan user_id
        $user = User::where('user_id', $userId)->firstOrFail();

        if ($user->sertifikat) {
            $this->previewUrl = asset('storage/' . $user->sertifikat);
            $this->previewUserName = $user->nama;
            $this->showPdfModal = true;
        }
    }

    /**
     * Menutup Modal Preview PDF
     */
    public function closePdfModal(): void
    {
        $this->showPdfModal = false;
        $this->previewUrl = null;
        $this->previewUserName = null;
    }

    /**
     * Proses Generate PDF dan simpan ke Database
     */
    public function generate(CertificateService $certificateService): void
    {
        $this->validate([
            'nomorSertifikat' => 'required|string',
            'tanggalTerbit' => 'required|date',
        ]);

        try {
            $certificateService->generateForUser(
                $this->selectedUser,
                $this->nomorSertifikat,
                $this->tanggalTerbit
            );

            session()->flash('message', 'Sertifikat untuk ' . $this->selectedUser->nama . ' berhasil diterbitkan!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat sertifikat: ' . $e->getMessage());
        }
    }

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

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
            // Filter Search Bar (Nama, Asal Sekolah, atau Email)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('asal_sekolah', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            // Filter anak bimbingan jika pengakses adalah Mentor
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.sertifikat', compact('users'));
    }
}