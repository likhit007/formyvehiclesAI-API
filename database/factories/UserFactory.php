<?php

namespace Database\Factories;

use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'mobile_number' => fake()->unique()->numerify('98########'),
            'country_code' => '+91',
            'state_id' => State::factory(),
            'terms_accepted' => true,
        ];
    }
}
