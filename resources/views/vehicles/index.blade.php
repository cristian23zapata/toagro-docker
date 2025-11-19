@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Vehículos</h1>
            <a href="#" onclick="openIframeModal('{{ route('vehicles.create') }}'); return false;"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow flex items-center transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Vehículo
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
                <form method="GET" action="{{ route('vehicles.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/2">
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ request('q') }}"
                                   placeholder="Buscar por placa, marca o modelo"
                                   class="w-full px-4 py-2 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-1/4">
                        <select name="status"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Todos los estados</option>
                            <option value="{{ App\Models\Vehicle::STATUS_ACTIVE }}" 
                                {{ request('status') == App\Models\Vehicle::STATUS_ACTIVE ? 'selected' : '' }}>
                                Activo
                            </option>
                            <option value="{{ App\Models\Vehicle::STATUS_INACTIVE }}" 
                                {{ request('status') == App\Models\Vehicle::STATUS_INACTIVE ? 'selected' : '' }}>
                                Inactivo
                            </option>
                            <option value="{{ App\Models\Vehicle::STATUS_MAINTENANCE }}" 
                                {{ request('status') == App\Models\Vehicle::STATUS_MAINTENANCE ? 'selected' : '' }}>
                                Mantenimiento
                            </option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fa-solid fa-search mr-2"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            @if(request('q') || request('status'))
                <div class="px-4 py-3 bg-gray-50 border-b">
                    <div class="text-sm text-gray-600">
                        Mostrando resultados 
                        @if(request('q'))
                            para "<span class="font-medium">{{ request('q') }}</span>"
                        @endif
                        @if(request('status'))
                            en estado "<span class="font-medium">{{ request('status') }}</span>"
                        @endif
                        ({{ $vehicles->total() }} encontrados)
                        <a href="{{ route('vehicles.index') }}" class="text-green-600 hover:text-green-800 ml-2">
                            <i class="fa-solid fa-xmark"></i> Limpiar filtros
                        </a>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Capacidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($vehicles as $v)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $v->plate_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $v->brand }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $v->model }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($v->capacity) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $v->status === 'activo'
                                        ? 'bg-green-100 text-green-800'
                                        : ($v->status === 'mantenimiento'
                                            ? 'bg-amber-100 text-amber-800'
                                            : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($v->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="#" onclick="openIframeModal('{{ route('vehicles.show', $v) }}'); return false;"
                                            class="inline-flex items-center bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>

                                        <a href="#" onclick="openIframeModal('{{ route('vehicles.edit', $v) }}'); return false;"
                                            class="inline-flex items-center bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-600 transition-colors duration-200">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </a>

                                        <form action="{{ route('vehicles.destroy', $v) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600 transition-colors duration-200"
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar este vehículo? Esta acción no se puede deshacer.')">
                                                <i class="fas fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron vehículos
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $vehicles->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
