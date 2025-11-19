<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo de Mapa de Viajes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Demo de Mapa de Viajes en Tiempo Real</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Cómo funciona el sistema de seguimiento</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-blue-500 text-2xl mb-2"><i class="fas fa-map-marked-alt"></i></div>
                    <h3 class="font-semibold text-lg mb-2">Visualización de Ruta</h3>
                    <p class="text-gray-600">El sistema muestra la ruta planificada desde el origen hasta el destino en un mapa interactivo de Google Maps.</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-green-500 text-2xl mb-2"><i class="fas fa-location-arrow"></i></div>
                    <h3 class="font-semibold text-lg mb-2">Ubicación en Tiempo Real</h3>
                    <p class="text-gray-600">La ubicación actual del vehículo se actualiza automáticamente cada 30 segundos o puede obtenerse manualmente.</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-purple-500 text-2xl mb-2"><i class="fas fa-chart-line"></i></div>
                    <h3 class="font-semibold text-lg mb-2">Seguimiento de Progreso</h3>
                    <p class="text-gray-600">El sistema calcula y muestra el porcentaje de progreso estimado del viaje basado en la ubicación actual.</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Instrucciones de uso</h2>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Accede a la sección de "Viajes" en la aplicación</li>
                <li>Selecciona un viaje que tenga una ruta con coordenadas asignadas</li>
                <li>Verás el mapa con la ruta planificada y marcadores de origen/destino</li>
                <li>Si el viaje está en progreso, se mostrará automáticamente la ubicación actual</li>
                <li>Puedes hacer clic en "Obtener Ubicación Actual" para actualizar manualmente la posición</li>
                <li>La información de ubicación se mostrará en el panel inferior del mapa</li>
            </ol>
            
            <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Nota:</strong> En una implementación real, las ubicaciones se obtendrían de dispositivos GPS en los vehículos. 
                            En esta demo, las ubicaciones se generan aleatoriamente para fines de visualización.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>