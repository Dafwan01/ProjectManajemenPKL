<?php

namespace App\Livewire\User;

use App\Models\log_book;
use App\Models\presensi as PresensiModel;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Carbon\Carbon;

class Presensi extends Component
{
    public $tipePresensi = 'masuk';
    public $logbook = '';
    public $latitude = null;
    public $longitude = null;
    public $fotoCaptured = null;

    // Flag status presensi hari ini
    public $sudahAbsenMasuk = false;
    public $sudahAbsenKeluar = false;

    private $targetLat = -6.595181;
    private $targetLng = 106.793836;
    private $maxRadiusMeters = 50000000; // Mode WFA 50.000 KM

    protected $messages = [
        'latitude.required'     => 'Koordinat lokasi belum terdeteksi. Izinkan akses lokasi di browser!',
        'longitude.required'    => 'Koordinat lokasi belum terdeteksi.',
        'logbook.required'      => 'Logbook harian wajib diisi saat presensi pulang!',
        'logbook.min'           => 'Isi logbook minimal 10 karakter.',
        'fotoCaptured.required' => 'Foto wajib diambil sebelum mengirim presensi!',
    ];

    public function mount()
    {
        $this->cekStatusPresensi();
    }

    private function currentUser()
    {
        return Auth::user() ?? User::where('role', UserRole::PKL)->first();
    }

    private function currentUserId()
    {
        return $this->currentUser()?->user_id;
    }

    public function setTipePresensi($tipe)
    {
        $this->tipePresensi = $tipe;
        $this->resetErrorBag();
    }

    private function cekStatusPresensi()
    {
        $presensiHariIni = $this->getPresensiHariIni();

        if ($presensiHariIni) {
            $this->sudahAbsenMasuk = !is_null($presensiHariIni->absen_masuk);
            $this->sudahAbsenKeluar = !is_null($presensiHariIni->absen_keluar);

            if ($this->sudahAbsenMasuk && !$this->sudahAbsenKeluar) {
                $this->tipePresensi = 'pulang';
            }
        }
    }

    private function getPresensiHariIni()
    {
        return PresensiModel::where('user_id', $this->currentUserId())
            ->whereDate('tanggal', Carbon::today()->toDateString())
            ->first();
    }

    public function simpanPresensi()
    {
        $presensiHariIni = $this->getPresensiHariIni();

        // 1. Cek Validasi Logis
        if ($this->tipePresensi === 'pulang') {
            if (!$presensiHariIni || !$presensiHariIni->absen_masuk) {
                session()->flash('warning', 'Anda belum melakukan presensi MASUK hari ini!');
                return;
            }
            if ($presensiHariIni->absen_keluar) {
                session()->flash('warning', 'Anda sudah melakukan presensi PULANG hari ini!');
                return;
            }
        }

        // 2. Validasi Form
        $rules = [
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'fotoCaptured' => 'required',
        ];

        if ($this->tipePresensi === 'pulang') {
            $rules['logbook'] = 'required|min:10';
        }

        $this->validate($rules);

        // 3. User & File Setup
        $user = $this->currentUser();
        if (!$user) {
            session()->flash('warning', 'User tidak ditemukan.');
            return;
        }

        $namaUserSlug = Str::slug($user->nama ?? 'user');
        $tanggalHariIni = Carbon::today()->format('Y-m-d');
        $tipe = $this->tipePresensi;

        $namaFile = "{$namaUserSlug}-{$tanggalHariIni}-{$tipe}.jpg";
        $fotoPath = $this->simpanFotoBase64($this->fotoCaptured, $namaFile);

        // 4. Eksekusi Simpan dengan Database Transaction & Error Catching
        DB::beginTransaction();
        try {
            if ($this->tipePresensi === 'masuk') {
                
                if ($presensiHariIni && $presensiHariIni->absen_masuk) {
                    session()->flash('warning', 'Anda sudah melakukan presensi MASUK hari ini!');
                    return;
                }

                PresensiModel::create([
                    'user_id'          => $user->user_id,
                    'tanggal'          => $tanggalHariIni,
                    'absen_masuk'      => now()->format('H:i:s'),
                    'foto_masuk'       => $fotoPath,
                    'status_kehadiran' => 'hadir',
                    'latitude'         => $this->latitude,
                    'longitude'        => $this->longitude,
                ]);

                session()->flash('message', 'Presensi MASUK berhasil dikirim!');

            } else {

                // Dapatkan Primary Key dari Record Presensi
                $pkName = $presensiHariIni->getKeyName(); // Mendapatkan 'presensi_id' atau 'id'
                $presensiId = $presensiHariIni->{$pkName};

                // Update jam & foto keluar pada tabel presensi
                $presensiHariIni->update([
                    'absen_keluar' => now()->format('H:i:s'),
                    'foto_keluar'  => $fotoPath,
                ]);

                // Simpan ke tabel log_books
                log_book::create([
                    'presensi_id' => $presensiId,
                    'user_id'     => $user->user_id,
                    'kegiatan'    => $this->logbook,
                ]);

                session()->flash('message', 'Presensi PULANG dan Logbook berhasil dikirim!');
            }

            DB::commit();

            // Reset Form & Refesh State
            $this->reset(['logbook', 'fotoCaptured']);
            $this->cekStatusPresensi();

        } catch (\Exception $e) {
            DB::rollBack();
            // Menampilkan error spesifik dari Database jika ada kegagalan query
            session()->flash('warning', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function simpanFotoBase64($base64Image, $namaFile)
    {
        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        $imageData = base64_decode($imageData);

        $path = 'presensi/' . $namaFile;
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }

    public function render()
    {
        return view('livewire.user.presensi')
            ->layout('layouts.user');
    }
}