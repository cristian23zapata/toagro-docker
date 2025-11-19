<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Incident;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class incidentsController extends Controller
{
    //mostrar los incidentes en la bd
    public function index(Request $request): View
    {
        $query = $request->get('q');
        $type = $request->get('type');
        $severity = $request->get('severity');
        $dateRange = $request->get('date_range');
        
        $incidents = Incident::with(['trip.vehicle', 'trip.driver.user', 'trip.route']);

        // Aplicar filtro de búsqueda
        if ($query) {
            $incidents->where(function($q) use ($query) {
                $q->where('description', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhereHas('trip.vehicle', function($sq) use ($query) {
                      $sq->where('plate_number', 'like', "%{$query}%");
                  })
                  ->orWhereHas('trip.driver.user', function($sq) use ($query) {
                      $sq->where('name', 'like', "%{$query}%");
                  });
            });
        }

        // Aplicar filtro de tipo
        if ($type) {
            $incidents->where('type', $type);
        }

        // Aplicar filtro de severidad
        if ($severity) {
            $incidents->where('severity', $severity);
        }

        // Aplicar filtro de fecha
        if ($dateRange) {
            [$start, $end] = explode(' - ', $dateRange);
            $incidents->whereBetween('reported_at', [$start, $end]);
        }

        $incidents = $incidents->orderBy('reported_at', 'desc')->paginate(10);
        
        return view('incidents.index', compact('incidents'));
    }

      //agregar un nuevo incidente / insertar
    public function store(StoreIncidentRequest $request)
    {
        // Create the incident with the validated data from the request
        $data = $request->validated();
        // Derive the resolved boolean from the resolution status if provided
        if (array_key_exists('resolution_status', $data)) {
            $data['resolved'] = ($data['resolution_status'] === 'resuelto');
        }
        $incident = Incident::create($data);

        // After creating an incident redirect back to the incidents list
        // and flash a success message. Using incidents.index here instead
        // of drivers.index ensures the user is returned to the correct
        // resource.
        return redirect()->route('incidents.index')
            ->with('success', 'Incidente creado exitosamente.');
    }

    //Actualizar / editar incidente
    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        // Update the incident with the validated data from the request
        $data = $request->validated();
        if (array_key_exists('resolution_status', $data)) {
            $data['resolved'] = ($data['resolution_status'] === 'resuelto');
        }
        $incident->update($data);

        // Redirect back to the incidents list and flash a success message
        return redirect()->route('incidents.index')
            ->with('success', 'Incidente actualizado exitosamente.');
    }

    //mostrar formulario de creación de incidente
    public function create(): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->whereIn('status', ['en_progreso', 'completado'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $types = Incident::getTypes();
        
        return view('incidents.create', compact('trips', 'types'));
    }

    //Mostrar un incidente
    public function show(Incident $incident): View
    {
        $incident->load([
            'trip.vehicle', 
            'trip.driver.user', 
            'trip.route',
            'trip.deliveries'
        ]);
        
        return view('incidents.show', compact('incident'));
    }

    //abrir formulario de editar incidente
    public function edit(Incident $incident): View
    {
        $trips = Trip::with(['vehicle', 'driver.user', 'route'])
            ->where('id', $incident->trip_id)
            ->orWhereIn('status', ['en_progreso', 'completado'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $types = Incident::getTypes();
            
        return view('incidents.edit', compact('incident', 'trips', 'types'));
    }

    //eliminar incidente
    public function destroy(Incident $incident): RedirectResponse
    {
        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incidente eliminado exitosamente.');
    }

    //marcar incidente como resuelto
    public function markAsResolved(Incident $incident): RedirectResponse
    {
        if ($incident->isResolved()) {
            return redirect()->route('incidents.index')
                ->with('error', 'El incidente ya está marcado como resuelto.');
        }

        $incident->markAsResolved();

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incidente marcado como resuelto exitosamente.');
    }

    //cambiar estado de resolución del incidente
    public function toggleResolved(Incident $incident): RedirectResponse
    {
        $newStatus = $incident->toggleResolved();
        $message = $newStatus ? 'resuelto' : 'pendiente';
        
        return redirect()->route('incidents.index')
            ->with('success', "Incidente marcado como: {$message}");
    }

    // obtener resumen de incidentes
    public function getSummary(): RedirectResponse
    {
        $summary = Incident::getStatsSummary();
        
        return redirect()->back()
            ->with('summary', $summary)
            ->with('success', 'Resumen de incidentes cargado exitosamente.');
    }

    // Obtener incidentes pendientes para dashboard
    public function getPending()
    {
        $incidents = Incident::pending()
            ->withRelations()
            ->orderBy('reported_at', 'desc')
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }

    // Buscar incidentes por descripción o tipo
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $incidents = Incident::with(['trip.vehicle', 'trip.driver.user'])
            ->where('description', 'like', "%{$query}%")
            ->orWhere('type', 'like', "%{$query}%")
            ->orderBy('reported_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }

    // Obtener incidentes recientes
    public function getRecent()
    {
        $incidents = Incident::recent()
            ->withRelations()
            ->orderBy('reported_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }

    // Obtener incidentes por tipo
    public function getByType(string $type)
    {
        $incidents = Incident::byType($type)
            ->withRelations()
            ->orderBy('reported_at', 'desc')
            ->get()
            ->map(function($incident) {
                return $incident->getSummaryInfo();
            });

        return response()->json($incidents);
    }
}
