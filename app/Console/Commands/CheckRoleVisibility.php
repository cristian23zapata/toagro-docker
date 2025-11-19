<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckRoleVisibility extends Command
{
    protected $signature = 'check:visibility {role}';
    protected $description = 'Comprueba qué elementos del dashboard son visibles para un rol dado';

    public function handle()
    {
        $roleName = $this->argument('role');
        $role = Role::where('name', $roleName)->first();
        if (! $role) {
            $this->error("Rol '$roleName' no encontrado");
            return 1;
        }

        $user = User::where('role_id', $role->id)->first();
        if (! $user) {
            $this->error("No hay usuarios con el rol '$roleName' para probar");
            return 1;
        }

        Auth::login($user);

        try {
            $html = view('dashboard')->render();
        } catch (\Throwable $e) {
            $this->error('Error al renderizar dashboard: ' . $e->getMessage());
            return 1;
        }

        // Buscamos los títulos de las tarjetas en el HTML (texto en español)
        $items = [
            'Conductores' => 'Conductores',
            'Vehículos' => 'Vehículos',
            'Rutas' => 'Rutas',
            'Viajes' => 'Viajes',
            'Entregas' => 'Entregas',
            'Incidentes' => 'Incidentes',
        ];

        $rows = [];
        foreach ($items as $label => $needle) {
            $present = mb_stripos($html, $needle) !== false ? 'Sí' : 'No';
            $rows[] = [$label, $present];
        }

        $this->info("\nVisibilidad en dashboard para rol: $roleName (usuario: {$user->email})");
    $this->table(['Elemento','Visible'], $rows);

        // Expected policy (etiquetas en español)
        $policy = [
            'admin' => ['Conductores','Vehículos','Rutas','Viajes','Entregas','Incidentes'],
            'gestor' => ['Conductores','Vehículos','Rutas','Viajes','Entregas','Incidentes'],
            'chofer' => ['Viajes'],
            'cliente' => ['Viajes','Entregas'],
        ];

        $expected = $policy[$roleName] ?? [];
        $this->info("\nElementos esperados por política: " . implode(', ', $expected));

        // Compare
        $mismatches = [];
        foreach ($items as $label => $needle) {
            $present = mb_stripos($html, $needle) !== false;
            $should = in_array($label, $expected);
            if ($present && ! $should) {
                $mismatches[] = "$label está visible pero no debería";
            }
            if (! $present && $should) {
                $mismatches[] = "$label no está visible pero debería";
            }
        }

        if (empty($mismatches)) {
            $this->info('\nLa visibilidad coincide con la política esperada.');
        } else {
            $this->warn('\nDiferencias encontradas:');
            foreach ($mismatches as $m) {
                $this->line('- ' . $m);
            }
        }

        return 0;
    }
}

// Helpers because route() may throw if route name not defined
if (! function_exists('route_exists')) {
    function route_exists($name)
    {
        try {
            return \Illuminate\Support\Facades\Route::has($name);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
