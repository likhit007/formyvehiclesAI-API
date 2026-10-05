<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vehicle_type_id' => VehicleType::factory(),
            'brand_id' => Brand::factory(),
            'vehicle_model_id' => VehicleModel::factory(),
            'registration_number' => 'KL '.fake()->numberBetween(10, 99).' '.fake()->lexify('?').' '.fake()->numberBetween(1000, 9999),
            'vehicle_type' => 'Car',
            'model_name' => fake()->word().' VXI',
            'year' => fake()->numberBetween(2010, 2024),
            'fuel_type' => fake()->randomElement(['Petrol', 'Diesel', 'Electric', 'CNG', 'Hybrid']),
            'gear_type' => fake()->randomElement(['Manual', 'Automatic']),
            'color' => fake()->randomElement(['White', 'Silver', 'Red', 'Black', 'Blue']),
            'category' => fake()->randomElement(['Hatchback', 'Sedan', 'SUV']),
            'seating_capacity' => '5-Seater',
            'mileage_km' => fake()->randomFloat(2, 10, 28),
            'is_taxi' => false,
            'image_url' => null,
            'is_active' => true,
        ];
    }
}
