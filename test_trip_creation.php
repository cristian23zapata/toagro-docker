<?php
require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

// Create a service container
$container = new Container();

// Create a database capsule
$capsule = new Capsule($container);
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'logistic_service',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setEventDispatcher(new Dispatcher($container));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Try to create a trip
try {
    $trip = new App\Models\Trip();
    $trip->vehicle_id = 1;
    $trip->driver_id = 1;
    $trip->route_id = 1;
    $trip->start_time = now();
    $trip->status = 'pendiente';
    $trip->save();

    echo "Trip created successfully!\n";
    echo "Trip ID: " . $trip->id . "\n";
} catch (Exception $e) {
    echo "Error creating trip: " . $e->getMessage() . "\n";
}
