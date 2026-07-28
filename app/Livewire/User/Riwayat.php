<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\User;
use App\Enums\UserRole;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.user')]
class Riwayat extends Component
{
    use WithPagination;

    private function currentUserId()
    {
        return auth()->id() ?? User::where('role', UserRole::PKL)->value('user_id');
    }

    public $search = '';
    public $filterStatus = 'semua';
    public $filterSource = 'semua';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $maxFilterRangeDays = 30;

    // State Edit Logbook
    public $editingId = null;
    public $editingLogbook = '';
    public $isEditModalOpen = false;

    protected $rules = [
        'editingLogbook' => 'required|min:10',
    ];

    protected $messages = [
        'editingLogbook.required' => 'Logbook harian wajib diisi.',
        'editingLogbook.min'      => 'Logbook minimal 10 karakter.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterSource()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    /**
     * Membuka modal edit dan menyiapkan data logbook yang dipilih
     */
    public function editLogbook($id)
    {
        $logBook = log_book::find($id);

        if ($logBook) {
            $this->editingId = $logBook->log_book_id;
            $this->editingLogbook = $logBook->kegiatan ?? '';
            $this->isEditModalOpen = true;
        }
    }

    /**
     * Menyimpan perubahan logbook ke database
     */
    public function updateLogbook()
    {
        $this->validate();

        $logBook = log_book::findOrFail($this->editingId);

        // Pastikan user cuma bisa edit logbook miliknya sendiri
        if ($logBook->user_id !== $this->currentUserId()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengedit logbook ini.');
            $this->closeModal();
            return;
        }

        $logBook->update([
            'kegiatan' => $this->editingLogbook,
        ]);

        $this->closeModal();
        session()->flash('message', 'Logbook berhasil diperbarui!');
    }

    public function closeModal()
    {
        $this->isEditModalOpen = false;
        $this->editingId = null;
        $this->editingLogbook = '';
        $this->resetValidation();
    }

    public function render()
    {
        $userId = $this->currentUserId();

        $logBooks = log_book::with(['presensi', 'user'])
            ->where('user_id', $userId)
            ->when($this->filterSource === 'session', fn ($query) => $query->whereRaw('0 = 1'))
            ->when($this->search, function ($query) {
                $query->where('kegiatan', 'like', '%' . $this->search . '%')
                    ->orWhereHas('presensi', function ($q) {
                        $q->whereDate('tanggal', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterStatus === 'absen', fn ($query) => $query->whereRaw('0 = 1'))
            ->when(in_array($this->filterStatus, ['hadir', 'izin', 'sakit']), function ($query) {
                $query->whereHas('presensi', function ($q) {
                    $q->where('status_kehadiran', strtolower($this->filterStatus));
                });
            })
            ->when($this->filterDateFrom || $this->filterDateTo, function ($query) {
                $query->when($this->filterDateFrom && $this->filterDateTo, function ($query) {
                    $from = \Carbon\Carbon::parse($this->filterDateFrom);
                    $to = \Carbon\Carbon::parse($this->filterDateTo);
                    if ($from->diffInDays($to) > $this->maxFilterRangeDays) {
                        $to = $from->copy()->addDays($this->maxFilterRangeDays);
                    }
                    $query->whereHas('presensi', function ($q) use ($from, $to) {
                        $q->whereDate('tanggal', '>=', $from->toDateString())
                          ->whereDate('tanggal', '<=', $to->toDateString());
                    });
                }, function ($query) {
                    $query->whereHas('presensi', function ($q) {
                        if ($this->filterDateFrom) {
                            $q->whereDate('tanggal', '>=', $this->filterDateFrom);
                        }
                        if ($this->filterDateTo) {
                            $q->whereDate('tanggal', '<=', $this->filterDateTo);
                        }
                    });
                });
            })
            ->latest('log_book_id')
            ->paginate(10);

        $dataRiwayat = $logBooks->through(function ($logBook) {
            $presensi = $logBook->presensi;

            return [
                'id'         => $logBook->log_book_id,
                'nama'       => $logBook->user->nama ?? '-',
                'sekolah'    => $logBook->user->asal_sekolah ?? '-',
                'tanggal'    => $presensi && $presensi->tanggal ? $presensi->tanggal->translatedFormat('l, d/m/Y') : '-',
                'jam_masuk'  => $presensi && $presensi->absen_masuk ? substr($presensi->absen_masuk, 0, 5) . ' WIB' : '-',
                'jam_pulang' => $presensi && $presensi->absen_keluar ? substr($presensi->absen_keluar, 0, 5) . ' WIB' : '-',
                'status'     => $presensi ? strtoupper($presensi->status_kehadiran?->value ?? '-') : '-',
                'logbook'    => $logBook->kegiatan ?? '-',
                'latitude'   => $presensi->latitude ?? null,
                'longitude'  => $presensi->longitude ?? null,
                'is_session' => false,
            ];
        });

        $sessionRiwayat = collect(session()->get('riwayat_presensi', []))
            ->map(function ($item) {
                $dateInfo = $this->parseSessionTanggal($item['tanggal'] ?? '');
                return [
                    'id'               => $item['id'] ?? null,
                    'nama'             => $item['nama'] ?? '-',
                    'sekolah'          => $item['sekolah'] ?? '-',
                    'tanggal'          => $item['tanggal'] ?? '-',
                    'jam_masuk'        => $item['jam_masuk'] ?? '-',
                    'jam_pulang'       => $item['jam_pulang'] ?? '-',
                    'status'           => $item['status'] ?? '-',
                    'logbook'          => $item['logbook'] ?? '-',
                    'status_pengajuan' => $item['status_pengajuan'] ?? 'pending',
                    'date_start'       => $dateInfo['start'] ?? null,
                    'date_end'         => $dateInfo['end'] ?? null,
                    'is_session'       => true,
                ];
            })
            ->when($this->filterSource === 'db', fn ($collection) => collect())
            ->when($this->search, function ($collection) {
                $needle = strtolower($this->search);
                return $collection->filter(function ($item) use ($needle) {
                    return str_contains(strtolower($item['logbook']), $needle)
                        || str_contains(strtolower($item['tanggal']), $needle)
                        || str_contains(strtolower($item['status']), $needle)
                        || str_contains(strtolower($item['nama']), $needle);
                });
            })
            ->when($this->filterStatus !== 'semua', function ($collection) {
                return $collection->filter(function ($item) {
                    $itemStatus = strtolower($item['status']);
                    if ($this->filterStatus === 'absen') {
                        return $itemStatus === 'absen';
                    }
                    return $itemStatus === strtolower($this->filterStatus);
                });
            })
            ->when($this->filterDateFrom || $this->filterDateTo, function ($collection) {
                $from = $this->filterDateFrom ? \Carbon\Carbon::parse($this->filterDateFrom) : null;
                $to = $this->filterDateTo ? \Carbon\Carbon::parse($this->filterDateTo) : null;

                if ($from && $to && $from->diffInDays($to) > $this->maxFilterRangeDays) {
                    $to = $from->copy()->addDays($this->maxFilterRangeDays);
                }

                return $collection->filter(function ($item) use ($from, $to) {
                    if (! $item['date_start'] || ! $item['date_end']) {
                        return false;
                    }

                    if ($from && $item['date_end']->lt($from)) {
                        return false;
                    }

                    if ($to && $item['date_start']->gt($to)) {
                        return false;
                    }

                    return true;
                });
            });

        // Statistik total (murni total keseluruhan, tidak terpengaruh pagination/filter)
        $totalHadir = log_book::where('user_id', $userId)
            ->whereHas('presensi', fn ($q) => $q->where('status_kehadiran', 'hadir'))
            ->count();

        $totalIzinSakit = log_book::where('user_id', $userId)
            ->whereHas('presensi', fn ($q) => $q->whereIn('status_kehadiran', ['izin', 'sakit']))
            ->count();

        return view('livewire.user.riwayat', [
            'dataRiwayat' => $dataRiwayat,
            'sessionRiwayat' => $sessionRiwayat,
            'totalHadir' => $totalHadir,
            'totalIzinSakit' => $totalIzinSakit,
        ]);
    }

    private function parseSessionTanggal($tanggal)
    {
        try {
            $tanggal = trim($tanggal);
            if (str_contains($tanggal, ' - ')) {
                [$start, $end] = explode(' - ', $tanggal, 2);
            } else {
                $parts = explode(', ', $tanggal);
                $start = trim(end($parts));
                $end = $start;
            }

            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($start));
            $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', trim($end));

            return ['start' => $startDate, 'end' => $endDate];
        } catch (\Exception $e) {
            return ['start' => null, 'end' => null];
        }
    }
}