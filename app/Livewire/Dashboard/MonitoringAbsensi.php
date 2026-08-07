<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\log_book;
use App\Models\presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class MonitoringAbsensi extends Component
{
    use WithPagination;

    public bool $showLogbookModal = false;
    public string $selectedLogbookText = '';
    public string $selectedLogbookUser = '';
    public string $tanggal = '';
    public bool $showMap = false;
    public $locations = [];

    // State untuk Modal Edit Absen
    public bool $showEditModal = false;
    public $selectedPresensiId = null;
    public $editNamaUser = '';
    public $editStatusKehadiran = 'hadir';
    public $editAbsenMasuk = '';
    public $editAbsenKeluar = '';
    public $editLogbook = '';

    public function mount()
    {
        $this->tanggal = now()->format('Y-m-d');
    }

    public function updatingTanggal()
    {
        $this->resetPage();
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

        // Ambil string value role secara konsisten (baik bertipe Enum maupun String)
        $userRole = $currentUser->role instanceof \UnitEnum 
            ? $currentUser->role->value 
            : $currentUser->role;

        return $userRole === UserRole::MENTOR->value;
    }

    // --- BUKA MODAL EDIT ---
    public function editAbsen($presensiId)
    {
        $presensi = presensi::with(['user', 'logBooks.user'])->find($presensiId);

        if ($presensi) {
            $user = $presensi->user ?? $presensi->logBooks->first()?->user;

            $this->selectedPresensiId = $presensi->presensi_id;
            $this->editNamaUser = $user->nama ?? $user->name ?? 'Pengguna';
            $this->editStatusKehadiran = is_object($presensi->status_kehadiran) 
                ? $presensi->status_kehadiran->value 
                : ($presensi->status_kehadiran ?? 'hadir');
            
            $this->editAbsenMasuk = $presensi->absen_masuk;
            $this->editAbsenKeluar = $presensi->absen_keluar;
            $this->editLogbook = $presensi->logBooks->first()?->kegiatan ?? '';

            $this->showEditModal = true;
        }
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['selectedPresensiId', 'editNamaUser', 'editStatusKehadiran', 'editAbsenMasuk', 'editAbsenKeluar', 'editLogbook']);
    }

    // --- SIMPAN EDIT ABSEN ---
    public function updateAbsen()
    {
        $this->validate([
            'editStatusKehadiran' => 'required',
            'editAbsenMasuk'      => 'nullable',
            'editAbsenKeluar'     => 'nullable',
            'editLogbook'         => 'nullable|string',
        ]);

        $presensi = presensi::find($this->selectedPresensiId);

        if ($presensi) {
            // Update data presensi
            $presensi->update([
                'status_kehadiran' => $this->editStatusKehadiran,
                'absen_masuk'      => $this->editAbsenMasuk ?: null,
                'absen_keluar'     => $this->editAbsenKeluar ?: null,
            ]);

            // Update atau buat logbook baru jika ada input kegiatan
            if ($this->editLogbook) {
                log_book::updateOrCreate(
                    [
                        'presensi_id' => $presensi->presensi_id,
                    ],
                    [
                        'user_id'  => $presensi->user_id ?? $presensi->logBooks->first()?->user_id,
                        'kegiatan' => $this->editLogbook,
                    ]
                );
            }

            session()->flash('message', 'Data absensi berhasil diperbarui!');
            $this->closeEditModal();
        }
    }

    // --- PETA LOKASI ---
    public function openMap()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $dataPresensi = presensi::with(['user', 'logBooks.user'])
            ->whereHas('user', function ($query) use ($currentUser, $isMentor) {
                $query->where('role', UserRole::PKL->value);

                // Filter berdasarkan nama mentor jika pengakses adalah Mentor
                if ($isMentor) {
                    $query->where('mentor', $currentUser->nama);
                }
            })
            ->whereDate('tanggal', $this->tanggal)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $this->locations = $dataPresensi->map(function ($item) {
            $user = $item->user ?? $item->logBooks->first()?->user;

            return [
                'nama'       => $user->nama ?? $user->name ?? 'Tanpa Nama',
                'sekolah'    => $user->sekolah?->nama_sekolah ?? '-', 
                'jam_masuk'  => $item->absen_masuk ? substr($item->absen_masuk, 0, 5) : '-',
                'jam_keluar' => $item->absen_keluar ? substr($item->absen_keluar, 0, 5) : '-',
                'lat'        => (float) $item->latitude,
                'lng'        => (float) $item->longitude,
            ];
        })->toArray();

        $this->showMap = true;
        $this->dispatch('init-leaflet-map', locations: $this->locations);
    }

    public function closeMap()
    {
        $this->showMap = false;
    }

    public function openLogbookModal($text, $nama)
    {
        $this->selectedLogbookText = $text;
        $this->selectedLogbookUser = $nama;
        $this->showLogbookModal = true;
    }

    public function closeLogbookModal()
    {
        $this->showLogbookModal = false;
        $this->selectedLogbookText = '';
        $this->selectedLogbookUser = '';
    }

    // --- RENDER COMPONENT ---
    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $presensis = presensi::with(['user', 'logBooks.user', 'user.detailJadwals.jadwal'])
            ->whereHas('user', function ($query) use ($currentUser, $isMentor) {
                $query->where('role', UserRole::PKL->value);

                // Filter khusus anak bimbingan jika pengakses adalah Mentor
                if ($isMentor) {
                    $query->where('mentor', $currentUser->nama);
                }
            })
            ->when($this->tanggal, function ($query) {
                $query->whereDate('tanggal', $this->tanggal);
            })
            ->orderBy('presensi_id', 'desc')
            ->paginate(10);

        return view('livewire.dashboard.monitoring-absensi', [
            'presensis' => $presensis,
        ]);
    }
}
