<?php

use App\Models\Brand;
use App\Models\State;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleWork;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAuthUserForWork(): array
{
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Vehicle Work User',
        'mobile_number' => '9876500001',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $token = $jwtService->generateToken($user);

    return [$user, $token];
}

function createVehicleForUser(User $user): Vehicle
{
    $brand = Brand::create(['name' => 'Toyota', 'vehicle_type' => 'Car']);

    return Vehicle::create([
        'user_id' => $user->id,
        'brand_id' => $brand->id,
        'registration_number' => 'KL 07 CD 1234',
        'model_name' => 'Innova Crysta',
        'year' => 2022,
        'fuel_type' => 'Diesel',
        'gear_type' => 'Automatic',
    ]);
}

test('vehicle add-info endpoint requires authentication', function () {
    $this->postJson('/api/vehicle/add-info', [])->assertStatus(401);
    $this->postJson('/vehicle/add-info', [])->assertStatus(401);
});

test('user can successfully add work info for a vehicle', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    $payload = [
        'title' => 'Engine Oil and Filter Replacement',
        'vehicle_id' => $vehicle->id,
        'type_id' => 1,
        'date' => '25-10-2026',
        'cycle' => 1,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/add-info', $payload);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                'isSuccess',
                'statusMessage',
            ],
        ])
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'data' => [
                'isSuccess' => true,
            ],
        ]);

    expect($response->json('data.isSuccess'))->toBeTrue()
        ->and($response->json('data.statusMessage'))->toBeString();

    $this->assertDatabaseHas('vehicle_works', [
        'user_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'type_id' => 1,
        'title' => 'Engine Oil and Filter Replacement',
        'date' => '2026-10-25',
        'cycle' => 1,
    ]);
});

test('endpoint works via /vehicle/add-info URL path', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    $payload = [
        'title' => 'Wheel Alignment and Balancing',
        'vehicle_id' => $vehicle->id,
        'type_id' => 6,
        'date' => '15-11-2026',
        'cycle' => 2,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/vehicle/add-info', $payload);

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'data' => [
                'isSuccess' => true,
            ],
        ]);

    $this->assertDatabaseHas('vehicle_works', [
        'vehicle_id' => $vehicle->id,
        'title' => 'Wheel Alignment and Balancing',
        'date' => '2026-11-15',
        'cycle' => 2,
    ]);
});

test('supports all recurring cycle options: One Time, Every week, Every month, Every Year', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    $cycles = [
        1 => 'One Time',
        2 => 'Every week',
        3 => 'Every month',
        4 => 'Every Year',
    ];

    foreach ($cycles as $cycleValue => $cycleName) {
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/vehicle/add-info', [
                'title' => "Maintenance {$cycleName}",
                'vehicle_id' => $vehicle->id,
                'type_id' => 1,
                'date' => '10-12-2026',
                'cycle' => $cycleValue,
            ]);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'isSuccess' => true,
                ],
            ]);

        $this->assertDatabaseHas('vehicle_works', [
            'vehicle_id' => $vehicle->id,
            'title' => "Maintenance {$cycleName}",
            'cycle' => $cycleValue,
        ]);
    }
});

test('validation fails when required fields are missing', function () {
    [$user, $token] = createAuthUserForWork();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/add-info', []);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'vehicle_id', 'type_id', 'date', 'cycle']);
});

test('validation fails when date format is not dd-MM-yyyy', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/add-info', [
            'title' => 'General Inspection',
            'vehicle_id' => $vehicle->id,
            'type_id' => 3,
            'date' => '2026-10-25', // Invalid format (yyyy-MM-dd instead of dd-MM-yyyy)
            'cycle' => 1,
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['date']);
});

test('validation fails when cycle value is invalid', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/add-info', [
            'title' => 'Brake Check',
            'vehicle_id' => $vehicle->id,
            'type_id' => 1,
            'date' => '05-11-2026',
            'cycle' => 5, // Invalid: only 1, 2, 3, 4 are permitted
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['cycle']);
});

test('validation fails when vehicle_id does not exist', function () {
    [$user, $token] = createAuthUserForWork();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/add-info', [
            'title' => 'AC Gas Refill',
            'vehicle_id' => 99999,
            'type_id' => 1,
            'date' => '01-11-2026',
            'cycle' => 1,
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['vehicle_id']);
});

test('returns 404 when vehicle belongs to another user', function () {
    [$user1, $token1] = createAuthUserForWork();

    $state = State::first();
    $user2 = User::create([
        'name' => 'Other Vehicle Owner',
        'mobile_number' => '9876500002',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);
    $otherVehicle = createVehicleForUser($user2);

    $response = $this->withHeader('Authorization', "Bearer {$token1}")
        ->postJson('/api/vehicle/add-info', [
            'title' => 'Battery Replacement',
            'vehicle_id' => $otherVehicle->id,
            'type_id' => 7,
            'date' => '10-11-2026',
            'cycle' => 1,
        ]);

    $response
        ->assertStatus(404)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 404,
            'data' => [
                'isSuccess' => false,
            ],
        ]);
});

test('validation fails when type_id does not exist', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicle/add-info', [
            'title' => 'Suspension Overhaul',
            'vehicle_id' => $vehicle->id,
            'type_id' => 99999,
            'date' => '15-11-2026',
            'cycle' => 1,
        ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['type_id']);
});

test('can retrieve vehicle work types list', function () {
    [$user, $token] = createAuthUserForWork();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/work-types');

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

    expect($response->json('data'))->not->toBeEmpty();
});

test('can list vehicle works for authenticated user', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    VehicleWork::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'type_id' => 1,
        'title' => 'Existing Service Work',
        'date' => '2026-10-30',
        'cycle' => 1,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/vehicle/works?vehicle_id={$vehicle->id}");

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
        ]);

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.title'))->toBe('Existing Service Work');
});

test('vehicle info-types endpoint requires authentication', function () {
    $this->getJson('/api/vehicle/info-types')->assertStatus(401);
    $this->getJson('/vehicle/info-types')->assertStatus(401);
});

test('can retrieve vehicle info types with static response', function () {
    [$user, $token] = createAuthUserForWork();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/info-types');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'data' => [
                'types' => [
                    [
                        'id' => 1,
                        'name' => 'Alert',
                    ],
                ],
            ],
        ]);

    expect($response->json('data.types'))->toBe([
        [
            'id' => 1,
            'name' => 'Alert',
        ],
    ]);
});

test('vehicle info-types works via /vehicle/info-types URL path', function () {
    [$user, $token] = createAuthUserForWork();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/vehicle/info-types');

    $response
        ->assertOk()
        ->assertJson([
            'data' => [
                'types' => [
                    [
                        'id' => 1,
                        'name' => 'Alert',
                    ],
                ],
            ],
        ]);
});

test('vehicle info-list endpoint requires authentication', function () {
    $this->getJson('/api/vehicle/info-list')->assertStatus(401);
    $this->postJson('/api/vehicle/info-list')->assertStatus(401);
    $this->getJson('/vehicle/info-list')->assertStatus(401);
});

test('vehicle info-list returns all vehicle works for user when id is null', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle1 = createVehicleForUser($user);

    $brand2 = Brand::create(['name' => 'Honda', 'vehicle_type' => 'Bike']);
    $vehicle2 = Vehicle::create([
        'user_id' => $user->id,
        'brand_id' => $brand2->id,
        'registration_number' => 'KL 08 EF 5678',
        'model_name' => 'Activa 6G',
    ]);

    VehicleWork::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle1->id,
        'type_id' => 1,
        'title' => 'Car Oil Change',
        'date' => '2026-10-25',
        'cycle' => 1,
    ]);

    VehicleWork::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle2->id,
        'type_id' => 2,
        'title' => 'Scooter Brake Pad Repair',
        'date' => '2026-11-05',
        'cycle' => 2,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/vehicle/info-list');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                'status' => [
                    '*' => [
                        'id',
                        'type' => ['id', 'name'],
                        'title',
                        'date',
                    ],
                ],
            ],
        ]);

    expect($response->json('data.status'))->toHaveCount(2)
        ->and(collect($response->json('data.status'))->pluck('title')->all())
        ->toContain('Car Oil Change', 'Scooter Brake Pad Repair')
        ->and($response->json('data.status.0.date'))->toBeString();
});

test('vehicle info-list filters by vehicle id when id is provided', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle1 = createVehicleForUser($user);

    $brand2 = Brand::create(['name' => 'Honda', 'vehicle_type' => 'Car']);
    $vehicle2 = Vehicle::create([
        'user_id' => $user->id,
        'brand_id' => $brand2->id,
        'registration_number' => 'KL 01 AB 9999',
        'model_name' => 'City',
    ]);

    VehicleWork::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle1->id,
        'type_id' => 1,
        'title' => 'Innova Service',
        'date' => '2026-10-25',
        'cycle' => 1,
    ]);

    VehicleWork::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle2->id,
        'type_id' => 1,
        'title' => 'City Wash',
        'date' => '2026-10-28',
        'cycle' => 1,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/vehicle/info-list?id={$vehicle1->id}");

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
        ]);

    expect($response->json('data.status'))->toHaveCount(1)
        ->and($response->json('data.status.0.title'))->toBe('Innova Service')
        ->and($response->json('data.status.0.date'))->toBe('25-10-2026')
        ->and($response->json('data.status.0.type.id'))->toBe(1)
        ->and($response->json('data.status.0.type.name'))->toBe('Alert');
});

test('vehicle info-list returns 404 when id belongs to another user', function () {
    [$user1, $token1] = createAuthUserForWork();

    $state = State::first();
    $user2 = User::create([
        'name' => 'Second User',
        'mobile_number' => '9876599999',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);
    $otherVehicle = createVehicleForUser($user2);

    $response = $this->withHeader('Authorization', "Bearer {$token1}")
        ->getJson("/api/vehicle/info-list?id={$otherVehicle->id}");

    $response
        ->assertStatus(404)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 404,
            'message' => 'Vehicle not found.',
        ]);
});

test('vehicle info-list works via /vehicle/info-list and POST', function () {
    [$user, $token] = createAuthUserForWork();
    $vehicle = createVehicleForUser($user);

    VehicleWork::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'type_id' => 1,
        'title' => 'General Inspection',
        'date' => '2026-11-01',
        'cycle' => 1,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/vehicle/info-list', ['id' => $vehicle->id]);

    $response
        ->assertOk()
        ->assertJson([
            'data' => [
                'status' => [
                    [
                        'title' => 'General Inspection',
                        'date' => '01-11-2026',
                    ],
                ],
            ],
        ]);
});
