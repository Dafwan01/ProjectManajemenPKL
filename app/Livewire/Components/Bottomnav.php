<?php

namespace App\Livewire\Components;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Bottomnav extends Component
{
    #[Layout('layouts.home')]
    public function render()
    {
        return view('livewire.components.bottomnav');
    }
}
