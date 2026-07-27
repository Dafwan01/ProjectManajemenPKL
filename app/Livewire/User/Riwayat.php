<?php

namespace App\Livewire\User;

use Livewire\Component;
use Carbon\Carbon;

class Riwayat extends Component
{
    public $search = '';
    public $filterStatus = 'semua';

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

    /**
     * Membuka modal edit dan menyiapkan data logbook yang dipih
     */
    public function editLogbook($id)
    {
        $riwayatData = session()->get('riwayat_presensi', $this->getDefaultRiwayat());
        $item = collect($riwayatData)->firstWhere('id', $id);

        if ($item) {
            $item = (object) $item;
            $this->editingId = $item->id;
            $this->editingLogbook = $item->logbook ?? '';
            $this->isEditModalOpen = true;
        }
    }

    /**
     * Menyimpan perubahan logbook ke dalam Session
     */
    public function updateLogbook()
    {
        $this->validate();

        $riwayatData = session()->get('riwayat_presensi', $this->getDefaultRiwayat());

        foreach ($riwayatData as $index => $item) {
            $itemId = is_array($item) ? ($item['id'] ?? null) : ($item->id ?? null);
            if ($itemId == $this->editingId) {
                if (is_array($riwayatData[$index])) {
                    $riwayatData[$index]['logbook'] = $this->editingLogbook;
                } else {
                    $riwayatData[$index]->logbook = $this->editingLogbook;
                }
                break;
            }
        }

        session()->put('riwayat_presensi', $riwayatData);

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

    /**
     * Data dummy cadangan jika Session riwayat_presensi masih kosong
     */
    private function getDefaultRiwayat()
    {
        return [
            [
                'id' => 1,
                'nama' => 'Jonathan',
                'sekolah' => 'Institut Bisnis dan Informatika Bogor',
                'tanggal' => 'Jumat, 24/07/2026',
                'jam_masuk' => '2026-07-24 07:55:00',
                'jam_pulang' => '2026-07-24 16:05:00',
                'status' => 'HADIR',
                'logbook' => 'Mengimplementasikan komponen UI presensi terpisah, menangani integrasi kamera real-time, dan perekaman geolocation GPS via Alpine.js.',
                'latitude' => '-6.5971',
                'longitude' => '106.8060'
            ]
        ];
    }

    public function render()
    {
        // 1. Ambil data dari Session, jika belum ada gunakan default dummy
        $riwayatData = session()->get('riwayat_presensi', $this->getDefaultRiwayat());

        // 2. Format & Transformasi Data
        $dataFiltered = collect($riwayatData)
            ->map(function ($item) {
                $item = (object) $item;

                // Helper untuk parsing format jam menggunakan Carbon secara aman
                $formatJam = function ($time) {
                    if (empty($time) || $time === '-') {
                        return '-';
                    }
                    try {
                        return Carbon::parse($time)->format('H:i') . ' WIB';
                    } catch (\Exception $e) {
                        return $time; // Fallback jika format teks jam biasa (misal: "08:00 WIB")
                    }
                };

                return [
                    'id'         => $item->id ?? null,
                    'nama'       => $item->nama ?? '-',
                    'sekolah'    => $item->sekolah ?? '-',
                    'tanggal'    => $item->tanggal ?? '-',
                    'jam_masuk'  => $formatJam($item->jam_masuk ?? null),
                    'jam_pulang' => $formatJam($item->jam_pulang ?? null),
                    'status'     => $item->status ?? '-',
                    'logbook'    => $item->logbook ?? '-',
                    'latitude'   => $item->latitude ?? null,
                    'longitude'  => $item->longitude ?? null,
                ];
            })
            // 3. Filter Berdasarkan Pencarian (Search) dan Filter Status
            ->filter(function ($item) {
                $matchSearch = empty($this->search) || 
                    str_contains(strtolower($item['logbook']), strtolower($this->search)) ||
                    str_contains(strtolower($item['tanggal']), strtolower($this->search));
                    
                $matchStatus = ($this->filterStatus === 'semua') || 
                    (strtoupper($item['status']) === strtoupper($this->filterStatus));

                return $matchSearch && $matchStatus;
            });

        return view('livewire.user.riwayat', [
            'dataRiwayat' => $dataFiltered
        ])->layout('layouts.user');
    }
}