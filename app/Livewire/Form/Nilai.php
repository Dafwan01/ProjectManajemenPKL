<?php

namespace App\Livewire\Form;

use App\Models\Nilai as NilaiModel;
use App\Models\User;
use Livewire\Component;

class Nilai extends Component
{
    public $userId;
    public ?User $user = null;

    public $kedisiplinan = 0;
    public $kemampuan_teknis = 0;
    public $problem_solving = 0;
    public $komunikasi_kerjasama = 0;
    public $kualitas_ketepatan = 0;
    public $catatan = '';

    public $sudahAdaNilai = false; // Flag status nilai

    protected function rules()
    {
        return [
            'kedisiplinan' => 'required|integer|min:0|max:100',
            'kemampuan_teknis' => 'required|integer|min:0|max:100',
            'problem_solving' => 'required|integer|min:0|max:100',
            'komunikasi_kerjasama' => 'required|integer|min:0|max:100',
            'kualitas_ketepatan' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
        ];
    }

    protected $messages = [
        'kedisiplinan.required' => 'Nilai kedisiplinan wajib diisi.',
        'kemampuan_teknis.required' => 'Nilai kemampuan teknis wajib diisi.',
        'problem_solving.required' => 'Nilai problem solving wajib diisi.',
        'komunikasi_kerjasama.required' => 'Nilai komunikasi & kerja sama wajib diisi.',
        'kualitas_ketepatan.required' => 'Nilai kualitas & ketepatan waktu wajib diisi.',
        '*.integer' => 'Nilai harus berupa angka.',
        '*.min' => 'Nilai minimal 0.',
        '*.max' => 'Nilai maksimal 100.',
    ];

    public function mount($userId = null)
    {
        $this->userId = $userId;
        $this->user = User::findOrFail($userId);

        $nilai = NilaiModel::where('user_id', $userId)->first();

        if ($nilai) {
            $this->kedisiplinan = $nilai->kedisiplinan;
            $this->kemampuan_teknis = $nilai->kemampuan_teknis;
            $this->problem_solving = $nilai->problem_solving;
            $this->komunikasi_kerjasama = $nilai->komunikasi_kerjasama;
            $this->kualitas_ketepatan = $nilai->kualitas_ketepatan;
            $this->catatan = $nilai->catatan;
            $this->sudahAdaNilai = true;
        }
    }

    public function simpan()
    {
        $this->validate();

        NilaiModel::updateOrCreate(
            ['user_id' => $this->userId],
            [
                'kedisiplinan' => $this->kedisiplinan,
                'kemampuan_teknis' => $this->kemampuan_teknis,
                'problem_solving' => $this->problem_solving,
                'komunikasi_kerjasama' => $this->komunikasi_kerjasama,
                'kualitas_ketepatan' => $this->kualitas_ketepatan,
                'catatan' => $this->catatan,
            ]
        );

        $this->sudahAdaNilai = true;

        session()->flash('message', 'Nilai untuk ' . $this->user->nama . ' berhasil disimpan!');
        $this->dispatch('close-nilai-modal');
    }

    public function tutup()
    {
        $this->dispatch('close-nilai-modal');
    }

    public function render()
    {
        return view('livewire.form.nilai');
    }
}