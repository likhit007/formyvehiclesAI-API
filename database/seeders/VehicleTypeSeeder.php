<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Car', 'slug' => 'car', 'icon' => 'car'],
            ['name' => 'Bike', 'slug' => 'bike', 'icon' => 'two_wheeler'],
            ['name' => 'Scooter', 'slug' => 'scooter', 'icon' => 'moped'],
            ['name' => 'Tractor', 'slug' => 'tractor', 'icon' => 'agriculture'],
            ['name' => 'Bus', 'slug' => 'bus', 'icon' => 'directions_bus'],
            ['name' => 'Truck', 'slug' => 'truck', 'icon' => 'local_shipping'],
            ['name' => 'Auto Rickshaw', 'slug' => 'auto_rickshaw', 'icon' => 'electric_rickshaw'],
        ];

        foreach ($types as $type) {
            VehicleType::firstOrCreate(
                ['slug' => $type['slug']],
                [
                    'name' => $type['name'],
                    'icon' => $type['icon'],
                    'is_active' => true,
                ]
            );
        }
    }
}
