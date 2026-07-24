<?php

namespace App\Livewire\Dashboard\UploadFIle;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class SuratPenerimaanMagang extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public $files = []; // menampung file per user_id sementara

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Dipanggil otomatis tiap kali salah satu input file di 'files' array berubah
    public function updatedFiles($value, $key)
    {
        $userId = $key;

        $this->validateOnly("files.$userId", [
            "files.$userId" => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [], [
            "files.$userId" => 'File',
        ]);

        $user = User::findOrFail($userId);
        $file = $this->files[$userId];

        if ($user->surat_penerimaan && Storage::disk('public')->exists($user->surat_penerimaan)) {
            Storage::disk('public')->delete($user->surat_penerimaan);
        }

        $extension = $file->getClientOriginalExtension();
        $namaFile = Str::slug($user->nama) . '-suratpenerimaanmagang.' . $extension;

        $path = $file->storeAs('suratpenerimaanmagang', $namaFile, 'public');

        $user->update([
            'surat_penerimaan' => $path,
        ]);

        unset($this->files[$userId]);

        session()->flash('message', 'Surat penerimaan magang untuk ' . $user->nama . ' berhasil diupload!');
    }

    public function render()
    {
        $users = User::query()
            ->where('role', UserRole::PKL->value)
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest('tanggal_mulai')
            ->paginate(10);

        return view('livewire.dashboard.upload-file.surat-penerimaan-magang', compact('users'));
    }
}