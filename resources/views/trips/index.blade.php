@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestión de Viajes</h1>
            {{-- Solo mostrar botón de crear viaje para administradores y gestores --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
            <a href="#" onclick="openIframeModal('{{ route('trips.create') }}'); return false;"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                Nuevo Viaje
            </a>
            @endif
        </div>

        {{-- Filtros de búsqueda --}}
        <form method="GET" action="{{ route('trips.index') }}" class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-2/3">
                    <div class="relative">
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}"
                               placeholder="Buscar por vehículo, chofer o ruta"
                               class="w-full px-4 py-2 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-1/6">
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_progreso" {{ request('status') == 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                        <option value="completado" {{ request('status') == 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="w-full md:w-1/6">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        <i class="fa-solid fa-search mr-2"></i> Buscar
                    </button>
                </div>
            </div>
        </form>

        @if(request('q') || request('status'))
            <div class="bg-gray-50 px-4 py-3 mb-6 rounded-lg border flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Mostrando resultados 
                    @if(request('q'))
                        para "<span class=\"font-medium\">{{ request('q') }}</span>"
                    @endif
                    @if(request('status'))
                        con estado "<span class=\"font-medium\">{{ request('status') }}</span>"
                    @endif
                    ({{ $trips->total() }} encontrados)
                </div>
                <a href="{{ route('trips.index') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fa-solid fa-xmark"></i> Limpiar filtros
                </a>
            </div>
        @endif

        {{-- Tabla de viajes --}}
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left border-collapse">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Vehículo</th>
                        <th class="px-4 py-3">Chofer</th>
                        <th class="px-4 py-3">Ruta</th>
                        <th class="px-4 py-3">Inicio</th>
                        <th class="px-4 py-3">Fin</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trips as $trip)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $trip->id }}</td>
                            <td class="px-4 py-2">{{ $trip->vehicle->plate_number ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $trip->driver->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $trip->route->origin }} → {{ $trip->route->destination }}</td>
                            <td class="px-4 py-2">{{ $trip->start_time ? $trip->start_time->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-4 py-2">{{ $trip->end_time ? $trip->end_time->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-2">
                                @switch($trip->status)
                                    @case('pendiente')
                                        <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">Pendiente</span>
                                    @break

                                    @case('en_progreso')
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs">En
                                            progreso</span>
                                    @break

                                    @case('completado')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">Completado</span>
                                    @break

                                    @case('cancelado')
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs">Cancelado</span>
                                    @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="#" onclick="openIframeModal('{{ route('trips.show', $trip->id) }}'); return false;"
                                        class="inline-flex items-center bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition-colors duration-200">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                    
                                    {{-- Solo mostrar botones de editar y eliminar para administradores y gestores --}}
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <a href="#" onclick="openIframeModal('{{ route('trips.edit', $trip->id) }}'); return false;"
                                        class="inline-flex items-center bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-600 transition-colors duration-200">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                    
                                    <form action="{{ route('trips.destroy', $trip->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600 transition-colors duration-200"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este viaje? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-gray-500">No hay viajes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endsection