<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin',
        'destination',
        'distance_km',
        'estimated_duration',
        'origin_latitude',
        'origin_longitude',
        'destination_latitude',
        'destination_longitude',
        'delivery_address',
        'status',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'estimated_duration' => 'decimal:2',
        'origin_latitude' => 'decimal:8',
        'origin_longitude' => 'decimal:8',
        'destination_latitude' => 'decimal:8',
        'destination_longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con viajes
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    // Obtener distancia formateada
    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance_km, 2) . ' km';
    }

    // Obtener descripción de la ruta
    public function getDescriptionAttribute(): string
    {
        return "{$this->origin} → {$this->destination}";
    }

    // Verificar si la ruta tiene viajes activos
    public function hasActiveTrips(): bool
    {
        return $this->trips()->whereIn('status', ['pendiente', 'en_progreso'])->exists();
    }

    // Obtener rutas disponibles (sin viajes activos)
    public static function available()
    {
        return static::whereDoesntHave('trips', function ($query) {
            $query->whereIn('status', ['pendiente', 'en_progreso']);
        })->where('status', 'active');
    }

    // Calcular distancia estimada entre dos puntos
    public static function calculateEstimatedDistance(string $origin, string $destination): array
    {
        // Simulación básica - en producción integrar con Google Maps API
        $estimatedDistance = rand(10, 500);
        // Generate a duration in hours (random between 1 and 48 hours)
        $estimatedDuration = rand(1, 48);

        return [
            'distance_km' => $estimatedDistance,
            'estimated_duration' => $estimatedDuration,
        ];
    }

    // Obtener información resumida de la ruta
    public function getSummaryInfo(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'distance_km' => $this->distance_km,
            'estimated_duration' => $this->estimated_duration,
            'formatted_distance' => $this->formatted_distance,
        ];
    }

    // Scope para buscar rutas por origen
    public function scopeByOrigin($query, string $origin)
    {
        return $query->where('origin', 'like', "%{$origin}%");
    }

    // Scope para buscar rutas por destino
    public function scopeByDestination($query, string $destination)
    {
        return $query->where('destination', 'like', "%{$destination}%");
    }

    // Scope para buscar rutas por estado
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // Verificar si se puede eliminar la ruta
    public function canBeDeleted(): bool
    {
        return !$this->hasActiveTrips();
    }

    // Verificar si la ruta está activa
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Suspender la ruta
    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    // Activar la ruta
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }
}
