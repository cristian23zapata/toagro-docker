<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo de Rutas por Carreteras</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Demo de Rutas por Carreteras</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">¿Cómo funcionan las rutas por carreteras?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-blue-500 text-2xl mb-2"><i class="fas fa-route"></i></div>
                    <h3 class="font-semibold text-lg mb-2">Rutas Reales por Carreteras</h3>
                    <p class="text-gray-600">
                        Nuestro sistema utiliza la API de Google Maps Directions para calcular rutas reales que siguen 
                        carreteras, autopistas y caminos. Esto proporciona distancias y tiempos de viaje precisos.
                    </p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-red-500 text-2xl mb-2"><i class="fas fa-minus"></i></div>
                    <h3 class="font-semibold text-lg mb-2">Líneas Rectas (Fallback)</h3>
                    <p class="text-gray-600">
                        En caso de fallos en la API de Google Maps, el sistema muestra una línea recta entre puntos 
                        como solución de respaldo, pero esta no representa rutas reales de conducción.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Ejemplo Visual</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-lg mb-2 text-green-600">Ruta por Carreteras (Real)</h3>
                    <div class="border-2 border-dashed border-green-300 rounded-lg h-48 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-road text-green-500 text-3xl mb-2"></i>
                            <p class="text-gray-600">Ruta siguiendo caminos y autopistas</p>
                            <p class="text-sm text-gray-500 mt-1">Distancia: 250 km | Tiempo: 3.5 horas</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-2 text-red-600">Línea Recta (Fallback)</h3>
                    <div class="border-2 border-dashed border-red-300 rounded-lg h-48 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-arrow-right text-red-500 text-3xl mb-2"></i>
                            <p class="text-gray-600">Línea directa entre puntos</p>
                            <p class="text-sm text-gray-500 mt-1">Distancia: 180 km | Tiempo: No aplica</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Beneficios de las Rutas por Carreteras</h2>
            <ul class="list-disc list-inside space-y-2 text-gray-600">
                <li>Distancias y tiempos de viaje precisos basados en condiciones reales del tráfico</li>
                <li>Rutas optimizadas que siguen caminos, autopistas y vías navegables</li>
                <li>Información detallada de giros, salidas y puntos de referencia</li>
                <li>Actualizaciones en tiempo real para evitar congestiones</li>
                <li>Mejor planificación de entregas y asignación de recursos</li>
            </ul>
            
            <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Nota:</strong> Todas las rutas en nuestro sistema utilizan la API de Google Maps 
                            Directions para proporcionar rutas reales por carreteras. Las líneas rectas solo se muestran 
                            como fallback en caso de errores de la API.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>