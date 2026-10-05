<?php

use App\Models\Brand;
use App\Models\State;
use App\Models\User;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAuthUserForModel(): array
{
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Model Test User',
        'mobile_number' => '9999900003',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $token = $jwtService->generateToken($user);

    return [$user, $token];
}

test('vehicle models api requires authentication', function () {
    $this->getJson('/api/vehicle/vehicle-models')->assertStatus(401);
});

test('vehicle models api returns list of active models via GET', function () {
    [$user, $token] = createAuthUserForModel();

    $brand = Brand::create(['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car']);
    VehicleModel::create([
        'brand_id' => $brand->id,
        'name' => 'Swift',
        'vehicle_type' => 'Car',
        'vehicle_category' => 'Hatchback',
        'is_active' => true,
    ]);
    VehicleModel::create([
        'brand_id' => $brand->id,
        'name' => 'Discontinued',
        'vehicle_type' => 'Car',
        'vehicle_category' => 'Sedan',
        'is_active' => false,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/vehicle-models');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'brand_id',
                    'name',
                    'vehicle_type',
                    'vehicle_category',
                ],
            ],
        ]);

    expect($response->json('hasError'))->toBeFalse()
        ->and($response->json('errorCode'))->toBe(0)
        ->and($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Swift');
});

test('vehicle models api supports POST via vehicle-models-list endpoint', function () {
    [$user, $token] = createAuthUserForModel();

    $brand = Brand::create(['name' => 'Tata', 'vehicle_type' => 'Car']);
    VehicleModel::create([
        'brand_id' => $brand->id,
        'name' => 'Nexon',
        'vehicle_type' => 'Car',
        'vehicle_category' => 'SUV',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/vehicle-models-list');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'Vehicle models retrieved successfully.',
        ]);

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Nexon');
});

test('selection of brand and vehicle type triggers vehicle-models api to filter models of that brand and type', function () {
    [$user, $token] = createAuthUserForModel();

    $carType = VehicleType::create(['name' => 'Car', 'slug' => 'car']);
    $bikeType = VehicleType::create(['name' => 'Bike', 'slug' => 'bike']);
    $scooterType = VehicleType::create(['name' => 'Scooter', 'slug' => 'scooter']);

    $honda = Brand::create(['name' => 'Honda']);
    $maruti = Brand::create(['name' => 'Maruti Suzuki']);

    $cityCar = VehicleModel::create([
        'brand_id' => $honda->id,
        'vehicle_type_id' => $carType->id,
        'name' => 'City',
    ]);
    $activaScooter = VehicleModel::create([
        'brand_id' => $honda->id,
        'vehicle_type_id' => $scooterType->id,
        'name' => 'Activa 6G',
    ]);
    $shineBike = VehicleModel::create([
        'brand_id' => $honda->id,
        'vehicle_type_id' => $bikeType->id,
        'name' => 'Shine 125',
    ]);
    $swiftCar = VehicleModel::create([
        'brand_id' => $maruti->id,
        'vehicle_type_id' => $carType->id,
        'name' => 'Swift',
    ]);

    // Query models under Honda of type Car
    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/vehicle/vehicle-models?brand_id={$honda->id}&vehicle_type_id={$carType->id}");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('City');

    // Query models under Honda of type Scooter
    $scooterResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/vehicle/vehicle-models?brand_id={$honda->id}&vehicle_type_id={$scooterType->id}");

    $scooterResponse->assertOk();
    expect($scooterResponse->json('data'))->toHaveCount(1)
        ->and($scooterResponse->json('data.0.name'))->toBe('Activa 6G');
});

test('vehicle models api filters by search query', function () {
    [$user, $token] = createAuthUserForModel();

    $brand = Brand::create(['name' => 'Hyundai', 'vehicle_type' => 'Car']);
    VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Creta']);
    VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Venue']);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/vehicle-models?search=cre');

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Creta');
});
