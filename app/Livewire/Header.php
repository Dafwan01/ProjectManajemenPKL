<?php

namespace App\Livewire;

use Livewire\Component;

class Header extends Component
{

    public function GoToMamnajemenPkl()
    {
        return redirect()->route('manajemen-pkl');
    }

    public function GoToManajemenAkun()
    {
        return redirect()->route('manajemen-akun');
    }

    public function GoToMonitoringAbsensi()
    {
        return redirect()->route('monitoring-absensi');
    }

    public function GoToDashboard()
    {
        return redirect()->route('dashboard');
    }

    public function GoToSuratPenerimaanMagang()
    {
        return redirect()->route('surat-penerimaan-magang');
    }

    public function GoToSertifikat()
    {
        return redirect()->route('sertifikat');
    }

    public function GoToNilai()
    {
        return redirect()->route('nilai');
    }   

    public function GoToPermohonanIzin()
    {
        return redirect()->route('permohonan-izin');
    }

    public function GoToRekap(){
        return redirect()->route('rekap-absensi');
    }

    public function render()
    {
        return view('livewire.components.header');
    }
}
