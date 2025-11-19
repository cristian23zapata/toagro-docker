<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class vehiclesController extends Controller
{

    //mostrar los vehiculos en la bd
    public function index(Request $request): View
    {
        $query = $request->get('q');
        $status = $request->get('status');
        
        $vehicles = Vehicle::query();

        // Aplicar filtro de búsqueda
        if ($query) {
            $vehicles->where(function($q) use ($query) {
                $q->where('plate_number', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%");
            });
        }

        // Aplicar filtro de estado
        if ($status) {
            $vehicles->where('status', $status);
        }

        $vehicles = $vehicles->orderBy('created_at', 'desc')->paginate(10);
        
        return view('vehicles.index', compact('vehicles'));
    }

    //mostrar formulario de creacion de vehiculo
    public function create(): View
    {
        $statuses = Vehicle::getStatuses();
        return view('vehicles.create', compact('statuses'));
    }

    //agregar un nuevo vehiculo / insertar
    public function store(StoreVehicleRequest $request)
    {
        Vehicle::create($request->validated());
        return redirect()->route('vehicles.index')
                         ->with('success', 'Vehicle created successfully');
    }

    //Actualizar / editar vehiculo
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());
        return redirect()->route('vehicles.index')
                         ->with('success', 'Vehicle updated successfully');
    }

    //Mostrar un vehículo
    public function show(Vehicle $vehicle): View
    {
        return view('vehicles.show', compact('vehicle'));
    }

    //abrir formulario de editar vehiculo
    public function edit(Vehicle $vehicle): View
    {
        $statuses = Vehicle::getStatuses();
        return view('vehicles.edit', compact('vehicle', 'statuses'));
    }

    //eliminar vehiculo
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }

    //cambiar estdo del vehiculo
    public function toggleStatus(Vehicle $vehicle): RedirectResponse
    {
        $newStatus = $vehicle->toggleStatus();

        return redirect()->route('vehicles.index')
            ->with('success', "Estado del vehículo cambiado a: {$newStatus}");
    }

    // Obtener vehículos disponibles para AJAX/select2
    public function getAvailable()
    {
        $vehicles = Vehicle::available()
            ->orderBy('plate_number')
            ->get()
            ->map(function($vehicle) {
                return $vehicle->getSummaryInfo();
            });

        return response()->json($vehicles);
    }

    // Buscar vehículos por placa
    public function search(Request $request)
    {
        $query = $request->get('search', '');
        $status = $request->get('status');
        
        $vehicles = Vehicle::where(function($q) use ($query) {
            $q->where('plate_number', 'like', "%{$query}%")
              ->orWhere('brand', 'like', "%{$query}%")
              ->orWhere('model', 'like', "%{$query}%");
        });

        if ($status) {
            $vehicles->where('status', $status);
        }

        $vehicles = $vehicles->orderBy('plate_number')
            ->paginate(10)
            ->get()
            ->map(function($vehicle) {
                return $vehicle->getSummaryInfo();
            });

        return response()->json($vehicles);
    }
}
