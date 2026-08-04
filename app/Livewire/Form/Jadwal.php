<?php

namespace App\Livewire\Form;

use App\Enums\JadwalStatusKerja;
use App\Models\DetailJadwal;
use App\Models\Jadwal as JadwalModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Traits\Toastable;

class Jadwal extends Component
{
    use Toastable;

    #[Layout('layouts.dashboard')]

    public array $jadwalData = [];
    public array $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    public $userId;

    public function mount($userId = null)
    {
        $this->userId = $userId ?? optional(auth::user())->user_id ?? Auth::id();

        foreach ($this->daftarHari as $hari) {
            $detail = DetailJadwal::where('user_id', $this->userId)
                ->where('hari', $hari)
                ->with('jadwal')
                ->first();

            $this->jadwalData[$hari] = [
                'jam_masuk' => $detail && $detail->jadwal ? substr($detail->jadwal->jam_masuk, 0, 5) : '07:30',
                'jam_keluar' => $detail && $detail->jadwal ? substr($detail->jadwal->jam_keluar, 0, 5) : '16:30',
                'status_kerja' => $detail && $detail->jadwal ? (is_object($detail->jadwal->status_kerja) ? $detail->jadwal->status_kerja->value : $detail->jadwal->status_kerja) : 'WFO',
            ];
        }
    }

    protected function rules()
    {
        $rules = [];
        foreach ($this->daftarHari as $hari) {
            $rules["jadwalData.{$hari}.jam_masuk"] = [
                'required',
                'date_format:H:i',
                'after_or_equal:07:30',
            ];
            $rules["jadwalData.{$hari}.jam_keluar"] = [
                'required',
                'date_format:H:i',
                'before_or_equal:16:30',
                "after:jadwalData.{$hari}.jam_masuk",
            ];
            $rules["jadwalData.{$hari}.status_kerja"] = [
                'required',
                Rule::enum(JadwalStatusKerja::class),
            ];
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = [];
        foreach ($this->daftarHari as $hari) {
            $messages["jadwalData.{$hari}.jam_masuk.after_or_equal"] = "Jam masuk {$hari} paling pagi pukul 07:30.";
            $messages["jadwalData.{$hari}.jam_masuk.required"] = "Jam masuk {$hari} wajib diisi.";
            $messages["jadwalData.{$hari}.jam_keluar.before_or_equal"] = "Jam keluar {$hari} paling lama pukul 16:30.";
            $messages["jadwalData.{$hari}.jam_keluar.after"] = "Jam keluar {$hari} harus setelah jam masuk.";
            $messages["jadwalData.{$hari}.jam_keluar.required"] = "Jam keluar {$hari} wajib diisi.";
        }

        return $messages;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            foreach ($this->daftarHari as $hari) {
                $item = $this->jadwalData[$hari];

                $jadwal = JadwalModel::firstOrCreate([
                    'jam_masuk' => $item['jam_masuk'],
                    'jam_keluar' => $item['jam_keluar'],
                    'status_kerja' => $item['status_kerja'],
                ]);

                DetailJadwal::updateOrCreate(
                    [
                        'user_id' => $this->userId,
                        'hari' => $hari,
                    ],
                    [
                        'jadwal_id' => $jadwal->jadwal_id,
                    ]
                );
            }
        });

        $this->toastSuccess( 'Jadwal kerja berhasil diperbarui!');
        $this->dispatch('close-jadwal-modal');
    }

    public function render()
    {
        return view('livewire.form.jadwal');
    }
}