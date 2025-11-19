<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RenderDashboardAsUser extends Command
{
    protected $signature = 'render:dashboard {email}';
    protected $description = 'Renderiza la vista dashboard como el usuario dado para verificar errores';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error('Usuario no encontrado');
            return 1;
        }

        Auth::login($user);

        try {
            $html = view('dashboard')->render();
            $this->info('Dashboard renderizado correctamente para: ' . $email);
            return 0;
        } catch (\Throwable $e) {
            $this->error('Error al renderizar dashboard: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}
