<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\PermohonanIzin as PermohonanIzinModel;
use App\Models\presensi as PresensiModel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
class Riwayat extends Component
{
    use WithPagination;

    public $filterStatus = 'semua';
    public $tanggalMulai = '';
    public $tanggalSelesai = '';

    public $editingPresensiId = null;
    public $editingLogbook = '';
    public $isEditModalOpen = false;

    protected $rules = [
        'editingLogbook' => 'required|min:10',
    ];

    protected $messages = [
        'editingLogbook.required' => 'Logbook harian wajib diisi.',
        'editingLogbook.min'      => 'Logbook minimal 10 karakter.',
    ];

    /**
     * Helper untuk mengecek apakah user yang login sudah lulus.
     */
    private function isLulus(): bool
    {
        $user = auth()->user();

        if (!$user || !$user->status) {
            return false;
        }

        $statusValue = $user->status instanceof \BackedEnum 
            ? $user->status->value 
            : $user->status;

        return strtolower((string) $statusValue) === 'lulus';
    }

    public function mount()
    {
        $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSelesai = now()->format('Y-m-d');
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingTanggalMulai()
    {
        $this->resetPage();
    }

    public function updatingTanggalSelesai()
    {
        $this->resetPage();
    }

    public function resetFilterTanggal()
    {
        $this->tanggalMulai = '';
        $this->tanggalSelesai = '';
        $this->resetPage();
    }

    public function editLogbook($presensiId)
    {
        if ($this->isLulus()) {
            session()->flash('error', 'Logbook tidak dapat diubah karena Anda sudah dinyatakan LULUS.');
            return;
        }

        $presensi = PresensiModel::with('logBooks')->find($presensiId);

        if ($presensi && $presensi->user_id === auth()->id()) {
            $logBook = $presensi->logBooks->first();

            $this->editingPresensiId = $presensi->presensi_id;
            $this->editingLogbook = $logBook?->kegiatan ?? '';
            $this->isEditModalOpen = true;
        }
    }

    public function updateLogbook()
    {
        if ($this->isLulus()) {
            session()->flash('error', 'Logbook tidak dapat diubah karena Anda sudah dinyatakan LULUS.');
            $this->closeModal();
            return;
        }

        $this->validate();

        $presensi = PresensiModel::findOrFail($this->editingPresensiId);

        if ($presensi->user_id !== auth()->id()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah logbook ini.');
            $this->closeModal();
            return;
        }

        log_book::updateOrCreate(
            [
                'presensi_id' => $presensi->presensi_id,
                'user_id'     => auth()->id(),
            ],
            [
                'kegiatan'    => $this->editingLogbook,
            ]
        );

        $this->closeModal();
        session()->flash('message', 'Logbook berhasil diperbarui!');
    }

    public function closeModal()
    {
        $this->isEditModalOpen = false;
        $this->editingPresensiId = null;
        $this->editingLogbook = '';
        $this->resetValidation();
    }

    private function labelAbsenSusulan(PermohonanIzinModel $p): string
    {
        $bagian = [];
        if ($p->absen_masuk) $bagian[] = 'Masuk';
        if ($p->absen_pulang) $bagian[] = 'Keluar';

        return 'ABSEN (' . implode(' & ', $bagian) . ')';
    }

    public function render()
    {
        // Set locale Carbon ke Indonesia untuk nama hari & bulan
        Carbon::setLocale('id');

        $userId = auth()->id();
        $userIsLulus = $this->isLulus();

        // 1) Data presensi asli
        $presensiList = PresensiModel::with(['logBooks'])
            ->where('user_id', $userId)
            ->when($this->tanggalMulai, function ($query) {
                $query->whereDate('tanggal', '>=', $this->tanggalMulai);
            })
            ->when($this->tanggalSelesai, function ($query) {
                $query->whereDate('tanggal', '<=', $this->tanggalSelesai);
            })
            ->get();

        $baris = collect();

        foreach ($presensiList as $presensi) {
            $statusValue = $presensi->status_kehadiran?->value ?? $presensi->status_kehadiran;

            // Format tanggal & jam ke standar Indonesia
            $tanggalFormatIndo = $presensi->tanggal 
                ? Carbon::parse($presensi->tanggal)->translatedFormat('l, d F Y') 
                : '-';

            $jamMasukFormatIndo = $presensi->absen_masuk 
                ? Carbon::parse($presensi->absen_masuk)->format('H.i') . ' WIB' 
                : '-';

            $jamPulangFormatIndo = $presensi->absen_keluar 
                ? Carbon::parse($presensi->absen_keluar)->format('H.i') . ' WIB' 
                : '-';

            $baris->push([
                'tanggal_sort' => $presensi->tanggal,
                'tanggal'      => $tanggalFormatIndo,
                'jam_masuk'    => $jamMasukFormatIndo,
                'jam_pulang'   => $jamPulangFormatIndo,
                'status'       => strtoupper($statusValue ?? '-'),
                'logbook'      => $presensi->logBooks->first()?->kegiatan,
                'presensi_id'  => $presensi->presensi_id,
                'bisa_edit'    => !$userIsLulus,
            ]);
        }

        // 2) Pengajuan Absen Susulan
        $pengajuanAbsen = PermohonanIzinModel::where('user_id', $userId)
            ->where('jenis', 'absen')
            ->get();

        foreach ($pengajuanAbsen as $p) {
            $awal = Carbon::parse($p->tanggal_awal ?? $p->tanggal_permohonan);
            $akhir = $p->tanggal_akhir ? Carbon::parse($p->tanggal_akhir) : $awal;

            foreach (CarbonPeriod::create($awal, $akhir) as $tgl) {
                $tglString = $tgl->format('Y-m-d');

                if ($this->tanggalMulai && $tglString < $this->tanggalMulai) continue;
                if ($this->tanggalSelesai && $tglString > $this->tanggalSelesai) continue;

                $prefix = match ($p->status) {
                    'pending'   => 'MENUNGGU: ',
                    'ditolak'   => 'DITOLAK: ',
                    'disetujui' => 'DISETUJUI: ',
                    default     => '',
                };

                $baris->push([
                    'tanggal_sort' => $tgl->copy(),
                    'tanggal'      => $tgl->translatedFormat('l, d F Y'),
                    'jam_masuk'    => '-',
                    'jam_pulang'   => '-',
                    'status'       => $prefix . $this->labelAbsenSusulan($p),
                    'logbook'      => $p->alasan,
                    'presensi_id'  => null,
                    'bisa_edit'    => false,
                ]);
            }
        }

        // Filter status
        if ($this->filterStatus !== 'semua') {
            $baris = $baris->filter(function ($item) {
                return str_contains(strtolower($item['status']), strtolower($this->filterStatus));
            });
        }

        $baris = $baris->sortByDesc('tanggal_sort')->values();

        // Pagination
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $items = $baris->slice(($page - 1) * $perPage, $perPage)->values();

        $dataRiwayat = new LengthAwarePaginator(
            $items,
            $baris->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $totalHadir = PresensiModel::where('user_id', $userId)
            ->where('status_kehadiran', 'hadir')
            ->count();

        $totalIzinSakit = PresensiModel::where('user_id', $userId)
            ->whereIn('status_kehadiran', ['izin', 'sakit'])
            ->count();

        $totalMenunggu = PermohonanIzinModel::where('user_id', $userId)
            ->where('jenis', 'absen')
            ->where('status', 'pending')
            ->count();

        return view('livewire.user.riwayat', [
            'dataRiwayat'    => $dataRiwayat,
            'totalHadir'     => $totalHadir,
            'totalIzinSakit' => $totalIzinSakit,
            'totalMenunggu'  => $totalMenunggu,
        ]);
    }
}
