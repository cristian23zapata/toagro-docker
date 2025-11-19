@extends('layouts.app')

@section('title', 'Crear Nueva Entrega')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-6">

        {{-- Encabezado --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Crear Nueva Entrega</h1>
            <a href="#"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow"
                onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('deliveries.index') }}\"; } return false;">
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
            <form action="{{ route('deliveries.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Viaje --}}
                <div>
                    <label for="trip_id" class="block text-sm font-medium text-gray-700">Viaje</label>
                    <select name="trip_id" id="trip_id"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un viaje</option>
                        @foreach ($trips as $trip)
                            <option value="{{ $trip->id }}" {{ old('trip_id') == $trip->id ? 'selected' : '' }}>
                                Viaje #{{ $trip->id }} - {{ $trip->vehicle->plate_number ?? 'N/A' }} -
                                {{ $trip->driver->user->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('trip_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre del Cliente --}}
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700">Nombre del Cliente</label>
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dirección de Entrega --}}
                <div>
                    <label for="delivery_address" class="block text-sm font-medium text-gray-700">Dirección de
                        Entrega</label>
                    <textarea name="delivery_address" id="delivery_address" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">{{ old('delivery_address') }}</textarea>
                    @error('delivery_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fecha y Hora de Entrega --}}
                <div>
                    <label for="delivery_time" class="block text-sm font-medium text-gray-700">Fecha y Hora de
                        Entrega</label>
                    <input type="datetime-local" name="delivery_time" id="delivery_time" value="{{ old('delivery_time') }}"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('delivery_time')
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
                        Guardar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
