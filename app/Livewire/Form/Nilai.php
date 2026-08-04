<?php

namespace App\Livewire\Form;

use App\Models\Nilai as NilaiModel;
use App\Models\file as FileModel;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Traits\Toastable;

class Nilai extends Component
{
    use Toastable;

    use WithFileUploads;

    public $userId;
    public ?User $user = null;

    public $kedisiplinan = 0;
    public $kemampuan_teknis = 0;
    public $problem_solving = 0;
    public $komunikasi_kerjasama = 0;
    public $kualitas_ketepatan = 0;
    public $catatan = '';

    public $file = null;
    public ?FileModel $fileNilaiLama = null;

    public $sudahAdaNilai = false;

    protected $namaFileKategori = 'nilai';
    protected $namaFilePdfKategori = 'nilai_pdf';

    protected function rules()
    {
        return [
            'kedisiplinan' => 'required|integer|min:0|max:100',
            'kemampuan_teknis' => 'required|integer|min:0|max:100',
            'problem_solving' => 'required|integer|min:0|max:100',
            'komunikasi_kerjasama' => 'required|integer|min:0|max:100',
            'kualitas_ketepatan' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
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
        'file.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
        'file.max' => 'Ukuran file maksimal 5MB.',
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

        $this->fileNilaiLama = FileModel::where('user_id', $userId)
            ->where('nama_file', $this->namaFileKategori)
            ->first();
    }

    public function simpan()
    {
        $this->validate();

        $nilai = NilaiModel::updateOrCreate(
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

        if ($this->file) {
            if ($this->fileNilaiLama && Storage::disk('public')->exists($this->fileNilaiLama->file)) {
                Storage::disk('public')->delete($this->fileNilaiLama->file);
            }

            $extension = $this->file->getClientOriginalExtension();
            $namaFile = Str::slug($this->user->nama) . '-nilai.' . $extension;
            $path = $this->file->storeAs('nilai', $namaFile, 'public');

            FileModel::updateOrCreate(
                [
                    'user_id' => $this->userId,
                    'nama_file' => $this->namaFileKategori,
                ],
                [
                    'file' => $path,
                ]
            );

            $this->fileNilaiLama = FileModel::where('user_id', $this->userId)
                ->where('nama_file', $this->namaFileKategori)
                ->first();
        }

        // Generate PDF otomatis kalau kelima nilai sudah tidak 0
        $semuaNilaiTerisi = $this->kedisiplinan > 0
            && $this->kemampuan_teknis > 0
            && $this->problem_solving > 0
            && $this->komunikasi_kerjasama > 0
            && $this->kualitas_ketepatan > 0;

        if ($semuaNilaiTerisi) {
            $this->generatePdfNilai($nilai);
        }

        $this->sudahAdaNilai = true;
        $this->file = null;

        $this->toastSuccess( 'Nilai untuk ' . $this->user->nama . ' berhasil disimpan!');
        $this->dispatch('close-nilai-modal');
    }

   private function generatePdfNilai(NilaiModel $nilai): void
{
    $rataRata = collect([
        $nilai->kedisiplinan,
        $nilai->kemampuan_teknis,
        $nilai->problem_solving,
        $nilai->komunikasi_kerjasama,
        $nilai->kualitas_ketepatan,
    ])->avg();

    // Tambahkan rata_rata ke object nilai supaya bisa dipakai di view
    $nilai->rata_rata = number_format($rataRata, 1);

    $pdf = Pdf::loadView('livewire.components.cetak-nilai', [
        'selectedUser' => $this->user,
        'nilaiUser' => $nilai,
    ]);

    $namaFilePdf = Str::slug($this->user->nama) . '-nilai.pdf';
    $path = 'files/' . $namaFilePdf;

    Storage::disk('public')->put($path, $pdf->output());

    FileModel::updateOrCreate(
        [
            'user_id' => $this->userId,
            'nama_file' => $this->namaFilePdfKategori,
        ],
        [
            'file' => $path,
        ]
    );
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