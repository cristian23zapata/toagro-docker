<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PredefinedLocation;

class PredefinedLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['name' => 'Medellín', 'department' => 'Antioquia', 'latitude' => 6.244203, 'longitude' => -75.581211],
            ['name' => 'Bogotá', 'department' => 'Cundinamarca', 'latitude' => 4.609710, 'longitude' => -74.081750],
            ['name' => 'Cali', 'department' => 'Valle del Cauca', 'latitude' => 3.437220, 'longitude' => -76.522500],
            ['name' => 'Barranquilla', 'department' => 'Atlántico', 'latitude' => 10.968540, 'longitude' => -74.781320],
            ['name' => 'Cartagena', 'department' => 'Bolívar', 'latitude' => 10.391040, 'longitude' => -75.479430],
            ['name' => 'Cúcuta', 'department' => 'Norte de Santander', 'latitude' => 7.893900, 'longitude' => -72.507820],
            ['name' => 'Bucaramanga', 'department' => 'Santander', 'latitude' => 7.125390, 'longitude' => -73.119800],
            ['name' => 'Pereira', 'department' => 'Risaralda', 'latitude' => 4.813330, 'longitude' => -75.696110],
            ['name' => 'Santa Marta', 'department' => 'Magdalena', 'latitude' => 11.240350, 'longitude' => -74.199040],
            ['name' => 'Manizales', 'department' => 'Caldas', 'latitude' => 5.068780, 'longitude' => -75.517370],
            ['name' => 'Ibagué', 'department' => 'Tolima', 'latitude' => 4.438890, 'longitude' => -75.232220],
            ['name' => 'Pasto', 'department' => 'Nariño', 'latitude' => 1.213670, 'longitude' => -77.281110],
            ['name' => 'Neiva', 'department' => 'Huila', 'latitude' => 2.927300, 'longitude' => -75.281880],
            ['name' => 'Villavicencio', 'department' => 'Meta', 'latitude' => 4.142000, 'longitude' => -73.626640],
            ['name' => 'Armenia', 'department' => 'Quindío', 'latitude' => 4.533890, 'longitude' => -75.681110],
            ['name' => 'Cajicá', 'department' => 'Cundinamarca', 'latitude' => 4.933330, 'longitude' => -74.033330],
            ['name' => 'Cartago', 'department' => 'Valle del Cauca', 'latitude' => 4.746390, 'longitude' => -75.911670],
            ['name' => 'Tulua', 'department' => 'Valle del Cauca', 'latitude' => 4.083330, 'longitude' => -76.199440],
            ['name' => 'Palmira', 'department' => 'Valle del Cauca', 'latitude' => 3.539440, 'longitude' => -76.303610],
            ['name' => 'Buenaventura', 'department' => 'Valle del Cauca', 'latitude' => 3.880100, 'longitude' => -77.031160],
        ];

        foreach ($locations as $location) {
            PredefinedLocation::updateOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}
