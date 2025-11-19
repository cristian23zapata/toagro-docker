<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Route;

class CheckRouteCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:check-coordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check route coordinates in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking route coordinates...');
        
        $routes = Route::all();
        
        if ($routes->isEmpty()) {
            $this->warn('No routes found in the database.');
            return;
        }
        
        foreach ($routes as $route) {
            $this->line("Route ID: {$route->id}");
            $this->line("Origin: {$route->origin}");
            $this->line("Destination: {$route->destination}");
            $this->line("Origin Latitude: " . ($route->origin_latitude ?? 'NULL'));
            $this->line("Origin Longitude: " . ($route->origin_longitude ?? 'NULL'));
            $this->line("Destination Latitude: " . ($route->destination_latitude ?? 'NULL'));
            $this->line("Destination Longitude: " . ($route->destination_longitude ?? 'NULL'));
            $this->line('---');
        }
    }
}