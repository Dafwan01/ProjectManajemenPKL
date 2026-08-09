<?php

namespace App\Livewire\Dashboard\UploadFile;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Nilai as NilaiModel; // ✅ sesuaikan namespace model Nilai kamu
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Nilai extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;
    public $selectedUserId = null;

    public bool $showPdfModal = false;
    public $pdfUserId = null;

    public function boot(): void
    {
        Carbon::setLocale('id');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
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

    public function openForm($userId): void
    {
        $this->selectedUserId = $userId;
        $this->showModal = true;
    }

    #[On('close-nilai-modal')]
    public function closeForm(): void
    {
        $this->showModal = false;
        $this->selectedUserId = null;
    }

    public function openPdfModal($userId): void
    {
        $this->pdfUserId = $userId;
        $this->showPdfModal = true;
    }

    public function closePdfModal(): void
    {
        $this->showPdfModal = false;
        $this->pdfUserId = null;
    }

    #[Layout('layouts.dashboard')]
    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $users = User::query()
            ->where('role', UserRole::PKL->value)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('sekolah', function ($subQuery) {
                          $subQuery->where('nama_sekolah', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($isMentor, function ($query) use ($currentUser) {
                $query->where('mentor', $currentUser->nama);
            })
            // ✅ Hitung apakah user sudah punya nilai (0 = belum ada, >0 = sudah ada)
            ->addSelect(['sudah_ada_nilai' => NilaiModel::selectRaw('COUNT(*)')
                ->whereColumn('user_id', 'users.user_id')
            ])
            // ✅ Urutan: yang BELUM ada nilai (0) tampil duluan, baru status aktif/lulus, lalu terbaru
            ->orderBy('sudah_ada_nilai', 'asc')
            ->orderByRaw("CASE 
                WHEN status = 'aktif' THEN 1 
                WHEN status = 'lulus' THEN 2 
                ELSE 3 
            END ASC")
            ->latest('tanggal_mulai')
            ->with(['nilai', 'nilais', 'sekolah'])
            ->paginate(10);

        return view('livewire.dashboard.upload-file.nilai', compact('users'));
    }
}