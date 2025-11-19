@extends('layouts.app')

@section('title', 'Detalles de la Entrega')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-6">

    {{-- Título --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detalles de la Entrega</h1>
        <div class="flex gap-2">
            <a href="#"
               onclick="if (window.parent && typeof window.parent.openIframeModal === 'function') { window.parent.openIframeModal('{{ route('deliveries.edit', $delivery->id) }}'); } else { window.location.href=\"{{ route('deliveries.edit', $delivery->id) }}\"; } return false;"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow">
               Editar
            </a>
            <a href="#"
               onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('deliveries.index') }}\"; } return false;"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow">
               Volver
            </a>
        </div>
    </div>

    <!-- Mapa de la entrega -->
    @if($delivery->trip && $delivery->trip->route && 
        $delivery->trip->route->origin_latitude && $delivery->trip->route->origin_longitude && 
        $delivery->trip->route->destination_latitude && $delivery->trip->route->destination_longitude)
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Mapa de la Entrega</h2>
        </div>
        <div class="px-6 py-4">
            <div id="map" class="w-full h-96 rounded-lg"></div>
        </div>
    </div>
    @endif

    {{-- Información de la Entrega --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Información de la Entrega</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">ID de Entrega</p>
                <p class="font-medium text-gray-900">{{ $delivery->id }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Cliente</p>
                <p class="font-medium text-gray-900">{{ $delivery->customer_name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Dirección de Entrega</p>
                <p class="font-medium text-gray-900">{{ $delivery->delivery_address }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Estado</p>
                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                    @if($delivery->status == 'pendiente') bg-gray-100 text-gray-700 
                    @elseif($delivery->status == 'entregado') bg-green-100 text-green-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($delivery->status) }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fecha de Entrega</p>
                <p class="font-medium text-gray-900">{{ $delivery->delivery_time ? $delivery->delivery_time->format('d/m/Y H:i') : 'Pendiente' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Creado</p>
                <p class="font-medium text-gray-900">{{ $delivery->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Información del Viaje --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Información del Viaje</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">ID del Viaje</p>
                <p class="font-medium text-gray-900">{{ $delivery->trip->id }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Vehículo</p>
                <p class="font-medium text-gray-900">{{ $delivery->trip->vehicle->plate_number ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Chofer</p>
                <p class="font-medium text-gray-900">{{ $delivery->trip->driver->user->name ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Ruta</p>
                <p class="font-medium text-gray-900">{{ $delivery->trip->route->origin ?? 'N/A' }} → {{ $delivery->trip->route->destination ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    @if($delivery->status == 'pendiente')
    <div class="flex gap-2">
        <form action="{{ route('deliveries.mark-as-delivered', $delivery) }}" method="POST">
            @csrf
            <button type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow">
                Marcar como Entregado
            </button>
        </form>
        
        <form action="{{ route('deliveries.mark-as-failed', $delivery) }}" method="POST">
            @csrf
            <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow">
                Marcar como Fallido
            </button>
        </form>
        
        <form action="{{ route('deliveries.destroy', $delivery->id) }}" method="POST" 
              onsubmit="return confirm('¿Seguro que deseas eliminar esta entrega?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow">
                Eliminar
            </button>
        </form>
    </div>
    @endif
</div>

<!-- Google Maps JavaScript API -->
@if($delivery->trip && $delivery->trip->route && 
    $delivery->trip->route->origin_latitude && $delivery->trip->route->origin_longitude && 
    $delivery->trip->route->destination_latitude && $delivery->trip->route->destination_longitude)
<script>
    function initMap() {
        // Coordenadas de origen y destino
        const origin = { 
            lat: {{ $delivery->trip->route->origin_latitude }}, 
            lng: {{ $delivery->trip->route->origin_longitude }} 
        };
        
        const destination = { 
            lat: {{ $delivery->trip->route->destination_latitude }}, 
            lng: {{ $delivery->trip->route->destination_longitude }} 
        };

        // Crear el mapa centrado en el origen
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 10,
            center: origin,
        });

        // Crear el servicio de direcciones
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);

        // Solicitar la ruta
        directionsService
            .route({
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
            })
            .then((response) => {
                directionsRenderer.setDirections(response);
                
                // Agregar marcador para la dirección de entrega
                const deliveryMarker = new google.maps.Marker({
                    position: destination,
                    title: "Dirección de Entrega: {{ $delivery->customer_name }}",
                    map: map,
                });
            })
            .catch((e) => {
                console.error("Directions request failed due to " + e);
                
                // Si es un error de ZERO_RESULTS, intentar encontrar la carretera más cercana
                if (e.message && e.message.includes("ZERO_RESULTS")) {
                    findNearestRoadsAndRetry(directionsService, directionsRenderer, origin, destination, map);
                } else {
                    // Si falla, mostrar una línea recta entre los puntos como fallback
                    const straightLine = new google.maps.Polyline({
                        path: [origin, destination],
                        geodesic: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 3
                    });
                    
                    straightLine.setMap(map);
                }
            });
    }
    
    // Función para encontrar las carreteras más cercanas y reintentar
    function findNearestRoadsAndRetry(directionsService, directionsRenderer, origin, destination, map) {
        // Usar Roads API para encontrar puntos cercanos en carreteras
        const apiKey = "{{ config('services.google.maps.api_key') }}";
        
        // Crear solicitudes para encontrar carreteras cercanas
        const originRequest = `https://roads.googleapis.com/v1/nearestRoads?points=${origin.lat},${origin.lng}&key=${apiKey}`;
        const destinationRequest = `https://roads.googleapis.com/v1/nearestRoads?points=${destination.lat},${destination.lng}&key=${apiKey}`;
        
        // Realizar ambas solicitudes en paralelo
        Promise.all([
            fetch(originRequest),
            fetch(destinationRequest)
        ])
        .then(responses => Promise.all(responses.map(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Roads API request failed with status ' + response.status);
            }
        })))
        .then(([originResponse, destinationResponse]) => {
            let snappedOrigin = origin;
            let snappedDestination = destination;
            
            // Verificar si se encontraron carreteras cercanas para el origen
            if (originResponse.snappedPoints && originResponse.snappedPoints.length > 0) {
                snappedOrigin = {
                    lat: originResponse.snappedPoints[0].location.latitude,
                    lng: originResponse.snappedPoints[0].location.longitude
                };
            }
            
            // Verificar si se encontraron carreteras cercanas para el destino
            if (destinationResponse.snappedPoints && destinationResponse.snappedPoints.length > 0) {
                snappedDestination = {
                    lat: destinationResponse.snappedPoints[0].location.latitude,
                    lng: destinationResponse.snappedPoints[0].location.longitude
                };
            }
            
            // Si se ajustó al menos un punto, intentar calcular la ruta nuevamente
            if (snappedOrigin !== origin || snappedDestination !== destination) {
                // Intentar calcular la ruta con los puntos ajustados
                directionsService.route({
                    origin: snappedOrigin,
                    destination: snappedDestination,
                    travelMode: google.maps.TravelMode.DRIVING
                }).then((response) => {
                    directionsRenderer.setDirections(response);
                    
                    // Agregar marcador para la dirección de entrega
                    const deliveryMarker = new google.maps.Marker({
                        position: destination,
                        title: "Dirección de Entrega: {{ $delivery->customer_name }}",
                        map: map,
                    });
                }).catch((e) => {
                    console.error("Directions request failed even with snapped points: " + e);
                    // Si aún falla, mostrar línea recta
                    const straightLine = new google.maps.Polyline({
                        path: [origin, destination],
                        geodesic: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 3
                    });
                    
                    straightLine.setMap(map);
                });
            } else {
                // Si no se encontraron carreteras cercanas, mostrar línea recta
                const straightLine = new google.maps.Polyline({
                    path: [origin, destination],
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 3
                });
                
                straightLine.setMap(map);
            }
        }).catch(error => {
            console.error("Error finding nearest roads:", error);
            // Si hay un error en la API de carreteras, intentar con un enfoque alternativo
            findAlternativeRoute(directionsService, directionsRenderer, origin, destination, map);
        });
    }
    
    // Función alternativa para encontrar una ruta cuando la Roads API no está disponible
    function findAlternativeRoute(directionsService, directionsRenderer, origin, destination, map) {
        // Intentar calcular la ruta con una pequeña compensación para evitar puntos exactos problemáticos
        const offsetOrigin = {
            lat: origin.lat + (Math.random() - 0.5) * 0.01,
            lng: origin.lng + (Math.random() - 0.5) * 0.01
        };
        
        const offsetDestination = {
            lat: destination.lat + (Math.random() - 0.5) * 0.01,
            lng: destination.lng + (Math.random() - 0.5) * 0.01
        };
        
        directionsService.route({
            origin: offsetOrigin,
            destination: offsetDestination,
            travelMode: google.maps.TravelMode.DRIVING
        }).then((response) => {
            directionsRenderer.setDirections(response);
            
            // Agregar marcador para la dirección de entrega
            const deliveryMarker = new google.maps.Marker({
                position: destination,
                title: "Dirección de Entrega: {{ $delivery->customer_name }}",
                map: map,
            });
        }).catch((e) => {
            console.error("Alternative route calculation also failed: " + e);
            // Si aún falla, mostrar línea recta
            const straightLine = new google.maps.Polyline({
                path: [origin, destination],
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 3
            });
            
            straightLine.setMap(map);
        });
    }

    // Cargar la API de Google Maps
    window.initMap = initMap;
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&callback=initMap">
</script>
@endif
@endsection