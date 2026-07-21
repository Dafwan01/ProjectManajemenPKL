<?php

namespace App\Livewire\Form;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Akun extends Component
{
     #[Layout('layouts.dashboard')]
    public function render()
    {
        return view('livewire.form.akun');
    }
}
