@extends('layouts.app')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Editar Viaje</h1>

    <form action="{{ route('trips.update', $trip->id) }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium">Vehículo</label>
            <select name="vehicle_id" class="w-full border rounded-lg px-3 py-2">
                @foreach($vehicles as $vehicle)
                    @php
                        // Determine availability of the vehicle. If this vehicle is the one
                        // currently assigned to the trip, allow it to be selected even if
                        // it is busy. Otherwise disable it if it has active trips.
                        $vehicleAvailable = method_exists($vehicle, 'isAvailable') ? $vehicle->isAvailable() : true;
                        $allowSelection = ($trip->vehicle_id == $vehicle->id) || $vehicleAvailable;
                    @endphp
                    <option value="{{ $vehicle->id }}"
                        {{ $trip->vehicle_id == $vehicle->id ? 'selected' : '' }}
                        {{ $allowSelection ? '' : 'disabled' }}
                        class="{{ $allowSelection ? '' : 'bg-gray-200 text-gray-500' }}">
                        {{ $vehicle->plate_number }}{{ $allowSelection || $vehicleAvailable ? '' : ' (Ocupado)' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Chofer</label>
            <select name="driver_id" class="w-full border rounded-lg px-3 py-2">
                @foreach($drivers as $driver)
                    @php
                        $driverAvailable = method_exists($driver, 'isAvailable') ? $driver->isAvailable() : true;
                        $driverAllowSelection = ($trip->driver_id == $driver->id) || $driverAvailable;
                    @endphp
                    <option value="{{ $driver->id }}"
                        {{ $trip->driver_id == $driver->id ? 'selected' : '' }}
                        {{ $driverAllowSelection ? '' : 'disabled' }}
                        class="{{ $driverAllowSelection ? '' : 'bg-gray-200 text-gray-500' }}">
                        {{ $driver->user->name ?? 'Chofer ' . $driver->id }}{{ $driverAllowSelection || $driverAvailable ? '' : ' (Ocupado)' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Ruta</label>
            <select name="route_id" class="w-full border rounded-lg px-3 py-2">
                @foreach($routes as $route)
                    <option value="{{ $route->id }}" {{ $trip->route_id == $route->id ? 'selected' : '' }}>
                        {{ $route->origin }} → {{ $route->destination }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Inicio</label>
            <input type="datetime-local" name="start_time" value="{{ $trip->start_time->format('Y-m-d\TH:i') }}" class="w-full border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium">Fin</label>
            <input type="datetime-local" name="end_time" value="{{ $trip->end_time ? $trip->end_time->format('Y-m-d\TH:i') : '' }}" class="w-full border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium">Estado</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2">
                <option value="pendiente" {{ $trip->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_progreso" {{ $trip->status == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                <option value="completado" {{ $trip->status == 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="cancelado" {{ $trip->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>

        <div class="flex justify-end gap-3">
            <a href="#" class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400"
               onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('trips.index') }}\"; } return false;">Cancelar</a>
            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Actualizar</button>
        </div>
    </form>
</div>
@endsection
