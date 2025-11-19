<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class CheckRoleAccess extends Command
{
    protected $signature = 'check:role-access';
    protected $description = 'Verifica el acceso de cada rol a las diferentes rutas';

    protected $routes = [
        'dashboard' => '/',
        'drivers' => '/drivers',
        'vehicles' => '/vehicles',
        'routes' => '/routes',
        'trips' => '/trips',
        'deliveries' => '/deliveries',
        'incidents' => '/incidents'
    ];

    public function handle()
    {
        $roles = Role::all();
        
        $this->info("\nVerificando accesos por rol:");
        
        foreach ($roles as $role) {
            // Crear o usar un usuario de prueba para cada rol
            $user = User::where('role_id', $role->id)->first();
            if (!$user) {
                $user = User::factory()->create([
                    'role_id' => $role->id,
                    'email_verified_at' => now()
                ]);
            }
            
            Auth::login($user);
            
            $this->info("\nProbando acceso para rol: " . strtoupper($role->name));
            $this->info(str_repeat('-', 40));
            $this->table(
                ['Ruta', 'Debería tener acceso', 'HTTP Code'],
                $this->checkAccess($role->name)
            );
        }
        
        return Command::SUCCESS;
    }

    protected function checkAccess($role)
    {
        $results = [];
        
        foreach ($this->routes as $name => $path) {
            $shouldHaveAccess = $this->shouldHaveAccess($role, $name);
            $response = $this->get($path);
            $results[] = [
                $name,
                $shouldHaveAccess ? 'Sí' : 'No',
                $response
            ];
        }
        
        return $results;
    }

    protected function shouldHaveAccess($role, $route)
    {
        $accessMatrix = [
            'admin' => ['dashboard', 'drivers', 'vehicles', 'routes', 'trips', 'deliveries', 'incidents'],
            'manager' => ['dashboard', 'drivers', 'vehicles', 'routes', 'trips', 'deliveries', 'incidents'],
            'driver' => ['dashboard', 'trips'],
            'client' => ['dashboard', 'trips', 'deliveries'],
        ];
        
        return in_array($route, $accessMatrix[$role] ?? []);
    }

    protected function get($path)
    {
        $ch = curl_init('http://localhost:8000' . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, 'laravel_session=' . $_COOKIE['laravel_session'] ?? '');
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode;
    }
}