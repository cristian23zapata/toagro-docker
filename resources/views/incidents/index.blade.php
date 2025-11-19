@extends('layouts.app')

@section('title', 'Gestión de Incidentes')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Incidentes</h1>
            <a href="#" onclick="openIframeModal('{{ route('incidents.create') }}'); return false;"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Nuevo Incidente
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-4 border-b">
                <form method="GET" action="{{ route('incidents.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/3">
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ request('q') }}"
                                   placeholder="Buscar por descripción o ubicación"
                                   class="w-full px-4 py-2 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-1/4">
                        <select name="type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los tipos</option>
                            <option value="mecanico" {{ request('type') == 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                            <option value="accidente" {{ request('type') == 'accidente' ? 'selected' : '' }}>Accidente</option>
                            <option value="retraso" {{ request('type') == 'retraso' ? 'selected' : '' }}>Retraso</option>
                            <option value="otro" {{ request('type') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>

                    <div class="w-full md:w-1/4">
                        <select name="severity" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las severidades</option>
                            <option value="baja" {{ request('severity') == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ request('severity') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ request('severity') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="critica" {{ request('severity') == 'critica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                    </div>

                    <div class="w-full md:w-1/6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fa-solid fa-search mr-2"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            @if(request('q') || request('type') || request('severity'))
                <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Mostrando resultados 
                        @if(request('q'))
                            para "<span class="font-medium">{{ request('q') }}</span>"
                        @endif
                        @if(request('type'))
                            del tipo "<span class="font-medium">{{ request('type') }}</span>"
                        @endif
                        @if(request('severity'))
                            con severidad "<span class="font-medium">{{ request('severity') }}</span>"
                        @endif
                        ({{ $incidents->total() }} encontrados)
                    </div>
                    <a href="{{ route('incidents.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-xmark"></i> Limpiar filtros
                    </a>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Viaje</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conductor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($incidents as $incident)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $incident->reported_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $incident->type === 'mecanico' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $incident->type === 'accidente' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $incident->type === 'retraso' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $incident->type === 'otro' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($incident->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $incident->severity === 'baja' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $incident->severity === 'media' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $incident->severity === 'alta' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $incident->severity === 'critica' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($incident->severity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $incident->trip->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $incident->trip->vehicle->plate_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $incident->trip->driver->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <button type="button"
                                            onclick="openIframeModal('{{ route('incidents.show', $incident) }}'); return false;"
                                            class="inline-flex items-center bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </button>

                                        <button type="button"
                                            onclick="openIframeModal('{{ route('incidents.edit', $incident) }}'); return false;"
                                            class="inline-flex items-center bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-600 transition-colors duration-200">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </button>

                                        <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="inline-flex items-center bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600 transition-colors duration-200"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar este incidente? Esta acción no se puede deshacer.')">
                                                <i class="fas fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron incidentes
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $incidents->withQueryString()->links() }}
            </div>
        </div>
    </div>
    {{-- Incidents list ends here. Showing and editing is handled by the generic iframe modal --}}

@endsection