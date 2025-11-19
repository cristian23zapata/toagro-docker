<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRouteRequest;
use App\Http\Requests\UpdateRouteRequest;
use App\Models\Route;
use App\Models\PredefinedLocation;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class routesController extends Controller
{
    //mostrar las rutas en la bd
    public function index(Request $request): View
    {
        $query = $request->get('q');
        $status = $request->get('status');
        
        $routes = Route::query();

        // Aplicar filtro de búsqueda
        if ($query) {
            $routes->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('start_location', 'like', "%{$query}%")
                  ->orWhere('end_location', 'like', "%{$query}%");
            });
        }

        // Aplicar filtro de estado
        if ($status) {
            $routes->where('status', $status);
        }

        $routes = $routes->orderBy('created_at', 'desc')->paginate(10);

        return view('routes.index', compact('routes'));
    }

    //mostrar formulario de creación de ruta
    public function create(): View
    {
        $locations = PredefinedLocation::orderBy('name')->get();    
        return view('routes.create', compact('locations'));
    }

    //agregar una nueva ruta / insertar
    public function store(StoreRouteRequest $request)
    {
        Route::create($request->validated());
        return redirect()->route('routes.index')
            ->with('success', 'route created successfully');
    }

    //Actualizar / editar ruta
    public function update(UpdateRouteRequest $request, Route $route)
    {
        $route->update($request->validated());
        return redirect()->route('routes.index')
            ->with('success', 'route updated successfully');
    }

    //Mostrar una ruta con funcionalidad de mapa
    public function show(Route $route): View
    {
        $route->load('trips.vehicle', 'trips.driver.user');

        // Obtener datos del mapa si las coordenadas están disponibles
        $mapData = null;
        if (
            $route->origin_latitude && $route->origin_longitude &&
            $route->destination_latitude && $route->destination_longitude
        ) {

            $googleMapsService = new GoogleMapsService();
            $mapData = $googleMapsService->getDirections(
                "{$route->origin_latitude},{$route->origin_longitude}",
                "{$route->destination_latitude},{$route->destination_longitude}"
            );
            
            // If directions failed, try to find nearest roads
            if (!$mapData) {
                // Try to snap origin to nearest road
                $snappedOrigin = $googleMapsService->findNearestRoad(
                    $route->origin_latitude, 
                    $route->origin_longitude
                );
                
                // Try to snap destination to nearest road
                $snappedDestination = $googleMapsService->findNearestRoad(
                    $route->destination_latitude, 
                    $route->destination_longitude
                );
                
                // If we found snapped points, try directions again
                if ($snappedOrigin && $snappedDestination) {
                    $mapData = $googleMapsService->getDirections(
                        "{$snappedOrigin['latitude']},{$snappedOrigin['longitude']}",
                        "{$snappedDestination['latitude']},{$snappedDestination['longitude']}"
                    );
                }
            }
        }

        return view('routes.show', compact('route', 'mapData'));
    }

    //abrir formulario de editar ruta
    public function edit(Route $route): View
    {
        $locations = PredefinedLocation::orderBy('name')->get();
        return view('routes.edit', compact('route', 'locations'));
    }

    //eliminar ruta
    public function destroy(Route $route): RedirectResponse
    {
        // Verificar si tiene viajes activos
        if (!$route->canBeDeleted()) {
            return redirect()->route('routes.index')
                ->with('error', 'No se puede eliminar una ruta con viajes activos.');
        }

        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Ruta eliminada exitosamente.');
    }

    // Método para obtener ubicaciones predefinidas
    public function getPredefinedLocations(Request $request)
    {
        $search = $request->get('search');
        $locations = PredefinedLocation::where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->get();

        return response()->json($locations);
    }

    // Método para obtener datos de ubicación
    public function getLocationData(Request $request)
    {
        $locationId = $request->get('location_id');
        $location = PredefinedLocation::find($locationId);

        if ($location) {
            return response()->json([
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'address' => $location->address,
            ]);
        }

        return response()->json(['error' => 'Location not found'], 404);
    }

    // Activar ruta
    public function activate(Route $route): RedirectResponse
    {
        $route->activate();
        return redirect()->route('routes.index')
            ->with('success', 'Ruta activada exitosamente.');
    }

    // Suspender ruta
    public function suspend(Route $route): RedirectResponse
    {
        // Verificar si tiene viajes activos
        if ($route->hasActiveTrips()) {
            return redirect()->route('routes.index')
                ->with('error', 'No se puede suspender una ruta con viajes activos.');
        }

        $route->suspend();
        return redirect()->route('routes.index')
            ->with('success', 'Ruta suspendida exitosamente.');
    }

    // Duplicar ruta
    public function duplicate(Route $route): RedirectResponse
    {
        $newRoute = $route->replicate();
        $newRoute->status = 'active';
        $newRoute->save();

        return redirect()->route('routes.index')
            ->with('success', 'Ruta duplicada exitosamente.');
    }
}