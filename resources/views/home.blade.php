@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="bg-green-50 py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-green-700 mb-4">Servicio Logístico de Alimentos</h1>
        <p class="text-lg md:text-xl text-green-600 mb-8 max-w-3xl mx-auto">
            Bienvenido a nuestra plataforma de logística donde gestionamos de manera eficiente vehículos, conductores, rutas, viajes, entregas e incidentes.
        </p>
        @auth
            <a href="{{ route('dashboard') }}" class="inline-block px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Ir al panel</a>
        @else
            <a href="{{ route('login') }}" class="inline-block px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Iniciar sesión</a>
        @endauth
    </div>
</div>

<div class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-semibold text-green-700 text-center mb-10">¿Qué ofrecemos?</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="p-6 bg-green-100 rounded-lg shadow-md flex flex-col items-center">
                <svg class="h-12 w-12 text-green-600 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0a2 2 0 100 4h6a2 2 0 100-4M9 17V8a5 5 0 0110 0v9m-6-9h0"></path></svg>
                <h3 class="text-xl font-semibold text-green-700 mb-2">Gestión de Vehículos</h3>
                <p class="text-center text-green-600">Administra la flota de transporte con información detallada y estados actualizados.</p>
            </div>
            <div class="p-6 bg-green-100 rounded-lg shadow-md flex flex-col items-center">
                <svg class="h-12 w-12 text-green-600 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h10M7 16h10M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="text-xl font-semibold text-green-700 mb-2">Planificación de Rutas</h3>
                <p class="text-center text-green-600">Optimiza tus recorridos con rutas personalizadas y cálculo de tiempos.</p>
            </div>
            <div class="p-6 bg-green-100 rounded-lg shadow-md flex flex-col items-center">
                <svg class="h-12 w-12 text-green-600 mb-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v4H3V3zm0 6h18v12H3V9z"></path></svg>
                <h3 class="text-xl font-semibold text-green-700 mb-2">Seguimiento de Entregas</h3>
                <p class="text-center text-green-600">Monitorea cada entrega en tiempo real y resuelve incidentes al instante.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-green-50 py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-semibold text-green-700 mb-4">Conoce a nuestro equipo</h2>
        <p class="text-green-600 mb-8 max-w-2xl mx-auto">Un equipo comprometido con la calidad y la excelencia en la logística de alimentos.</p>
        <div class="flex flex-wrap justify-center gap-6">
            <div class="w-44 text-center">
                <img src="https://images.unsplash.com/photo-1526948128573-703ee1aeb6fa?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=120&w=120" alt="Equipo" class="rounded-full mb-2 h-24 w-24 object-cover">
                <p class="font-semibold text-green-700">Carolina</p>
                <p class="text-sm text-green-600">Gestora de Rutas</p>
            </div>
            <div class="w-44 text-center">
                <img src="https://images.unsplash.com/photo-1506089676908-3592f7389d4d?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=120&w=120" alt="Equipo" class="rounded-full mb-2 h-24 w-24 object-cover">
                <p class="font-semibold text-green-700">Miguel</p>
                <p class="text-sm text-green-600">Coordinador de Flota</p>
            </div>
            <div class="w-44 text-center">
                <img src="https://images.unsplash.com/photo-1533734648867-a54d3a21a00f?crop=entropy&cs=tinysrgb&fit=crop&fm=jpg&h=120&w=120" alt="Equipo" class="rounded-full mb-2 h-24 w-24 object-cover">
                <p class="font-semibold text-green-700">Lucía</p>
                <p class="text-sm text-green-600">Control de Calidad</p>
            </div>
        </div>
    </div>
</div>
@endsection