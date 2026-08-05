<?php

namespace App\Livewire\Form;

use App\Models\Nilai as NilaiModel;
use App\Models\file as FileModel;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Bidang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Nilai extends Component
{
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

        session()->flash('message', 'Nilai untuk ' . $this->user->nama . ' berhasil disimpan!');
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

        // Hitung predikat per aspek & predikat rata-rata,
        // supaya konsisten dengan yang dipakai di CetakNilai (Controller cetak manual).
        $aspek = [
            'kedisiplinan',
            'kemampuan_teknis',
            'problem_solving',
            'komunikasi_kerjasama',
            'kualitas_ketepatan',
        ];

        $predikatPerAspek = [];
        foreach ($aspek as $key) {
            $predikatPerAspek[$key] = $this->tentukanPredikat($nilai->{$key} ?? null);
        }

        $predikat = $this->tentukanPredikat($rataRata);

        // Ambil nama Divisi & Bidang milik user.
        // Query langsung by ID (bukan lewat relasi Eloquent $user->divisi->bidang)
        // supaya tidak tergantung pada guessing foreign key yang pernah bermasalah.
        $namaDivisi = null;
        $namaBidang = null;

        if (!empty($this->user->divisi_id)) {
            $divisi = Divisi::find($this->user->divisi_id);
            $namaDivisi = $divisi?->nama_divisi;

            if ($divisi && !empty($divisi->bidang_id)) {
                $bidangModel = Bidang::find($divisi->bidang_id);
                $namaBidang = $bidangModel?->nama_bidang;
            }
        }

        $pdf = Pdf::loadView('livewire.components.cetak-nilai', [
            'selectedUser'      => $this->user,
            'nilaiUser'         => $nilai,
            'predikat'          => $predikat,
            'predikatPerAspek'  => $predikatPerAspek,
            'namaDivisi'        => $namaDivisi,
            'namaBidang'        => $namaBidang,
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

    /**
     * Menentukan predikat berdasarkan rentang nilai.
     * Disamakan persis dengan logic di App\Livewire\Components\CetakNilai
     * supaya predikat yang tampil konsisten di kedua jalur cetak PDF.
     */
    private function tentukanPredikat($nilai): ?string
    {
        if ($nilai === null || $nilai === '') {
            return null;
        }

        $nilai = (float) $nilai;

        return match (true) {
            $nilai >= 95 => 'Sangat Baik',
            $nilai >= 86 => 'Sangat Baik',
            $nilai >= 80 => 'Baik Sekali',
            $nilai >= 75 => 'Baik',
            $nilai >= 70 => 'Baik',
            $nilai >= 65 => 'Cukup Baik',
            $nilai >= 60 => 'Cukup',
            $nilai >= 40 => 'Kurang',
            default      => 'Sangat Kurang',
        };
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