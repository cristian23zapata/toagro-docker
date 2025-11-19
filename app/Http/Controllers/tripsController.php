<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripLocation;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Route;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
// Use the correct namespace for Google Maps service. The service class lives
// in App\Services and is named GoogleMapsService (plural). Importing from
// the wrong namespace (e.g., App\Http\Services\GoogleMapService) will
// result in a "Class not found" error. Ensure the correct import here.
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class tripsController extends Controller
{
    //mostrar los viajes en la bd
    public function index(Request $request)
    {
        $query = $request->get('q');
        $status = $request->get('status');
        
        $trips = Trip::with(['vehicle', 'driver.user', 'route']);

        // Aplicar filtro de búsqueda
        if ($query) {
            $trips->where(function($q) use ($query) {
                $q->whereHas('vehicle', function($sq) use ($query) {
                    $sq->where('plate_number', 'like', "%{$query}%");
                })
                ->orWhereHas('driver.user', function($sq) use ($query) {
                    $sq->where('name', 'like', "%{$query}%");
                })
                ->orWhereHas('route', function($sq) use ($query) {
                    $sq->where('name', 'like', "%{$query}%")
                       ->orWhere('description', 'like', "%{$query}%");
                });
            });
        }

        // Aplicar filtro de estado
        if ($status) {
            $trips->where('status', $status);
        }

        $trips = $trips->orderBy('created_at', 'desc')->paginate(10);
        return view('trips.index', compact('trips'));
    }

    //agregar un nuevo viaje / insertar
    public function store(StoreTripRequest $request)
    {
        try {
            // Log the request data for debugging
            Log::info('Trip creation attempt', ['data' => $request->all()]);

            $trip = Trip::create($request->validated());

            Log::info('Trip created successfully', ['trip_id' => $trip->id]);

            return redirect()->route('trips.index')
                ->with('success', 'Viaje creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error creating trip', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return redirect()->route('trips.create')
                ->with('error', 'Error al crear el viaje: ' . $e->getMessage())
                ->withInput();
        }
    }


    //Actualizar / editar viaje
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $trip->update($request->validated());
        return redirect()->route('trips.index')
            ->with('success', 'trip updated successfully');
    }

    //mostrar formulario de creación de viaje
    public function create(): View
    {
        $vehicles = Vehicle::active()->orderBy('plate_number')->get();
        $drivers = Driver::active()->with('user')->orderBy('created_at')->get();
        $routes = Route::orderBy('origin')->get();
        $statuses = Trip::getStatuses();

        return view('trips.create', compact('vehicles', 'drivers', 'routes', 'statuses'));
    }

    //Mostrar un viaje
    public function show(Trip $trip): View
    {
        // Verificar si el usuario es chofer y si el viaje le pertenece
        $user = request()->user();
        if ($user && $user->isDriver()) {
            $driver = $user->driver;
            // Si el chofer no es el asignado a este viaje, mostrar error
            if ($driver && $trip->driver_id != $driver->id) {
                abort(403, 'No tienes permiso para ver este viaje.');
            }
        }

        $trip->load([
            'vehicle',
            'driver.user',
            'route',
            'deliveries',
            'incidents',
            'locations'
        ]);

        // Debug: Log route coordinates
        if ($trip->route) {
            Log::info('Route coordinates', [
                'route_id' => $trip->route->id,
                'origin_latitude' => $trip->route->origin_latitude,
                'origin_longitude' => $trip->route->origin_longitude,
                'destination_latitude' => $trip->route->destination_latitude,
                'destination_longitude' => $trip->route->destination_longitude,
            ]);
        }

        return view('trips.show', compact('trip'));
    }

    //abrir formulario de editar viaje
    public function edit(Trip $trip): View
    {
        $vehicles = Vehicle::where('status', 'activo')
            ->orWhere('id', $trip->vehicle_id)
            ->orderBy('plate_number')
            ->get();

        $drivers = Driver::with('user')
            ->where('status', 'activo')
            ->orWhere('id', $trip->driver_id)
            ->orderBy('created_at')
            ->get();

        $routes = Route::orderBy('origin')->get();
        $statuses = Trip::getStatuses();

        return view('trips.edit', compact('trip', 'vehicles', 'drivers', 'routes', 'statuses'));
    }

    //eliminar viaje
    public function destroy(Trip $trip): RedirectResponse
    {
        // Solo permitir eliminar viajes pendientes o cancelados
        if (!$trip->canBeDeleted()) {
            return redirect()->route('trips.index')
                ->with('error', 'No se puede eliminar un viaje en progreso o completado.');
        }

        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Viaje eliminado exitosamente.');
    }

    //cambiar estado del viaje
    public function updateStatus(Request $request, Trip $trip): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pendiente', 'en_progreso', 'completado', 'cancelado'])],
        ]);

        if ($trip->changeStatus($validated['status'])) {
            return redirect()->route('trips.index')
                ->with('success', "Estado del viaje cambiado a: {$validated['status']}");
        }

        return redirect()->back()
            ->with('error', 'No se pudo cambiar el estado del viaje.');
    }

    // iniciar viaje
    public function startTrip(Trip $trip): RedirectResponse
    {
        if ($trip->start()) {
            return redirect()->route('trips.show', $trip)
                ->with('success', 'Viaje iniciado exitosamente.');
        }

        return redirect()->route('trips.index')
            ->with('error', 'Solo se pueden iniciar viajes pendientes.');
    }

    // finalizar viaje
    public function completeTrip(Trip $trip): RedirectResponse
    {
        if ($trip->complete()) {
            return redirect()->route('trips.show', $trip)
                ->with('success', 'Viaje completado exitosamente.');
        }

        return redirect()->route('trips.index')
            ->with('error', 'Solo se pueden completar viajes en progreso.');
    }

    // Obtener viajes activos para dashboard
    public function getActive()
    {
        $trips = Trip::active()
            ->withAllRelations()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($trip) {
                return $trip->getSummaryInfo();
            });

        return response()->json($trips);
    }

    // Buscar viajes por vehículo o conductor
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->whereHas('vehicle', function ($q) use ($query) {
                $q->where('plate_number', 'like', "%{$query}%");
            })
            ->orWhereHas('driver.user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($trip) {
                return $trip->getSummaryInfo();
            });

        return response()->json($trips);
    }

    // Cancelar viaje
    public function cancelTrip(Trip $trip): RedirectResponse
    {
        if ($trip->cancel()) {
            return redirect()->route('trips.show', $trip)
                ->with('success', 'Viaje cancelado exitosamente.');
        }

        return redirect()->route('trips.index')
            ->with('error', 'No se pudo cancelar el viaje.');
    }

    // Método para obtener la ubicación actual de un viaje (para integración con GPS)
    public function getCurrentLocation(Trip $trip)
    {
        // En una implementación real, aquí se obtendría la ubicación del dispositivo GPS
        // Por ahora devolvemos datos de ejemplo
        
        // Si hay ubicaciones registradas, devolvemos la última
        $lastLocation = $trip->getLastLocation();
        
        if ($lastLocation) {
            // Calcular progreso aproximado
            $originLat = $trip->route->origin_latitude;
            $originLng = $trip->route->origin_longitude;
            $destLat = $trip->route->destination_latitude;
            $destLng = $trip->route->destination_longitude;
            
            // Calcular distancia total y distancia recorrida
            $totalDistance = $this->calculateDistance($originLat, $originLng, $destLat, $destLng);
            $traveledDistance = $this->calculateDistance($originLat, $originLng, $lastLocation->latitude, $lastLocation->longitude);
            $progress = ($totalDistance > 0) ? min(1, $traveledDistance / $totalDistance) : 0;
            
            return response()->json([
                'trip_id' => $trip->id,
                'latitude' => $lastLocation->latitude,
                'longitude' => $lastLocation->longitude,
                'progress' => $progress,
                'timestamp' => $lastLocation->recorded_at->toISOString(),
                'status' => $trip->status
            ]);
        }
        
        // Si no hay ubicaciones registradas, devolvemos la ubicación de origen
        return response()->json([
            'trip_id' => $trip->id,
            'latitude' => $trip->route->origin_latitude ?? 0,
            'longitude' => $trip->route->origin_longitude ?? 0,
            'progress' => 0,
            'timestamp' => now()->toISOString(),
            'status' => $trip->status
        ]);
    }

    // Método para almacenar la ubicación de un viaje (para integración con GPS)
    public function storeLocation(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = new TripLocation([
            'trip_id' => $trip->id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'recorded_at' => now(),
        ]);

        $location->save();

        return response()->json([
            'success' => true,
            'message' => 'Ubicación registrada exitosamente',
            'location' => $location
        ]);
    }

    // Método para obtener el historial de ubicaciones de un viaje
    public function getLocationHistory(Trip $trip)
    {
        $locations = $trip->locations()->orderBy('recorded_at', 'asc')->get();
        
        return response()->json([
            'trip_id' => $trip->id,
            'locations' => $locations
        ]);
    }

    // Mostrar viajes asignados al chofer autenticado
    public function driverIndex(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->isDriver()) {
            abort(403);
        }

        $driver = $user->driver;
        if (! $driver) {
            return view('trips.index', ['trips' => collect()]);
        }

        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->where('driver_id', $driver->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('trips.index', compact('trips'));
    }

    // Mostrar viajes donde el usuario es cliente receptor
    public function clientIndex(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        // Some deployments don't have customer_email/customer_id on deliveries table.
        // Make the query defensive: prefer matching by email/id if columns exist,
        // otherwise fall back to matching by customer_name containing the user's name or email.
        try {
            $hasEmailColumn = \Schema::hasColumn('deliveries', 'customer_email');
            $hasCustomerId = \Schema::hasColumn('deliveries', 'customer_id');

            $tripsQuery = Trip::with(['vehicle', 'driver.user', 'route', 'deliveries'])
                ->whereHas('deliveries', function ($q) use ($user, $hasEmailColumn, $hasCustomerId) {
                    if ($hasEmailColumn || $hasCustomerId) {
                        if ($hasEmailColumn) {
                            $q->where('customer_email', $user->email);
                        }

                        if ($hasCustomerId) {
                            $q->orWhere('customer_id', $user->id);
                        }
                    } else {
                        // Fallback: match deliveries where customer_name contains user's email or name
                        $q->where('customer_name', 'like', "%{$user->email}%")
                          ->orWhere('customer_name', 'like', "%{$user->name}%");
                    }
                });

            $trips = $tripsQuery->orderBy('created_at', 'desc')->paginate(10);
        } catch (\Exception $e) {
            // On any unexpected DB error, log and return an empty paginated collection to avoid 500.
            \Log::error('Error in clientIndex filtering deliveries: ' . $e->getMessage());
            $trips = collect();
        }

        return view('trips.index', compact('trips'));
    }
    
    // Función auxiliar para calcular distancia entre dos puntos (fórmula haversine)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radio de la Tierra en kilómetros
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c; // Distancia en kilómetros
    }
}