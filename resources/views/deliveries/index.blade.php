@extends('layouts.app')

@section('title', 'Gestión de Entregas')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Entregas</h1>
            <a href="#" onclick="openIframeModal('{{ route('deliveries.create') }}'); return false;"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow flex items-center transition">
                <i class="fa-solid fa-plus mr-2"></i> Nueva Entrega
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
                <form method="GET" action="{{ route('deliveries.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/2">
                        <input type="text" name="search" placeholder="Buscar por cliente o dirección"
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="w-full md:w-1/4">
                        <select name="status"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="entregado" {{ request('status') == 'entregado' ? 'selected' : '' }}>Entregado
                            </option>
                            <option value="fallido" {{ request('status') == 'fallido' ? 'selected' : '' }}>Fallido</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-black px-4 py-2 rounded-lg">
                            <i class="fa-solid fa-search mr-2"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dirección</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Chofer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha
                                Entrega</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($deliveries as $delivery)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $delivery->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $delivery->customer_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $delivery->delivery_address }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $delivery->trip->vehicle->plate_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $delivery->trip->driver->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $delivery->trip->route->origin ?? 'N/A' }} →
                                    {{ $delivery->trip->route->destination ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if ($delivery->status == 'pendiente') bg-gray-100 text-gray-800
                                @elseif($delivery->status == 'entregado') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($delivery->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $delivery->delivery_time ? $delivery->delivery_time->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="#" onclick="openIframeModal('{{ route('deliveries.show', $delivery) }}'); return false;"
                                            class="inline-flex items-center bg-blue-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-600 transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>

                                        @if ($delivery->status == 'pendiente')
                                            <a href="#" onclick="openIframeModal('{{ route('deliveries.edit', $delivery) }}'); return false;"
                                                class="inline-flex items-center bg-yellow-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-yellow-600 transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i> Editar
                                            </a>

                                            <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center bg-red-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-600 transition-colors duration-200"
                                                    onclick="return confirm('¿Estás seguro de que deseas eliminar esta entrega? Esta acción no se puede deshacer.')">
                                                    <i class="fas fa-trash mr-1"></i> Eliminar
                                                </button>
                                            </form>
                                        @endif

                                        @if ($delivery->status == 'pendiente')
                                            <div class="dropdown inline-block relative">
                                                <button
                                                    class="inline-flex items-center bg-green-500 text-black px-3 py-1 rounded-lg shadow hover:bg-green-600 hover:scale-105 transition">
                                                    <i class="fas fa-check-circle mr-1"></i> Acciones
                                                </button>
                                                <div class="dropdown-menu absolute hidden text-gray-700 pt-1 right-0">
                                                    <form action="{{ route('deliveries.mark-as-delivered', $delivery) }}"
                                                        method="POST" class="block">
                                                        @csrf
                                                        <button type="submit"
                                                            class="bg-green-100 hover:bg-green-200 text-green-800 py-2 px-4 block whitespace-no-wrap w-full text-left">
                                                            <i class="fas fa-check mr-1"></i> Marcar como Entregado
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('deliveries.mark-as-failed', $delivery) }}"
                                                        method="POST" class="block">
                                                        @csrf
                                                        <button type="submit"
                                                            class="bg-red-100 hover:bg-red-200 text-red-800 py-2 px-4 block whitespace-no-wrap w-full text-left">
                                                            <i class="fas fa-times mr-1"></i> Marcar como Fallido
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron entregas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $deliveries->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');

            dropdowns.forEach(dropdown => {
                const button = dropdown.querySelector('button');
                const menu = dropdown.querySelector('.dropdown-menu');

                button.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                dropdowns.forEach(dropdown => {
                    const button = dropdown.querySelector('button');
                    const menu = dropdown.querySelector('.dropdown-menu');

                    if (!dropdown.contains(event.target)) {
                        menu.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endsection
