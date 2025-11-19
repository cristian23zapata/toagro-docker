<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TripLocation;
use App\Models\Trip;

class TripLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all trips
        $trips = Trip::all();
        
        // Create 3-5 locations for each trip
        foreach ($trips as $trip) {
            if ($trip->route) {
                // Generate locations between origin and destination
                $originLat = $trip->route->origin_latitude;
                $originLng = $trip->route->origin_longitude;
                $destLat = $trip->route->destination_latitude;
                $destLng = $trip->route->destination_longitude;
                
                if ($originLat && $originLng && $destLat && $destLng) {
                    $locationCount = rand(3, 5);
                    
                    for ($i = 0; $i < $locationCount; $i++) {
                        // Generate intermediate points between origin and destination
                        $progress = $i / ($locationCount - 1);
                        $lat = $originLat + ($destLat - $originLat) * $progress;
                        $lng = $originLng + ($destLng - $originLng) * $progress;
                        
                        TripLocation::create([
                            'trip_id' => $trip->id,
                            'latitude' => $lat,
                            'longitude' => $lng,
                            'recorded_at' => now()->subMinutes(rand(0, 120)),
                        ]);
                    }
                }
            }
        }
    }
}