@extends('layouts.app')

@section('title', 'Editar Ruta')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Editar Ruta</h1>
            <a href="#"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center"
                onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('routes.index') }}\"; } return false;">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('routes.update', $route) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Predefined Origin Selection -->
                    <div>
                        <label for="predefined_origin" class="block text-sm font-medium text-gray-700 mb-2">Origen
                            Predefinido</label>
                        <select id="predefined_origin"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione una ciudad de origen</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" data-name="{{ $location->name }}"
                                    data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}">
                                    {{ $location->name }} ({{ $location->department }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Predefined Destination Selection -->
                    <div>
                        <label for="predefined_destination" class="block text-sm font-medium text-gray-700 mb-2">Destino
                            Predefinido</label>
                        <select id="predefined_destination"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione una ciudad de destino</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" data-name="{{ $location->name }}"
                                    data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}">
                                    {{ $location->name }} ({{ $location->department }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="origin" class="block text-sm font-medium text-gray-700 mb-2">Origen *</label>
                        <input type="text" name="origin" id="origin" value="{{ old('origin', $route->origin) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('origin') border-red-500 @enderror"
                            placeholder="Ej: Ciudad de México" required>
                        @error('origin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">Destino *</label>
                        <input type="text" name="destination" id="destination"
                            value="{{ old('destination', $route->destination) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destination') border-red-500 @enderror"
                            placeholder="Ej: Guadalajara" required>
                        @error('destination')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="origin_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitud de
                            Origen</label>
                        <input type="number" step="any" name="origin_latitude" id="origin_latitude"
                            value="{{ old('origin_latitude', $route->origin_latitude) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('origin_latitude') border-red-500 @enderror"
                            placeholder="Ej: 19.432608">
                        @error('origin_latitude')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="origin_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitud de
                            Origen</label>
                        <input type="number" step="any" name="origin_longitude" id="origin_longitude"
                            value="{{ old('origin_longitude', $route->origin_longitude) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('origin_longitude') border-red-500 @enderror"
                            placeholder="Ej: -99.133209">
                        @error('origin_longitude')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="destination_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitud de
                            Destino</label>
                        <input type="number" step="any" name="destination_latitude" id="destination_latitude"
                            value="{{ old('destination_latitude', $route->destination_latitude) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destination_latitude') border-red-500 @enderror"
                            placeholder="Ej: 20.659698">
                        @error('destination_latitude')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="destination_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitud de
                            Destino</label>
                        <input type="number" step="any" name="destination_longitude" id="destination_longitude"
                            value="{{ old('destination_longitude', $route->destination_longitude) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destination_longitude') border-red-500 @enderror"
                            placeholder="Ej: -103.349609">
                        @error('destination_longitude')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-2">Dirección de
                            Entrega</label>
                        <textarea name="delivery_address" id="delivery_address" rows="3"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('delivery_address') border-red-500 @enderror"
                            placeholder="Dirección completa de entrega">{{ old('delivery_address', $route->delivery_address) }}</textarea>
                        @error('delivery_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="distance_km" class="block text-sm font-medium text-gray-700 mb-2">Distancia (km)
                            *</label>
                        <input type="number" step="0.01" name="distance_km" id="distance_km"
                            value="{{ old('distance_km', $route->distance_km) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('distance_km') border-red-500 @enderror"
                            placeholder="Ej: 534.5" min="0" required>
                        @error('distance_km')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="estimated_duration" class="block text-sm font-medium text-gray-700 mb-2">Duración
                            Estimada (horas) *</label>
                        <input type="number" step="0.1" name="estimated_duration" id="estimated_duration"
                            value="{{ old('estimated_duration', $route->estimated_duration) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('estimated_duration') border-red-500 @enderror"
                            placeholder="Ej: 48.5" min="0.1" required>
                        @error('estimated_duration')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                            required>
                            <option value="active" {{ (old('status') ?? $route->status) == 'active' ? 'selected' : '' }}>
                                Activa</option>
                            <option value="suspended"
                                {{ (old('status') ?? $route->status) == 'suspended' ? 'selected' : '' }}>Suspendida
                            </option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center">
                        <i class="fas fa-save mr-2"></i> Actualizar Ruta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for handling predefined locations -->
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Origin selection
            const originSelect = document.getElementById('predefined_origin');
            const originInput = document.getElementById('origin');
            const originLatInput = document.getElementById('origin_latitude');
            const originLngInput = document.getElementById('origin_longitude');

            originSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    originInput.value = selectedOption.getAttribute('data-name');
                    originLatInput.value = selectedOption.getAttribute('data-lat');
                    originLngInput.value = selectedOption.getAttribute('data-lng');
                } else {
                    originInput.value = '';
                    originLatInput.value = '';
                    originLngInput.value = '';
                }
            });

            // Destination selection
            const destinationSelect = document.getElementById('predefined_destination');
            const destinationInput = document.getElementById('destination');
            const destinationLatInput = document.getElementById('destination_latitude');
            const destinationLngInput = document.getElementById('destination_longitude');

            destinationSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    destinationInput.value = selectedOption.getAttribute('data-name');
                    destinationLatInput.value = selectedOption.getAttribute('data-lat');
                    destinationLngInput.value = selectedOption.getAttribute('data-lng');
                } else {
                    destinationInput.value = '';
                    destinationLatInput.value = '';
                    destinationLngInput.value = '';
                }
            });
        });
    </script>
@endsection
@endsection
