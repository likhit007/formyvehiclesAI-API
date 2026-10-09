<?php

namespace Database\Seeders;

use App\Models\VehicleWorkType;
use Illuminate\Database\Seeder;

class VehicleWorkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Alert', 'slug' => 'alert', 'icon' => 'notifications'],
            ['name' => 'Repair', 'slug' => 'repair', 'icon' => 'handyman'],

            ['name' => 'Inspection', 'slug' => 'inspection', 'icon' => 'fact_check'],
            ['name' => 'Insurance Renewal', 'slug' => 'insurance-renewal', 'icon' => 'shield'],
            ['name' => 'PUC / Pollution Check', 'slug' => 'puc-pollution-check', 'icon' => 'eco'],
            ['name' => 'Tire & Wheel Service', 'slug' => 'tire-wheel-service', 'icon' => 'tire_repair'],
            ['name' => 'Battery Service', 'slug' => 'battery-service', 'icon' => 'battery_charging_full'],
            ['name' => 'Other', 'slug' => 'other', 'icon' => 'more_horiz'],
        ];

        foreach ($types as $type) {
            VehicleWorkType::firstOrCreate(
                ['slug' => $type['slug']],
                [
                    'name' => $type['name'],
                    'icon' => $type['icon'],
                    'is_active' => true,
                ],
            );
        }
    }
}
