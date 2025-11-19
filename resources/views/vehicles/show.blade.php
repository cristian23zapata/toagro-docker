@extends('layouts.app')

@section('title', 'Detalles del Vehículo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Detalles del Vehículo</h1>
        <div class="flex space-x-3">
            <!-- Botón para editar el vehículo dentro de un modal -->
            <a href="#"
               onclick="if (window.parent && typeof window.parent.openIframeModal === 'function') { window.parent.openIframeModal('{{ route('vehicles.edit', $vehicle) }}'); } else { window.location.href='{{ route('vehicles.edit', $vehicle) }}'; } return false;"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg shadow transition duration-200 flex items-center">
                <i class="fas fa-edit mr-2"></i> Editar
            </a>
            <!-- Botón para volver; cierra el modal si existe, de lo contrario redirige -->
            <a href="#"
               onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href='{{ route('vehicles.index') }}'; } return false;"
               class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg shadow transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </div>

    <!-- Información del Vehículo -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-100 to-gray-200 border-b">
            <h2 class="text-xl font-semibold text-gray-900">Información del Vehículo</h2>
        </div>
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Placa</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $vehicle->plate_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Marca</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $vehicle->brand }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Modelo</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $vehicle->model }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Capacidad (kg)</p>
                    <p class="text-lg font-semibold text-gray-800">{{ number_format($vehicle->capacity) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Estado</p>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full shadow
                        {{ $vehicle->status == 'activo' ? 'bg-green-100 text-green-800' : ($vehicle->status == 'mantenimiento' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($vehicle->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Fecha de registro</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $vehicle->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Viajes Asignados -->
    @if($vehicle->trips->count() > 0)
    <div class="bg-white shadow-lg rounded-xl overflow-hidden mt-8">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-100 to-gray-200 border-b">
            <h2 class="text-xl font-semibold text-gray-900">Viajes Asignados ({{ $vehicle->trips->count() }})</h2>
        </div>
        <div class="px-6 py-6">
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Chofer</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Ruta</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Fecha Inicio</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($vehicle->trips as $trip)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $trip->driver->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $trip->route->origin }} → {{ $trip->route->destination }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-800">{{ $trip->start_time ? $trip->start_time->format('d/m/Y H:i') : 'No iniciado' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full shadow
                                    {{ $trip->status == 'completado' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $trip->status == 'en_progreso' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $trip->status == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $trip->status == 'cancelado' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($trip->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection