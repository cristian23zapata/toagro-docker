@extends('layouts.app')

@section('title', 'Detalles del Incidente')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles del Incidente #{{ $incident->id }}</h1>
            <div class="flex space-x-3">
                <a href="#"
                    onclick="if (window.parent && typeof window.parent.openIframeModal === 'function') { window.parent.openIframeModal('{{ route('incidents.edit', $incident) }}'); } else { window.location.href=\"{{ route('incidents.edit', $incident) }}\"; } return false;"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
                <a href="#"
                    onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('incidents.index') }}\"; } return false;"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Información básica -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Tipo y Severidad -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información General</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Tipo:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ml-2
                                    {{ $incident->type === 'mecanico' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $incident->type === 'accidente' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $incident->type === 'retraso' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $incident->type === 'otro' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($incident->type) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Severidad:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ml-2
                                    {{ $incident->severity === 'baja' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $incident->severity === 'media' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $incident->severity === 'alta' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $incident->severity === 'critica' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($incident->severity) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Estado de Resolución:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ml-2
                                    {{ $incident->resolution_status === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $incident->resolution_status === 'en_proceso' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $incident->resolution_status === 'resuelto' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($incident->resolution_status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Viaje -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del Viaje</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Viaje ID:</span>
                                <span class="ml-2">#{{ $incident->trip->id }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Vehículo:</span>
                                <span class="ml-2">{{ $incident->trip->vehicle->plate_number }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Conductor:</span>
                                <span class="ml-2">{{ $incident->trip->driver->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fecha y Ubicación -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Fecha y Ubicación</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Reportado el:</span>
                                <span class="ml-2">{{ $incident->reported_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Ubicación:</span>
                                <span class="ml-2">{{ $incident->location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción y Acciones -->
            <div class="border-t border-gray-200 px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $incident->description }}</p>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Tomadas</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ $incident->actions_taken ?: 'No se han registrado acciones.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-gray-50 px-6 py-4 text-sm text-gray-500">
                <div class="flex justify-between">
                    <div>
                        Creado: {{ $incident->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        Última actualización: {{ $incident->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection