<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StateSeeder::class,
            VehicleTypeSeeder::class,
            BrandSeeder::class,
            VehicleModelSeeder::class,
        ]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'mobile_number' => '9999999999',
        //     'country_code' => '+91',
        //     'state_id' => 1,
        //     'terms_accepted' => true,
        // ]);
    }
}
