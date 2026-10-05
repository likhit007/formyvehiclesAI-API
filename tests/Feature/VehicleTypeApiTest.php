<?php

use App\Models\State;
use App\Models\User;
use App\Models\VehicleType;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAuthUserForVehicleType(): array
{
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Vehicle Type User',
        'mobile_number' => '9999900001',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $token = $jwtService->generateToken($user);

    return [$user, $token];
}

test('vehicle types api requires authentication', function () {
    $this->getJson('/api/vehicle/vehicle-types')->assertStatus(401);
});

test('vehicle types api returns list of active vehicle types via GET', function () {
    [$user, $token] = createAuthUserForVehicleType();

    VehicleType::create(['name' => 'Car', 'slug' => 'car', 'is_active' => true]);
    VehicleType::create(['name' => 'Bike', 'slug' => 'bike', 'is_active' => true]);
    VehicleType::create(['name' => 'Tractor', 'slug' => 'tractor', 'is_active' => true]);
    VehicleType::create(['name' => 'Inactive Type', 'slug' => 'inactive', 'is_active' => false]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/vehicle-types');

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
                    'slug',
                    'icon',
                ],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(3)
        ->and(collect($response->json('data'))->pluck('name')->all())
        ->toEqual(['Car', 'Bike', 'Tractor']);
});

test('vehicle types api supports POST via vehicle-types-list', function () {
    [$user, $token] = createAuthUserForVehicleType();

    VehicleType::create(['name' => 'Bus', 'slug' => 'bus']);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/vehicle-types-list');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'Vehicle types retrieved successfully.',
        ]);

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Bus');
});

test('vehicle types api filters by search', function () {
    [$user, $token] = createAuthUserForVehicleType();

    VehicleType::create(['name' => 'Car', 'slug' => 'car']);
    VehicleType::create(['name' => 'Tractor', 'slug' => 'tractor']);
    VehicleType::create(['name' => 'Scooter', 'slug' => 'scooter']);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/vehicle-types?search=trac');

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Tractor');
});
