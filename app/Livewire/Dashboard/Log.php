<?php

namespace App\Livewire\Dashboard;

use App\Models\DetailJadwal;
use App\Models\file;
use App\Models\Forum;
use App\Models\ForumMessage;
use App\Models\Jadwal;
use App\Models\log_book;
use App\Models\Nilai;
use App\Models\PermohonanIzin;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use App\Models\presensi;

#[Layout('layouts.dashboard')]
class Log extends Component
{
    use WithPagination;

    public string $search = '';
    public string $eventFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEventFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = Activity::query()
            ->with([
                'causer' => fn ($q) => $q->withTrashed(),
                'subject' => function ($morphTo) {
                    $morphTo->morphWith([
                        User::class => [],
                        presensi::class => ['user'],
                             Jadwal::class => ['detailJadwals'],
                               DetailJadwal::class => ['user', 'jadwal'],
                         log_book::class => ['user'],
                             PermohonanIzin::class => ['user'],
                               Nilai::class => ['user'],
                                file::class => ['user'],
                                 Forum::class => ['user'],
                                 ForumMessage::class => ['user', 'forum'],
                    ]);
                },
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhereHasMorph('causer', ['App\Models\User'], function ($userQuery) {
                          $userQuery->where('nama', 'like', '%' . $this->search . '%')
                                    ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->eventFilter, function ($query) {
                $query->where('event', $this->eventFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.dashboard.log', compact('logs'));
    }
}