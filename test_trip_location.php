<?php

// Simple test script to demonstrate trip location tracking
// This would typically be part of a mobile app or vehicle tracking device

// In a real implementation, this would be a POST request from a GPS device
// For testing purposes, we'll simulate location updates

require_once 'vendor/autoload.php';

use App\Models\Trip;
use App\Models\TripLocation;

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get a trip (in a real app, this would come from the device)
$trip = Trip::first();

if ($trip) {
    // Simulate a location update
    $location = new TripLocation();
    $location->trip_id = $trip->id;
    $location->latitude = $trip->route->origin_latitude + (mt_rand(-100, 100) / 1000); // Small variation
    $location->longitude = $trip->route->origin_longitude + (mt_rand(-100, 100) / 1000); // Small variation
    $location->recorded_at = now();
    $location->save();
    
    echo "Location recorded for trip {$trip->id}\n";
    echo "Latitude: {$location->latitude}\n";
    echo "Longitude: {$location->longitude}\n";
} else {
    echo "No trips found\n";
}