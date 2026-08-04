<?php

namespace App\Traits;

trait Toastable
{
    public function toast(string $message, string $type = 'success'): void
    {
        if (method_exists($this, 'dispatch')) {
            $this->dispatch('show-toast', [
                'message' => $message,
                'type' => $type,
            ]);
        }

        $flashKey = $type === 'success' ? 'message' : $type;
        session()->flash($flashKey, $message);
    }

    public function toastSuccess(string $message): void
    {
        $this->toast($message, 'success');
    }

    public function toastError(string $message): void
    {
        $this->toast($message, 'error');
    }

    public function toastWarning(string $message): void
    {
        $this->toast($message, 'warning');
    }

    public function toastInfo(string $message): void
    {
        $this->toast($message, 'info');
    }
}
