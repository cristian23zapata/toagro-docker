<?php

namespace App\View\Components;

use Illuminate\View\Component;

class HasRole extends Component
{
    public function __construct(public array|string $roles)
    {
        $this->roles = is_array($roles) ? $roles : [$roles];
    }

    public function render()
    {
        return function (array $data) {
            $allowed = auth()->check() && collect($this->roles)->contains(function ($role) {
                return auth()->user()->hasRole($role);
            });

            if (! $allowed) {
                return '';
            }

            // Obtener slot de forma segura: puede ser string o ComponentSlot
            $slot = $data['slot'] ?? '';
            if (is_object($slot) && method_exists($slot, 'toHtml')) {
                return $slot->toHtml();
            }

            return (string) $slot;
        };
    }
}