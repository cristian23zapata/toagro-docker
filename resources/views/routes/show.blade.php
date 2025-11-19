@extends('layouts.app')

@section('title', 'Detalles de la Ruta')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles de la Ruta</h1>
            <div class="flex space-x-2">
                <form action="{{ route('routes.duplicate', $route) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center"
                        onclick="return confirm('¿Estás seguro de duplicar esta ruta?')">
                        <i class="fas fa-copy mr-2"></i> Duplicar
                    </button>
                </form>

                @if ($route->status === 'active')
                    <form action="{{ route('routes.suspend', $route) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center"
                            onclick="return confirm('¿Estás seguro de suspender esta ruta?')">
                            <i class="fas fa-pause mr-2"></i> Suspender
                        </button>
                    </form>
                @else
                    <form action="{{ route('routes.activate', $route) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center"
                            onclick="return confirm('¿Estás seguro de activar esta ruta?')">
                            <i class="fas fa-play mr-2"></i> Activar
                        </button>
                    </form>
                @endif

                <a href="#"
                    onclick="if (window.parent && typeof window.parent.openIframeModal === 'function') { window.parent.openIframeModal('{{ route('routes.edit', $route) }}'); } else { window.location.href=\"{{ route('routes.edit', $route) }}\"; } return false;"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
                <a href="#"
                    onclick="if (window.parent && typeof window.parent.closeIframeModal === 'function') { window.parent.closeIframeModal(); } else { window.location.href=\"{{ route('routes.index') }}\"; } return false;"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Volver
                </a>
            </div>
        </div>

        <!-- Mapa de la ruta -->
        @if (
            $route->origin_latitude &&
                $route->origin_longitude &&
                $route->destination_latitude &&
                $route->destination_longitude)
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">Mapa de la Ruta</h2>
                </div>
                <div class="px-6 py-4">
                    <div id="map" class="w-full h-96 rounded-lg"></div>
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

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Información de la Ruta</h2>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Origen</p>
                        <p class="text-lg font-medium">{{ $route->origin }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Destino</p>
                        <p class="text-lg font-medium">{{ $route->destination }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Latitud de Origen</p>
                        <p class="text-lg font-medium">{{ $route->origin_latitude ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Longitud de Origen</p>
                        <p class="text-lg font-medium">{{ $route->origin_longitude ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Latitud de Destino</p>
                        <p class="text-lg font-medium">{{ $route->destination_latitude ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Longitud de Destino</p>
                        <p class="text-lg font-medium">{{ $route->destination_longitude ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Dirección de Entrega</p>
                        <p class="text-lg font-medium">{{ $route->delivery_address ?? 'No especificada' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Distancia</p>
                        <p class="text-lg font-medium">{{ $route->distance_km }} km</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Duración Estimada</p>
                        <p class="text-lg font-medium">
                            {{ $route->estimated_duration ? $route->estimated_duration . ' horas' : 'No especificada' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estado</p>
                        <p class="text-lg font-medium">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $route->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $route->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ $route->status === 'active' ? 'Activa' : 'Suspendida' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Fecha de registro</p>
                        <p class="text-lg font-medium">{{ $route->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($route->trips->count() > 0)
            <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">Viajes en esta Ruta ({{ $route->trips->count() }})</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Conductor</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehículo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha Inicio</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($route->trips as $trip)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $trip->driver->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $trip->vehicle->brand }}
                                            {{ $trip->vehicle->model }} ({{ $trip->vehicle->plate_number }})</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $trip->start_time ? $trip->start_time->format('d/m/Y H:i') : 'No iniciado' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $trip->status == 'completado' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $trip->status == 'en_progreso' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $trip->status == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $trip->status == 'cancelado' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $trip->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Google Maps JavaScript API -->
    @if (
        $route->origin_latitude &&
            $route->origin_longitude &&
            $route->destination_latitude &&
            $route->destination_longitude)
        <script>
            function initMap() {
                // Coordenadas de origen y destino (asegurarse de que sean números)
                const originLat = parseFloat("{{ $route->origin_latitude }}");
                const originLng = parseFloat("{{ $route->origin_longitude }}");
                const destLat = parseFloat("{{ $route->destination_latitude }}");
                const destLng = parseFloat("{{ $route->destination_longitude }}");
                
                // Verificar que las coordenadas sean válidas
                if (isNaN(originLat) || isNaN(originLng) || isNaN(destLat) || isNaN(destLng)) {
                    console.error("Invalid coordinates");
                    showError("Coordenadas inválidas", "No se pudieron leer las coordenadas de la ruta correctamente.");
                    return;
                }
                
                const origin = {lat: originLat, lng: originLng};
                const destination = {lat: destLat, lng: destLng};

                // Crear el mapa centrado en el origen
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 10,
                    center: origin,
                });

                // Crear el servicio de direcciones
                const directionsService = new google.maps.DirectionsService();
                const directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map,
                    suppressMarkers: true, // Usar nuestros propios marcadores
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
                    title: "Origen: {{ $route->origin }}",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
                    }
                });

                new google.maps.Marker({
                    position: destination,
                    map: map,
                    title: "Destino: {{ $route->destination }}",
                    icon: {
                        url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                    }
                });
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
                                Origen: {{ $route->origin }}<br>
                                Destino: {{ $route->destination }}
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