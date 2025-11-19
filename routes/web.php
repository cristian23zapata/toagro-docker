<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\driversController;
use App\Http\Controllers\vehiclesController;
use App\Http\Controllers\routesController;
use App\Http\Controllers\tripsController;
use App\Http\Controllers\deliveriesController;
use App\Http\Controllers\incidentsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Drivers routes
    // Use the "role" middleware alias instead of concatenating the class name. This ensures
    // that parameters are parsed correctly and avoids 403 errors when admin users attempt
    // to access a route. For example, admin and gestor users can manage drivers.
    Route::resource('drivers', driversController::class)
        ->middleware('role:admin,gestor');

    // Vehicles routes
    // Restrict access to admin and gestor users for managing vehicles.
    Route::resource('vehicles', vehiclesController::class)
        ->middleware('role:admin,gestor');

    // Routes routes
    Route::resource('routes', routesController::class)
        ->except(['show'])
        ->middleware('role:admin,gestor');
    Route::get('/routes/{route}', [routesController::class, 'show'])
        ->name('routes.show');
    Route::post('/routes/{route}/suspend', [routesController::class, 'suspend'])->name('routes.suspend');
    Route::post('/routes/{route}/activate', [routesController::class, 'activate'])->name('routes.activate');
    Route::post('/routes/{route}/duplicate', [routesController::class, 'duplicate'])->name('routes.duplicate');

    // Additional routes for predefined locations
    Route::get('/routes/predefined-locations', [routesController::class, 'getPredefinedLocations'])
        ->name('routes.predefined-locations');
    Route::get('/routes/location-data', [routesController::class, 'getLocationData'])
        ->name('routes.location-data');

    // Trips routes
    // Providers (gestor) can create and manage trips; admin can manage all; drivers and clients can view limited info
    Route::resource('trips', tripsController::class)
        ->except(['show'])
        ->middleware('role:admin,gestor');
    // Allow drivers, admins and gestores to view individual trips
    Route::get('/trips/{trip}', [tripsController::class, 'show'])
        ->name('trips.show')
        ->middleware('role:admin,gestor,chofer');
    // Driver routes to view assigned trips (admin should also be able to view)
    Route::get('/driver/trips', [tripsController::class, 'driverIndex'])
        ->name('driver.trips')
        ->middleware('role:chofer,admin');
    // Client routes to view received trips (admin should also be able to view)
    Route::get('/client/trips', [tripsController::class, 'clientIndex'])
        ->name('client.trips')
        ->middleware('role:cliente,admin');
    Route::post('/trips/{trip}/start', [tripsController::class, 'startTrip'])->name('trips.start');
    Route::post('/trips/{trip}/complete', [tripsController::class, 'completeTrip'])->name('trips.complete');
    Route::post('/trips/{trip}/cancel', [tripsController::class, 'cancelTrip'])->name('trips.cancel');
    Route::get('/trips/{trip}/current-location', [tripsController::class, 'getCurrentLocation'])->name('trips.current-location');
    Route::post('/trips/{trip}/store-location', [tripsController::class, 'storeLocation'])->name('trips.store-location');
    Route::get('/trips/{trip}/location-history', [tripsController::class, 'getLocationHistory'])->name('trips.location-history');
    
    // Demo routes
    Route::get('/trips/map-demo', function () {
        return view('trips.map-demo');
    })->name('trips.map-demo');
    
    Route::get('/trips/route-demo', function () {
        return view('trips.route-demo');
    })->name('trips.route-demo');
    
    Route::get('/trips/map-test', function () {
        return view('trips.map-test');
    })->name('trips.map-test');

    // Deliveries routes
    Route::resource('deliveries', deliveriesController::class)
        ->middleware('role:admin,gestor');
    // Client can view their deliveries
    Route::get('/client/deliveries', [deliveriesController::class, 'clientIndex'])
        ->name('client.deliveries')
        ->middleware('role:cliente');
    Route::post('/deliveries/{delivery}/mark-as-delivered', [deliveriesController::class, 'markAsDelivered'])
        ->name('deliveries.mark-as-delivered');
    Route::post('/deliveries/{delivery}/mark-as-failed', [deliveriesController::class, 'markAsFailed'])
        ->name('deliveries.mark-as-failed');

    // Incidents routes (admin and gestor)
    Route::resource('incidents', incidentsController::class)
        ->middleware('role:admin,gestor');
});

require __DIR__ . '/auth.php';