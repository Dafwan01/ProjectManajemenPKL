<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public function gotodashboard()
    {
        return redirect()->route('dashboard');
    }
    
    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.login');
    }
}
