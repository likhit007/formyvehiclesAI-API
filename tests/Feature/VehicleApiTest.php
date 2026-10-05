<?php

use App\Models\Brand;
use App\Models\State;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAuthenticatedUser(): array
{
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Test User',
        'mobile_number' => '9876543210',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $token = $jwtService->generateToken($user);

    return [$user, $token];
}

test('vehicles endpoints require authentication', function () {
    $this->getJson('/api/vehicles')->assertStatus(401);
    $this->postJson('/api/vehicles', [])->assertStatus(401);
});

test('user can list only their own vehicles', function () {
    [$user1, $token1] = createAuthenticatedUser();

    $state = State::first();
    $user2 = User::create([
        'name' => 'Other User',
        'mobile_number' => '9111111111',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $brand = Brand::create(['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car']);
    $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'Swift']);

    $vehicle1 = Vehicle::create([
        'user_id' => $user1->id,
        'brand_id' => $brand->id,
        'vehicle_model_id' => $model->id,
        'registration_number' => 'KL 56 X 7004',
        'model_name' => 'Swift VXI Hatchback',
        'year' => 2022,
        'fuel_type' => 'Petrol',
        'gear_type' => 'Automatic',
    ]);

    $vehicle2 = Vehicle::create([
        'user_id' => $user2->id,
        'brand_id' => $brand->id,
        'vehicle_model_id' => $model->id,
        'registration_number' => 'KL 07 B 1234',
        'model_name' => 'Swift ZXI',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token1}")
        ->getJson('/api/vehicles');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'brand_id',
                    'vehicle_model_id',
                    'registration_number',
                    'model_name',
                    'year',
                    'fuel_type',
                    'gear_type',
                    'brand' => ['id', 'name'],
                    'vehicle_model' => ['id', 'name'],
                ],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.registration_number'))->toBe('KL 56 X 7004')
        ->and($response->json('data.0.user_id'))->toBe($user1->id);
});

test('user can add a new vehicle', function () {
    [$user, $token] = createAuthenticatedUser();

    $vehicleType = VehicleType::create(['name' => 'Bike', 'slug' => 'bike']);
    $brand = Brand::create(['name' => 'Hero Honda', 'vehicle_type' => 'Two Wheeler']);
    $model = VehicleModel::create([
        'brand_id' => $brand->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'GLAMOUR 125 Fi',
        'vehicle_type' => 'Two Wheeler',
        'vehicle_category' => 'Motorcycle',
    ]);

    $payload = [
        'registration_number' => 'kl 56 e 6332',
        'vehicle_type_id' => $vehicleType->id,
        'brand_id' => $brand->id,
        'vehicle_model_id' => $model->id,
        'year' => 2012,
        'fuel_type' => 'Petrol',
        'gear_type' => 'Manual',
        'color' => 'Red',
        'category' => 'Motorcycle',
        'seating_capacity' => '2-Seater',
        'mileage_km' => 55.5,
        'is_taxi' => false,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicles', $payload);

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'Vehicle added successfully.',
            'data' => [
                'registration_number' => 'KL 56 E 6332',
                'model_name' => 'GLAMOUR 125 Fi',
                'user_id' => $user->id,
                'vehicle_type_id' => $vehicleType->id,
                'brand_id' => $brand->id,
                'vehicle_model_id' => $model->id,
            ],
        ]);

    $this->assertDatabaseHas('vehicles', [
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'registration_number' => 'KL 56 E 6332',
        'model_name' => 'GLAMOUR 125 Fi',
    ]);
});

test('adding vehicle validates required fields', function () {
    [$user, $token] = createAuthenticatedUser();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/vehicles', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['registration_number', 'brand_id']);
});

test('user can view their single vehicle details', function () {
    [$user, $token] = createAuthenticatedUser();

    $brand = Brand::create(['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car']);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'brand_id' => $brand->id,
        'registration_number' => 'KL 56 X 7004',
        'model_name' => 'Swift VXI Hatchback',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/vehicles/{$vehicle->id}");

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'data' => [
                'id' => $vehicle->id,
                'registration_number' => 'KL 56 X 7004',
            ],
        ]);
});

test('user cannot view or update another user vehicle', function () {
    [$user1, $token1] = createAuthenticatedUser();

    $state = State::first();
    $user2 = User::create([
        'name' => 'Other User',
        'mobile_number' => '9222222222',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $brand = Brand::create(['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car']);
    $vehicle = Vehicle::create([
        'user_id' => $user2->id,
        'brand_id' => $brand->id,
        'registration_number' => 'KL 01 AB 9999',
    ]);

    // View should return 404
    $this->withHeader('Authorization', "Bearer {$token1}")
        ->getJson("/api/vehicles/{$vehicle->id}")
        ->assertStatus(404)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 404,
            'message' => 'Vehicle not found.',
        ]);

    // Update should return 404
    $this->withHeader('Authorization', "Bearer {$token1}")
        ->putJson("/api/vehicles/{$vehicle->id}", ['color' => 'Black'])
        ->assertStatus(404);

    // Delete should return 404
    $this->withHeader('Authorization', "Bearer {$token1}")
        ->deleteJson("/api/vehicles/{$vehicle->id}")
        ->assertStatus(404);
});

test('user can update and delete their own vehicle', function () {
    [$user, $token] = createAuthenticatedUser();

    $brand = Brand::create(['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car']);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'brand_id' => $brand->id,
        'registration_number' => 'KL 56 X 7004',
        'color' => 'White',
    ]);

    // Update
    $updateResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/vehicles/{$vehicle->id}", [
            'color' => 'Silver',
            'mileage_km' => 20.5,
        ]);

    $updateResponse->assertOk();
    expect($vehicle->fresh()->color)->toBe('Silver')
        ->and((float) $vehicle->fresh()->mileage_km)->toBe(20.5);

    // Delete
    $deleteResponse = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/vehicles/{$vehicle->id}");

    $deleteResponse->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'Vehicle deleted successfully.',
        ]);

    $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
});
