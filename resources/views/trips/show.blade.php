@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-6">

    {{-- Título --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detalles del Viaje</h1>
        <div class="flex gap-2">
            {{-- Solo mostrar botón de editar para administradores y gestores --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
            <a href="#"
               onclick="if (window.parent && typeof window.parent.openIframeModal === 'function') { window.parent.openIframeModal('{{ route('trips.edit', $trip->id) }}'); } else { window.location.href=\"{{ route('trips.edit', $trip->id) }}\"; } return false;"
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg shadow">
               Editar
            </a>
            @endif
            <a href="#"
               onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('trips.index') }}\"; } return false;"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow">
               Volver
            </a>
        </div>
    </div>

    {{-- Mapa del Viaje --}}
    @if ($trip->route && 
         !is_null($trip->route->origin_latitude) && 
         !is_null($trip->route->origin_longitude) && 
         !is_null($trip->route->destination_latitude) && 
         !is_null($trip->route->destination_longitude))
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Mapa del Viaje en Tiempo Real</h2>
                @if($trip->status == 'en_progreso')
                    <button id="getCurrentLocationBtn" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow flex items-center">
                        <i class="fas fa-location-arrow mr-2"></i>
                        Obtener Ubicación Actual
                    </button>
                @endif
            </div>
            <div id="map" class="w-full h-96 rounded-lg"></div>
            <div id="locationInfo" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                <h3 class="font-semibold text-gray-700 mb-2">Información de Ubicación</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Latitud</p>
                        <p id="currentLat" class="font-medium text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Longitud</p>
                        <p id="currentLng" class="font-medium text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Última Actualización</p>
                        <p id="lastUpdate" class="font-medium text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Progreso Estimado</p>
                        <p id="progress" class="font-medium text-gray-900">-</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Información:</strong> Esta ruta no tiene coordenadas geográficas asignadas. 
                        Para ver el mapa, edite la ruta y agregue las coordenadas de origen y destino.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Información del Viaje --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Información del Viaje</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">Vehículo</p>
                <p class="font-medium text-gray-900">{{ $trip->vehicle->plate_number ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Chofer</p>
                <p class="font-medium text-gray-900">{{ $trip->driver->user->name ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Ruta</p>
                <p class="font-medium text-gray-900">{{ $trip->route->origin ?? 'N/A' }} → {{ $trip->route->destination ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Estado</p>
                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                    @if($trip->status == 'pendiente') bg-yellow-100 text-yellow-700 
                    @elseif($trip->status == 'en_progreso') bg-blue-100 text-blue-700
                    @elseif($trip->status == 'completado') bg-green-100 text-green-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($trip->status) }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500">Inicio</p>
                <p class="font-medium text-gray-900">{{ $trip->start_time ? $trip->start_time->format('d/m/Y H:i') : 'No iniciado' }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fin</p>
                <p class="font-medium text-gray-900">{{ $trip->end_time ? $trip->end_time->format('d/m/Y H:i') : 'En curso' }}</p>
            </div>

            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Notas</p>
                <p class="font-medium text-gray-900">{{ $trip->notes ?? 'Sin notas' }}</p>
            </div>
        </div>
    </div>

    {{-- Entregas asociadas --}}
    @if($trip->deliveries->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Entregas Asociadas ({{ $trip->deliveries->count() }})</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($trip->deliveries as $delivery)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $delivery->client_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->delivery_address }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($delivery->status == 'pendiente') bg-yellow-100 text-yellow-800
                                @elseif($delivery->status == 'entregado') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($delivery->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Incidentes --}}
    @if($trip->incidents->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Incidentes Reportados ({{ $trip->incidents->count() }})</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gravedad</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($trip->incidents as $incident)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $incident->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($incident->severity == 'baja') bg-green-100 text-green-800
                                @elseif($incident->severity == 'media') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($incident->severity) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Acciones --}}
    <div class="flex gap-2">
        {{-- Solo mostrar acciones de cambio de estado para administradores y gestores --}}
        @if((auth()->user()->isAdmin() || auth()->user()->isManager()))
            @if($trip->status == 'pendiente')
                <form action="{{ route('trips.start', $trip) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow"
                            onclick="return confirm('¿Iniciar este viaje?')">
                        Iniciar Viaje
                    </button>
                </form>
            @elseif($trip->status == 'en_progreso')
                <form action="{{ route('trips.complete', $trip) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow"
                            onclick="return confirm('¿Marcar este viaje como completado?')">
                        Completar Viaje
                    </button>
                </form>
                <form action="{{ route('trips.cancel', $trip) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow"
                            onclick="return confirm('¿Cancelar este viaje?')">
                        Cancelar Viaje
                    </button>
                </form>
            @endif

            {{-- Solo mostrar botón de eliminar para administradores y gestores --}}
            <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" 
                  onsubmit="return confirm('¿Seguro que deseas eliminar este viaje?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow">
                    Eliminar
                </button>
            </form>
        @endif
    </div>
</div>

@endsection

@section('scripts')
@if ($trip->route && 
     !is_null($trip->route->origin_latitude) && 
     !is_null($trip->route->origin_longitude) && 
     !is_null($trip->route->destination_latitude) && 
     !is_null($trip->route->destination_longitude))
<script>
    let tripTracker;

    function initMap() {
        // Coordenadas de origen y destino (asegurarse de que sean números)
        const originLat = parseFloat("{{ $trip->route->origin_latitude }}");
        const originLng = parseFloat("{{ $trip->route->origin_longitude }}");
        const destLat = parseFloat("{{ $trip->route->destination_latitude }}");
        const destLng = parseFloat("{{ $trip->route->destination_longitude }}");
        
        // Verificar que las coordenadas sean válidas
        if (isNaN(originLat) || isNaN(originLng) || isNaN(destLat) || isNaN(destLng)) {
            console.error("Invalid coordinates");
            showError("Coordenadas inválidas", "No se pudieron leer las coordenadas de la ruta correctamente.");
            return;
        }
        
        const origin = {lat: originLat, lng: originLng};
        const destination = {lat: destLat, lng: destLng};

        // Inicializar el mapa centrado en el origen
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 10,
            center: origin,
        });

        // Crear el servicio de direcciones
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true, // Para usar nuestros propios marcadores
            polylineOptions: {
                strokeColor: '#3b82f6', // blue-500
                strokeWeight: 6
            }
        });

        // Solicitar la ruta usando DRIVING mode para seguir carreteras
        directionsService.route({
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC
            }, (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                    
                    // Mostrar información adicional de la ruta
                    const route = response.routes[0];
                    const distance = route.legs[0].distance.text;
                    const duration = route.legs[0].duration.text;
                    
                    // Crear un info window con detalles de la ruta
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div><strong>Ruta por carreteras</strong><br>
                                  Distancia: ${distance}<br>
                                  Duración estimada: ${duration}</div>`
                    });
                    
                    // Abrir el info window en el mapa
                    infoWindow.open(map);
                } else {
                    console.error("Directions request failed due to " + status);
                    
                    // Si es un error de ZERO_RESULTS, intentar encontrar la carretera más cercana
                    if (status === "ZERO_RESULTS") {
                        findNearestRoadsAndRetry(directionsService, directionsRenderer, origin, destination, map);
                    } else {
                        // Mostrar mensaje de error específico
                        let errorMessage = "";
                        switch(status) {
                            case "MAX_WAYPOINTS_EXCEEDED":
                                errorMessage = "Demasiados puntos intermedios en la ruta.";
                                break;
                            case "INVALID_REQUEST":
                                errorMessage = "Solicitud inválida. Verifique las coordenadas.";
                                break;
                            case "OVER_QUERY_LIMIT":
                                errorMessage = "Límite de solicitudes excedido. Intente más tarde.";
                                break;
                            case "REQUEST_DENIED":
                                errorMessage = "Acceso denegado. Verifique la clave API.";
                                break;
                            case "UNKNOWN_ERROR":
                                errorMessage = "Error desconocido. Intente nuevamente.";
                                break;
                            default:
                                errorMessage = "Error al calcular la ruta: " + status;
                        }
                        
                        showError(errorMessage, "Mostrando línea directa entre puntos como alternativa.");
                        
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
                }
            });

        // Agregar marcadores personalizados
        new google.maps.Marker({
            position: origin,
            map: map,
            title: "Origen: {{ $trip->route->origin }}",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
            }
        });

        new google.maps.Marker({
            position: destination,
            map: map,
            title: "Destino: {{ $trip->route->destination }}",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
            }
        });

        // Inicializar el rastreador de viajes
        tripTracker = new TripTracker({{ $trip->id }}, 'map');
        tripTracker.map = map;
        tripTracker.currentLocationMarker = null;

        // Agregar evento al botón de obtener ubicación actual
        document.getElementById('getCurrentLocationBtn')?.addEventListener('click', function() {
            tripTracker.getCurrentLocation();
        });

        // Iniciar seguimiento automático si el viaje está en progreso
        @if($trip->status == 'en_progreso')
            // Actualizar cada 30 segundos
            tripTracker.startTracking(30000);
        @endif
    }
    
    // Función para encontrar las carreteras más cercanas y reintentar
    function findNearestRoadsAndRetry(directionsService, directionsRenderer, origin, destination, map) {
        // Mostrar mensaje de intento
        showError("Buscando carreteras cercanas...", "Por favor espere mientras buscamos la ruta más cercana.");
        
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
            let message = "";
            
            // Verificar si se encontraron carreteras cercanas para el origen
            if (originResponse.snappedPoints && originResponse.snappedPoints.length > 0) {
                snappedOrigin = {
                    lat: originResponse.snappedPoints[0].location.latitude,
                    lng: originResponse.snappedPoints[0].location.longitude
                };
                message += "Origen ajustado a carretera cercana. ";
            }
            
            // Verificar si se encontraron carreteras cercanas para el destino
            if (destinationResponse.snappedPoints && destinationResponse.snappedPoints.length > 0) {
                snappedDestination = {
                    lat: destinationResponse.snappedPoints[0].location.latitude,
                    lng: destinationResponse.snappedPoints[0].location.longitude
                };
                message += "Destino ajustado a carretera cercana.";
            }
            
            // Si se ajustó al menos un punto, intentar calcular la ruta nuevamente
            if (snappedOrigin !== origin || snappedDestination !== destination) {
                // Intentar calcular la ruta con los puntos ajustados
                directionsService.route({
                    origin: snappedOrigin,
                    destination: snappedDestination,
                    travelMode: google.maps.TravelMode.DRIVING,
                    unitSystem: google.maps.UnitSystem.METRIC
                }, (response, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(response);
                        
                        // Mostrar información adicional de la ruta
                        const route = response.routes[0];
                        const distance = route.legs[0].distance.text;
                        const duration = route.legs[0].duration.text;
                        
                        // Crear un info window con detalles de la ruta
                        const infoWindow = new google.maps.InfoWindow({
                            content: `<div><strong>Ruta por carreteras (ajustada)</strong><br>
                                      ${message}<br>
                                      Distancia: ${distance}<br>
                                      Duración estimada: ${duration}</div>`
                        });
                        
                        // Abrir el info window en el mapa
                        infoWindow.open(map);
                        
                        // Actualizar marcadores para mostrar los puntos ajustados
                        // Limpiar marcadores anteriores si es necesario
                    } else {
                        // Si aún falla, mostrar línea recta
                        showError("No se pudo calcular ruta por carreteras", "Mostrando línea directa entre puntos.");
                        
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
            } else {
                // Si no se encontraron carreteras cercanas, mostrar línea recta
                showError("No se encontraron carreteras cercanas", "Mostrando línea directa entre puntos.");
                
                const straightLine = new google.maps.Polyline({
                    path: [origin, destination],
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 3
                });
                
                straightLine.setMap(map);
            }
        })
        .catch(error => {
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
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC
        }, (response, status) => {
            if (status === "OK") {
                directionsRenderer.setDirections(response);
                
                // Mostrar información adicional de la ruta
                const route = response.routes[0];
                const distance = route.legs[0].distance.text;
                const duration = route.legs[0].duration.text;
                
                // Crear un info window con detalles de la ruta
                const infoWindow = new google.maps.InfoWindow({
                    content: `<div><strong>Ruta por carreteras (ajustada)</strong><br>
                              Coordenadas ligeramente ajustadas para encontrar carreteras.<br>
                              Distancia: ${distance}<br>
                              Duración estimada: ${duration}</div>`
                });
                
                // Abrir el info window en el mapa
                infoWindow.open(map);
            } else {
                // Si aún falla, mostrar línea recta
                showError("No se pudo calcular ruta por carreteras", "Mostrando línea directa entre puntos.");
                
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
    
    function showError(mainMessage, subMessage) {
        const mapContainer = document.getElementById("map");
        mapContainer.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 100%; background-color: #f8f9fa; text-align: center; padding: 20px;">
                <div>
                    <h3 style="color: #dc3545; margin-bottom: 10px;">${mainMessage}</h3>
                    <p style="color: #6c757d;">${subMessage}</p>
                    <p style="color: #6c757d; font-size: 0.9em; margin-top: 10px;">
                        Origen: {{ $trip->route->origin }}<br>
                        Destino: {{ $trip->route->destination }}
                    </p>
                </div>
            </div>
        `;
    }

    // Cargar la API de Google Maps
    window.initMap = initMap;
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&callback=initMap">
</script>
@endif
@endsection