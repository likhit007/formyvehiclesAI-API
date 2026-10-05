<?php

use App\Models\Brand;
use App\Models\State;
use App\Models\User;
use App\Models\VehicleType;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAuthUserForBrand(): array
{
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Brand Test User',
        'mobile_number' => '9999900002',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $token = $jwtService->generateToken($user);

    return [$user, $token];
}

test('brands api requires authentication', function () {
    $this->getJson('/api/vehicle/vehicle-brands')->assertStatus(401);
});

test('brands api returns list of active brands via GET', function () {
    [$user, $token] = createAuthUserForBrand();

    Brand::create(['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car', 'is_active' => true]);
    Brand::create(['name' => 'Hero Honda', 'vehicle_type' => 'Two Wheeler', 'is_active' => true]);
    Brand::create(['name' => 'Inactive Brand', 'vehicle_type' => 'Car', 'is_active' => false]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/vehicle-brands');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'vehicle_type',
                    'logo',
                ],
            ],
        ]);

    expect($response->json('hasError'))->toBeFalse()
        ->and($response->json('errorCode'))->toBe(0)
        ->and($response->json('data'))->toHaveCount(2)
        ->and($response->json('data.0.name'))->toBe('Hero Honda')
        ->and($response->json('data.1.name'))->toBe('Maruti Suzuki');
});

test('brands api supports POST via brands-list endpoint', function () {
    [$user, $token] = createAuthUserForBrand();

    Brand::create(['name' => 'Tata', 'vehicle_type' => 'Car', 'is_active' => true]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/brands-list');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'Brands retrieved successfully.',
        ]);

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Tata');
});

test('selection of vehicle type triggers vehicle-brands api to show only brands of that type', function () {
    [$user, $token] = createAuthUserForBrand();

    $carType = VehicleType::create(['name' => 'Car', 'slug' => 'car']);
    $bikeType = VehicleType::create(['name' => 'Bike', 'slug' => 'bike']);
    $tractorType = VehicleType::create(['name' => 'Tractor', 'slug' => 'tractor']);

    $maruti = Brand::create(['name' => 'Maruti Suzuki']);
    $maruti->vehicleTypes()->attach($carType->id);

    $honda = Brand::create(['name' => 'Honda']);
    $honda->vehicleTypes()->attach([$carType->id, $bikeType->id]);

    $johnDeere = Brand::create(['name' => 'John Deere']);
    $johnDeere->vehicleTypes()->attach($tractorType->id);

    // Filtering by vehicle_type_id for Car
    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/vehicle/vehicle-brands?vehicle_type_id={$carType->id}");

    $response->assertOk();
    $names = collect($response->json('data'))->pluck('name')->all();

    expect($names)->toContain('Maruti Suzuki')
        ->and($names)->toContain('Honda')
        ->and($names)->not->toContain('John Deere');

    // Filtering by vehicle_type_id for Tractor
    $tractorResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/vehicle/vehicle-brands?vehicle_type_id={$tractorType->id}");

    $tractorResponse->assertOk();
    $tractorNames = collect($tractorResponse->json('data'))->pluck('name')->all();

    expect($tractorNames)->toContain('John Deere')
        ->and($tractorNames)->not->toContain('Maruti Suzuki')
        ->and($tractorNames)->not->toContain('Honda');
});

test('brands api filters by search query', function () {
    [$user, $token] = createAuthUserForBrand();

    Brand::create(['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car']);
    Brand::create(['name' => 'Mahindra', 'vehicle_type' => 'Car']);
    Brand::create(['name' => 'Toyota', 'vehicle_type' => 'Car']);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/vehicle-brands?search=mahin');

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Mahindra');
});
