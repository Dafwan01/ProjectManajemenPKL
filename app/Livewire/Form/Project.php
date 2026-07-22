<?php

namespace App\Livewire\Form;

use App\Models\project as ProjectModel;
use Livewire\Component;

class Project extends Component
{
    public $userId;
    public ?ProjectModel $project = null;

   public function mount($userId = null)
{
    $this->userId = $userId;
    $this->project = ProjectModel::where('user_id', $this->userId)
        ->latest('project_id')
        ->first();
}

    public function tutup()
    {
        $this->dispatch('close-project-modal');
    }

    public function render()
    {
        return view('livewire.form.project');
    }
}