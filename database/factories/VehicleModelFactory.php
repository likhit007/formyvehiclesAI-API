<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'vehicle_type_id' => VehicleType::factory(),
            'name' => fake()->word(),
            'vehicle_type' => fake()->randomElement(['Car', 'Bike', 'Scooter', 'Tractor', 'Bus']),
            'vehicle_category' => fake()->randomElement(['Hatchback', 'Sedan', 'SUV', 'Motorcycle', 'Scooter']),
            'is_active' => true,
        ];
    }
}
