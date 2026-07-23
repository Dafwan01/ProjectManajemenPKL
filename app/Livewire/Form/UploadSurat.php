<?php

namespace App\Livewire\Form;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class uploadSurat extends Component
{
    use WithFileUploads;

    public $userId;
    public ?User $user = null;
    public $file = null;

    protected $rules = [
        'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ];

    protected $messages = [
        'file.required' => 'Silakan pilih file terlebih dahulu.',
        'file.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
        'file.max' => 'Ukuran file maksimal 5MB.',
    ];

    public function mount($userId = null)
    {
        $this->userId = $userId;
        $this->user = User::find($userId);
    }

    public function simpan()
    {
        $this->validate();

        // Hapus file lama kalau ada, biar tidak numpuk sampah
        if ($this->user->surat_penerimaan && Storage::disk('public')->exists($this->user->surat_penerimaan)) {
            Storage::disk('public')->delete($this->user->surat_penerimaan);
        }

        $extension = $this->file->getClientOriginalExtension();
        $namaFile = Str::slug($this->user->nama) . '-suratpenerimaanmagang.' . $extension;

        $path = $this->file->storeAs('suratpenerimaanmagang', $namaFile, 'public');

        $this->user->update([
            'surat_penerimaan' => $path,
        ]);

        session()->flash('message', 'Surat penerimaan magang berhasil diupload!');
        $this->dispatch('close-upload-modal');
    }

    public function tutup()
    {
        $this->dispatch('close-upload-modal');
    }

    public function render()
    {
        return view('livewire.form.upload-surat');
    }
}