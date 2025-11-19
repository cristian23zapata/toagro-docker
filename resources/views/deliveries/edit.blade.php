@extends('layouts.app')

@section('title', 'Editar Entrega')

@section('content')
    <div class="p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Editar Entrega</h1>

        <form action="{{ route('deliveries.update', $delivery->id) }}" method="POST"
            class="space-y-4 bg-white p-6 rounded-lg shadow">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium">Viaje</label>
                <select name="trip_id" class="w-full border rounded-lg px-3 py-2">
                    @foreach ($trips as $trip)
                        <option value="{{ $trip->id }}" {{ $delivery->trip_id == $trip->id ? 'selected' : '' }}>
                            Viaje #{{ $trip->id }} - {{ $trip->vehicle->plate_number ?? 'N/A' }} -
                            {{ $trip->driver->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Nombre del Cliente</label>
                <input type="text" name="customer_name" value="{{ $delivery->customer_name }}"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Dirección de Entrega</label>
                <textarea name="delivery_address" class="w-full border rounded-lg px-3 py-2">{{ $delivery->delivery_address }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium">Fecha y Hora de Entrega</label>
                <input type="datetime-local" name="delivery_time"
                    value="{{ $delivery->delivery_time ? $delivery->delivery_time->format('Y-m-d\TH:i') : '' }}"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Estado</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}" {{ $delivery->status == $key ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="#"
                    class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400"
                    onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('deliveries.index') }}\"; } return false;">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Actualizar</button>
            </div>
        </form>
    </div>
@endsection
