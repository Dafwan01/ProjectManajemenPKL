<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\log_book;
use App\Models\PermohonanIzin;
use App\Models\presensi;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function editAbsen($presensiId)
    {
        $presensi = presensi::with(['user', 'logBooks.user'])->find($presensiId);

        if ($presensi) {
            $user = $presensi->user ?? $presensi->logBooks->first()?->user;

            $this->selectedPresensiId = $presensi->presensi_id;
            $this->editNamaUser = $user->nama ?? $user->name ?? 'User';
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
            $presensi->update([
                'status_kehadiran' => $this->editStatusKehadiran,
                'absen_masuk'      => $this->editAbsenMasuk ?: null,
                'absen_keluar'     => $this->editAbsenKeluar ?: null,
            ]);

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

    public function openMap()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        $dataPresensi = presensi::with(['user', 'logBooks.user'])
            ->whereHas('user', function ($query) use ($currentUser, $isMentor) {
                $query->where('role', UserRole::PKL->value);

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
                'nama' => $user->nama ?? $user->name ?? 'Tanpa Nama',
                'sekolah' => $user->asal_sekolah ?? '-',
                'jam_masuk' => $item->absen_masuk ? substr($item->absen_masuk, 0, 5) : '-',
                'jam_keluar' => $item->absen_keluar ? substr($item->absen_keluar, 0, 5) : '-',
                'lat' => (float) $item->latitude,
                'lng' => (float) $item->longitude,
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

    /**
     * Bentuk label jenis pengajuan untuk baris virtual (izin/sakit/absen).
     */
    private function labelJenisPengajuan(PermohonanIzin $p): string
    {
        $label = strtoupper($p->jenis);

        if ($p->jenis === 'absen') {
            $bagian = [];
            if ($p->absen_masuk) $bagian[] = 'Masuk';
            if ($p->absen_pulang) $bagian[] = 'Pulang';
            $label .= ' (' . implode(' & ', $bagian) . ')';
        }

        return $label;
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isMentor = $this->isMentorUser();

        // 1) Data presensi asli pada tanggal terpilih
        $presensis = presensi::with(['user', 'logBooks.user', 'user.detailJadwals.jadwal'])
            ->whereHas('user', function ($query) use ($currentUser, $isMentor) {
                $query->where('role', UserRole::PKL->value);

                if ($isMentor) {
                    $query->where('mentor', $currentUser->nama);
                }
            })
            ->when($this->tanggal, function ($query) {
                $query->whereDate('tanggal', $this->tanggal);
            })
            ->get();

        foreach ($presensis as $p) {
            $p->is_pengajuan = false;
            $p->pengajuan_label = null;
        }

        $userIdSudahAda = $presensis->pluck('user_id')->toArray();
        $baris = $presensis->values();

        // 2) Baris virtual dari pengajuan pending/ditolak (user belum punya presensi di tanggal ini)
        if ($this->tanggal) {
            $tglTarget = Carbon::parse($this->tanggal);

            $permohonans = PermohonanIzin::with('user')
                ->whereIn('status', ['pending', 'ditolak'])
                ->whereHas('user', function ($query) use ($currentUser, $isMentor) {
                    $query->where('role', UserRole::PKL->value);
                    if ($isMentor) {
                        $query->where('mentor', $currentUser->nama);
                    }
                })
                ->get()
                ->filter(function ($p) use ($tglTarget, $userIdSudahAda) {
                    if (in_array($p->user_id, $userIdSudahAda)) {
                        return false;
                    }

                    $awal = Carbon::parse($p->tanggal_awal ?? $p->tanggal_permohonan);
                    $akhir = $p->tanggal_akhir ? Carbon::parse($p->tanggal_akhir) : $awal;

                    return $tglTarget->betweenIncluded($awal, $akhir);
                });

            foreach ($permohonans as $p) {
                $prefix = $p->status === 'pending' ? 'MENUNGGU: ' : 'DITOLAK: ';

                $virtual = new \stdClass();
                $virtual->presensi_id = null;
                $virtual->user = $p->user;
                $virtual->tanggal = $tglTarget->copy();
                $virtual->foto_masuk = null;
                $virtual->foto_keluar = null;
                $virtual->absen_masuk = null;
                $virtual->absen_keluar = null;
                $virtual->status_kehadiran = null;
                $virtual->logBooks = collect();
                $virtual->is_pengajuan = true;
                $virtual->pengajuan_status = $p->status;
                $virtual->pengajuan_label = $prefix . $this->labelJenisPengajuan($p);
                $virtual->alasan_pengajuan = $p->alasan;

                $baris->push($virtual);
            }
        }

        // Urutkan berdasarkan nama
        $sorted = $baris->sortBy(function ($item) {
            return $item->user->nama ?? $item->user->name ?? '';
        })->values();

        // Pagination manual atas hasil gabungan
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $items = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        $presensisPaginated = new LengthAwarePaginator(
            $items,
            $sorted->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.dashboard.monitoring-absensi', [
            'presensis' => $presensisPaginated,
        ]);
    }
}