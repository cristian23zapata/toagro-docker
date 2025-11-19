@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-6">

        {{-- Encabezado --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Viaje</h1>
            <a href="#" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow"
               onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('trips.index') }}\"; } return false;">
                Volver
            </a>
        </div>

        {{-- Mostrar mensajes de éxito o error --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        {{-- Mostrar errores de validación --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario --}}
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('trips.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Vehículo --}}
                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehículo</label>
                    <select name="vehicle_id" id="vehicle_id"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un vehículo</option>
                        @foreach ($vehicles as $vehicle)
                            @php
                                // Determine if the vehicle is available. A vehicle is
                                // considered busy when it has an active trip (status
                                // pendiente or en_progreso). The `isAvailable()` helper
                                // defined on the Vehicle model checks for this automatically.
                                $isAvailable = method_exists($vehicle, 'isAvailable') ? $vehicle->isAvailable() : true;
                            @endphp
                            <option value="{{ $vehicle->id }}"
                                {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}
                                {{ $isAvailable ? '' : 'disabled' }}
                                class="{{ $isAvailable ? '' : 'bg-gray-200 text-gray-500' }}">
                                {{ $vehicle->plate_number }}{{ $isAvailable ? '' : ' (Ocupado)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Chofer --}}
                <div>
                    <label for="driver_id" class="block text-sm font-medium text-gray-700">Chofer</label>
                    <select name="driver_id" id="driver_id"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un chofer</option>
                        @foreach ($drivers as $driver)
                            @php
                                // Determine if the driver is available using the helper on the
                                // Driver model. If the driver currently has an active trip
                                // (pendiente or en_progreso), mark them as unavailable.
                                $isAvailableDriver = method_exists($driver, 'isAvailable') ? $driver->isAvailable() : true;
                            @endphp
                            <option value="{{ $driver->id }}"
                                {{ old('driver_id') == $driver->id ? 'selected' : '' }}
                                {{ $isAvailableDriver ? '' : 'disabled' }}
                                class="{{ $isAvailableDriver ? '' : 'bg-gray-200 text-gray-500' }}">
                                {{ $driver->user->name ?? 'Chofer ' . $driver->id }}{{ $isAvailableDriver ? '' : ' (Ocupado)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('driver_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ruta --}}
                <div>
                    <label for="route_id" class="block text-sm font-medium text-gray-700">Ruta</label>
                    <select name="route_id" id="route_id"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione una ruta</option>
                        @foreach ($routes as $route)
                            <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->origin }} → {{ $route->destination }}
                            </option>
                        @endforeach
                    </select>
                    @error('route_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Inicio --}}
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Fecha y Hora de Inicio</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fin --}}
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">Fecha y Hora de Fin</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un estado</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('status', 'pendiente') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón --}}
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow">
                        Guardar Viaje
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
