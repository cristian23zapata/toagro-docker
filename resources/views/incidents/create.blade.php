@extends('layouts.app')

@section('title', 'Nuevo Incidente')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Nuevo Incidente</h1>
            <a href="#" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"
               onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('incidents.index') }}\"; } return false;">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('incidents.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Viaje asociado -->
                    <div>
                        <label for="trip_id" class="block text-sm font-medium text-gray-700 mb-2">Viaje Asociado</label>
                        <select name="trip_id" id="trip_id" required
                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                            <option value="">Seleccione un viaje</option>
                            @foreach($trips as $trip)
                                <option value="{{ $trip->id }}" {{ old('trip_id') == $trip->id ? 'selected' : '' }}>
                                    Viaje #{{ $trip->id }} - {{ $trip->vehicle->plate_number }} - {{ $trip->driver->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('trip_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de incidente -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Incidente</label>
                        <select name="type" id="type" required
                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                            <option value="">Seleccione el tipo</option>
                            <option value="mecanico" {{ old('type') == 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                            <option value="accidente" {{ old('type') == 'accidente' ? 'selected' : '' }}>Accidente</option>
                            <option value="retraso" {{ old('type') == 'retraso' ? 'selected' : '' }}>Retraso</option>
                            <option value="otro" {{ old('type') == 'otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Severidad -->
                    <div>
                        <label for="severity" class="block text-sm font-medium text-gray-700 mb-2">Severidad</label>
                        <select name="severity" id="severity" required
                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                            <option value="">Seleccione la severidad</option>
                            <option value="baja" {{ old('severity') == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('severity') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('severity') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="critica" {{ old('severity') == 'critica' ? 'selected' : '' }}>Crítica</option>
                        </select>
                        @error('severity')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                        <input type="text" name="location" id="location" required value="{{ old('location') }}"
                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                            placeholder="Ubicación del incidente">
                        @error('location')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha y hora -->
                    <div>
                        <label for="reported_at" class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora</label>
                        <input type="datetime-local" name="reported_at" id="reported_at" required value="{{ old('reported_at') }}"
                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                        @error('reported_at')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado de resolución -->
                    <div>
                        <label for="resolution_status" class="block text-sm font-medium text-gray-700 mb-2">Estado de Resolución</label>
                        <select name="resolution_status" id="resolution_status" required
                            class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                            <option value="pendiente" {{ old('resolution_status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ old('resolution_status') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="resuelto" {{ old('resolution_status') == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                        </select>
                        @error('resolution_status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" id="description" rows="4" required
                        class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                        placeholder="Describe el incidente">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Acciones tomadas -->
                <div class="mt-6">
                    <label for="actions_taken" class="block text-sm font-medium text-gray-700 mb-2">Acciones Tomadas</label>
                    <textarea name="actions_taken" id="actions_taken" rows="4"
                        class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                        placeholder="Describe las acciones tomadas">{{ old('actions_taken') }}</textarea>
                    @error('actions_taken')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    {{-- Close the modal on cancel. If the page is not inside a modal, fallback to redirecting to the index. --}}
                    <button type="button"
                        onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href='{{ route('incidents.index') }}'; } return false;"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i> Guardar Incidente
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection