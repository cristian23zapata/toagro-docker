<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Route;

class RouteCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define coordinates for major Colombian cities
        // Note: Using more accurate coordinates for Chocó (Quibdó)
        $cityCoordinates = [
            'Medellin' => ['latitude' => 6.244203, 'longitude' => -75.581211],
            'Bogota' => ['latitude' => 4.609710, 'longitude' => -74.081750],
            'Cali' => ['latitude' => 3.437220, 'longitude' => -76.522500],
            'Pereira' => ['latitude' => 4.813320, 'longitude' => -75.694420],
            'Cartago' => ['latitude' => 4.746030, 'longitude' => -75.913750],
            // Updated Chocó coordinates to Quibdó (capital of Chocó department)
            'Chocó' => ['latitude' => 5.694200, 'longitude' => -76.661100],
            'La tebaida' => ['latitude' => 4.444440, 'longitude' => -75.817340],
            // Additional cities for better coverage
            'Quibdó' => ['latitude' => 5.694200, 'longitude' => -76.661100], // Capital of Chocó
            'Manizales' => ['latitude' => 5.068780, 'longitude' => -75.517320],
            'Armenia' => ['latitude' => 4.533890, 'longitude' => -75.681110],
            'Ibagué' => ['latitude' => 4.438890, 'longitude' => -75.242220],
            'Neiva' => ['latitude' => 2.927300, 'longitude' => -75.281930],
            'Pasto' => ['latitude' => 1.213670, 'longitude' => -77.281110],
            'Popayán' => ['latitude' => 2.438230, 'longitude' => -76.613160],
            'Tulua' => ['latitude' => 4.078670, 'longitude' => -76.196110],
            'Buenaventura' => ['latitude' => 3.880100, 'longitude' => -77.048600],
        ];

        // Update all routes with coordinates
        Route::all()->each(function ($route) use ($cityCoordinates) {
            // Get origin coordinates
            $originCoords = $cityCoordinates[$route->origin] ?? 
                ['latitude' => 6.244203, 'longitude' => -75.581211]; // Default to Medellin
                
            // Get destination coordinates
            $destCoords = $cityCoordinates[$route->destination] ?? 
                ['latitude' => 4.609710, 'longitude' => -74.081750]; // Default to Bogota
                
            // Update the route with coordinates
            $route->update([
                'origin_latitude' => $originCoords['latitude'],
                'origin_longitude' => $originCoords['longitude'],
                'destination_latitude' => $destCoords['latitude'],
                'destination_longitude' => $destCoords['longitude'],
            ]);
        });
    }
}