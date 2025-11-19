<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = Config::get('services.google.maps.api_key');
    }

    /**
     * Get directions between origin and destination
     *
     * @param string $origin Latitude,Longitude or address
     * @param string $destination Latitude,Longitude or address
     * @param array $waypoints Optional waypoints
     * @return array|null
     */
    public function getDirections(string $origin, string $destination, array $waypoints = []): ?array
    {
        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'key' => $this->apiKey,
            'mode' => 'driving',
        ];

        if (!empty($waypoints)) {
            $params['waypoints'] = implode('|', $waypoints);
        }

        $response = Http::get("{$this->baseUrl}/directions/json", $params);

        if ($response->successful() && $response->json('status') === 'OK') {
            return $response->json();
        }

        return null;
    }

    /**
     * Find the nearest road to a given coordinate
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function findNearestRoad(float $latitude, float $longitude): ?array
    {
        try {
            $params = [
                'points' => "$latitude,$longitude",
                'key' => $this->apiKey,
            ];

            $response = Http::get("{$this->baseUrl}/roads/nearest/json", $params);

            if ($response->successful() && isset($response->json()['snappedPoints'][0])) {
                $snappedPoint = $response->json()['snappedPoints'][0];
                return [
                    'latitude' => $snappedPoint['location']['latitude'],
                    'longitude' => $snappedPoint['location']['longitude'],
                    'originalIndex' => $snappedPoint['originalIndex'] ?? null,
                    'placeId' => $snappedPoint['placeId'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // If Roads API fails, return null
            Log::warning('Google Roads API failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Snap coordinates to roads
     *
     * @param array $coordinates Array of [latitude, longitude] pairs
     * @return array|null
     */
    public function snapToRoads(array $coordinates): ?array
    {
        try {
            $points = [];
            foreach ($coordinates as $coord) {
                $points[] = $coord[0] . ',' . $coord[1];
            }

            $params = [
                'path' => implode('|', $points),
                'key' => $this->apiKey,
                'interpolate' => 'true',
            ];

            $response = Http::get("{$this->baseUrl}/roads/snapToRoads", $params);

            if ($response->successful() && isset($response->json()['snappedPoints'])) {
                $snappedPoints = [];
                foreach ($response->json()['snappedPoints'] as $point) {
                    $snappedPoints[] = [
                        'latitude' => $point['location']['latitude'],
                        'longitude' => $point['location']['longitude'],
                        'originalIndex' => $point['originalIndex'] ?? null,
                        'placeId' => $point['placeId'] ?? null,
                    ];
                }
                return $snappedPoints;
            }
        } catch (\Exception $e) {
            // If Roads API fails, return null
            Log::warning('Google Roads API failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Geocode an address to coordinates
     *
     * @param string $address
     * @return array|null
     */
    public function geocode(string $address): ?array
    {
        $response = Http::get("{$this->baseUrl}/geocode/json", [
            'address' => $address,
            'key' => $this->apiKey,
        ]);

        if ($response->successful() && $response->json('status') === 'OK') {
            $result = $response->json('results')[0];
            return [
                'lat' => $result['geometry']['location']['lat'],
                'lng' => $result['geometry']['location']['lng'],
                'formatted_address' => $result['formatted_address'],
            ];
        }

        return null;
    }

    /**
     * Reverse geocode coordinates to address
     *
     * @param float $lat
     * @param float $lng
     * @return array|null
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $response = Http::get("{$this->baseUrl}/geocode/json", [
            'latlng' => "$lat,$lng",
            'key' => $this->apiKey,
        ]);

        if ($response->successful() && $response->json('status') === 'OK') {
            $result = $response->json('results')[0];
            return [
                'address' => $result['formatted_address'],
            ];
        }

        return null;
    }
}