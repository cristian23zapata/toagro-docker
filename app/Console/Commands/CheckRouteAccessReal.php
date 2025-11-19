<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckRouteAccessReal extends Command
{
    protected $signature = 'check:route-access';
    protected $description = 'Verifica acceso real (HTTP) a rutas importantes para cada rol';

    protected $routes = [
        'home' => '/',
        'dashboard' => '/dashboard',
        'drivers' => '/drivers',
        'vehicles' => '/vehicles',
        'routes' => '/routes',
        'trips' => '/trips',
        'deliveries' => '/deliveries',
        'incidents' => '/incidents',
        'client_trips' => '/client/trips',
        'driver_trips' => '/driver/trips',
    ];

    public function handle()
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $user = User::where('role_id', $role->id)->first();
            if (! $user) {
                $this->warn("No hay usuario para rol {$role->name}, saltando...");
                continue;
            }

            Auth::login($user);

            $this->info("\nProbando como: {$role->name} ({$user->email})");
            $rows = [];
            foreach ($this->routes as $label => $path) {
                try {
                    $request = Request::create($path, 'GET');
                    // preservar sesión/usuario autenticado
                    $response = app()->handle($request);
                    $code = $response->getStatusCode();
                } catch (\Throwable $e) {
                    $code = 'ERR';
                }
                $rows[] = [$label, $path, $code];
            }
            $this->table(['Ruta','Path','HTTP Code'], $rows);

            // logout
            Auth::logout();
        }

        return 0;
    }
}
