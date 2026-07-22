<?php
    
namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('layouts.dashboard')]
    public function render()
    {
        return view('livewire.dashboard.dashboard');
    }
}
