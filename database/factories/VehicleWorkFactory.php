<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleWork;
use App\Models\VehicleWorkType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VehicleWork>
 */
class VehicleWorkFactory extends Factory
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
            'vehicle_id' => Vehicle::factory(),
            'type_id' => VehicleWorkType::factory(),
            'title' => fake()->sentence(3),
            'date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'cycle' => fake()->randomElement([1, 2, 3, 4]),
            'is_completed' => false,
            'status' => 'pending',
            'notes' => fake()->sentence(),
        ];
    }
}
