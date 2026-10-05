<?php

use App\Models\OtpVerification;
use App\Models\State;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('signup returns the expected api envelope and creates a user with state_name', function () {
    $response = $this->postJson('/api/auth/signup', [
        'name' => 'Lucy',
        'state_id' => null,
        'state_name' => 'Karnataka',
        'country_code' => '+91',
        'mobile_number' => '9812345678',
        'terms_accepted' => true,
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'mobile_number',
                    'country_code',
                    'state_id',
                    'terms_accepted',
                ],
                'otp' => ['code'],
            ],
        ]);

    expect($response->json('hasError'))->toBeFalse()
        ->and($response->json('errorCode'))->toBe(0)
        ->and($response->json('data.user.name'))->toBe('Lucy')
        ->and($response->json('data.user.id'))->toBeInt()
        ->and($response->json('data.user.state_id'))->toBeInt();

    $this->assertDatabaseHas('users', [
        'name' => 'Lucy',
        'mobile_number' => '9812345678',
        'country_code' => '+91',
        'terms_accepted' => true,
    ]);

    $this->assertDatabaseHas('states', [
        'name' => 'Karnataka',
    ]);

    $this->assertDatabaseHas('otp_verifications', [
        'mobile_number' => '9812345678',
        'consumed' => false,
    ]);
});

test('signup creates a user with an existing integer state_id', function () {
    $state = State::create([
        'name' => 'Maharashtra',
        'code' => 'MH',
    ]);

    $response = $this->postJson('/api/auth/signup', [
        'name' => 'John Doe',
        'state_id' => $state->id,
        'country_code' => '+91',
        'mobile_number' => '9876543210',
        'terms_accepted' => true,
    ]);

    $response->assertOk();

    expect($response->json('data.user.state_id'))->toBe($state->id);

    $this->assertDatabaseHas('users', [
        'mobile_number' => '9876543210',
        'state_id' => $state->id,
    ]);
});

test('signup rejects invalid state_id', function () {
    $response = $this->postJson('/api/auth/signup', [
        'name' => 'Invalid State User',
        'state_id' => 999999,
        'country_code' => '+91',
        'mobile_number' => '9876543211',
        'terms_accepted' => true,
    ]);

    $response->assertUnprocessable();
});

test('signup returns conflict error when mobile number already exists', function () {
    $state = State::create(['name' => 'Delhi', 'code' => 'DL']);
    User::create([
        'name' => 'Existing User',
        'mobile_number' => '9812345678',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $response = $this->postJson('/api/auth/signup', [
        'name' => 'Duplicate User',
        'state_id' => $state->id,
        'country_code' => '+91',
        'mobile_number' => '9812345678',
        'terms_accepted' => true,
    ]);

    $response
        ->assertStatus(409)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 409,
            'message' => 'User already exists. Please login instead.',
            'data' => null,
        ]);
});

test('login returns a structured error for an unregistered mobile number', function () {
    $response = $this->postJson('/api/auth/login', [
        'country_code' => '+91',
        'mobile_number' => '9999999999',
    ]);

    $response
        ->assertNotFound()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data',
        ]);

    expect($response->json('hasError'))->toBeTrue()
        ->and($response->json('errorCode'))->toBe(404)
        ->and($response->json('data'))->toBeNull()
        ->and($response->json('message'))->toContain('not found');
});

test('login sends otp successfully for an existing user', function () {
    $state = State::create(['name' => 'Goa', 'code' => 'GA']);
    $user = User::create([
        'name' => 'Login User',
        'mobile_number' => '9123456789',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'country_code' => '+91',
        'mobile_number' => '9123456789',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                'user' => ['id', 'name', 'mobile_number'],
                'otp' => ['code'],
            ],
        ]);

    expect($response->json('hasError'))->toBeFalse()
        ->and($response->json('data.user.id'))->toBe($user->id);

    $this->assertDatabaseHas('otp_verifications', [
        'user_id' => $user->id,
        'mobile_number' => '9123456789',
        'consumed' => false,
    ]);
});

test('verifyOtp verifies valid otp and marks it consumed', function () {
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Verify User',
        'mobile_number' => '9000000001',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $otp = OtpVerification::create([
        'user_id' => $user->id,
        'mobile_number' => '9000000001',
        'code' => '1234',
        'expires_at' => now()->addMinutes(5),
        'consumed' => false,
    ]);

    $response = $this->postJson('/api/auth/verify-otp', [
        'mobile_number' => '9000000001',
        'code' => '1234',
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'OTP verified successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => 'Verify User',
                    'mobile_number' => '9000000001',
                ],
            ],
        ]);

    expect($response->json('data.token'))->toBeString()
        ->and($response->json('data.access_token'))->toBeString()
        ->and($response->json('data.refresh_token'))->toBeString()
        ->and($response->json('data.token_type'))->toBe('Bearer')
        ->and($otp->fresh()->consumed)->toBeTrue();
});

test('verifyOtp rejects invalid otp code', function () {
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Verify User',
        'mobile_number' => '9000000002',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    OtpVerification::create([
        'user_id' => $user->id,
        'mobile_number' => '9000000002',
        'code' => '1234',
        'expires_at' => now()->addMinutes(5),
        'consumed' => false,
    ]);

    $response = $this->postJson('/api/auth/verify-otp', [
        'mobile_number' => '9000000002',
        'code' => '9999',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Invalid or expired OTP.',
            'data' => null,
        ]);
});

test('verifyOtp rejects expired otp', function () {
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Verify User',
        'mobile_number' => '9000000003',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    OtpVerification::create([
        'user_id' => $user->id,
        'mobile_number' => '9000000003',
        'code' => '1234',
        'expires_at' => now()->subMinutes(1),
        'consumed' => false,
    ]);

    $response = $this->postJson('/api/auth/verify-otp', [
        'mobile_number' => '9000000003',
        'code' => '1234',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Invalid or expired OTP.',
            'data' => null,
        ]);
});

test('verifyOtp rejects already consumed otp', function () {
    $state = State::create(['name' => 'Kerala', 'code' => 'KL']);
    $user = User::create([
        'name' => 'Verify User',
        'mobile_number' => '9000000004',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    OtpVerification::create([
        'user_id' => $user->id,
        'mobile_number' => '9000000004',
        'code' => '1234',
        'expires_at' => now()->addMinutes(5),
        'consumed' => true,
    ]);

    $response = $this->postJson('/api/auth/verify-otp', [
        'mobile_number' => '9000000004',
        'code' => '1234',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Invalid or expired OTP.',
            'data' => null,
        ]);
});

test('protected route returns user profile when valid jwt bearer token is provided', function () {
    $state = State::create(['name' => 'Goa', 'code' => 'GA']);
    $user = User::create([
        'name' => 'JWT User',
        'mobile_number' => '9888888888',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $token = $jwtService->generateToken($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'User profile retrieved successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => 'JWT User',
                    'mobile_number' => '9888888888',
                ],
            ],
        ]);
});

test('protected route returns 401 when no token is provided', function () {
    $response = $this->getJson('/api/auth/me');

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Unauthenticated.',
            'data' => null,
        ]);
});

test('protected route returns 401 when invalid token is provided', function () {
    $response = $this->withHeader('Authorization', 'Bearer invalid.token.payload')
        ->getJson('/api/auth/me');

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Unauthenticated.',
            'data' => null,
        ]);
});

test('protected route returns 401 when expired token is provided', function () {
    $state = State::create(['name' => 'Goa', 'code' => 'GA']);
    $user = User::create([
        'name' => 'Expired User',
        'mobile_number' => '9777777777',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $token = $jwtService->generateToken($user, -10); // expired 10 minutes ago

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me');

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Unauthenticated.',
            'data' => null,
        ]);
});

test('refreshToken endpoint issues new access and refresh tokens with valid refresh token', function () {
    $state = State::create(['name' => 'Goa', 'code' => 'GA']);
    $user = User::create([
        'name' => 'Refresh User',
        'mobile_number' => '9666666666',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $refreshToken = $jwtService->generateRefreshToken($user);

    $response = $this->postJson('/api/auth/refresh-token', [
        'refresh_token' => $refreshToken,
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'Token refreshed successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => 'Refresh User',
                ],
            ],
        ]);

    expect($response->json('data.token'))->toBeString()
        ->and($response->json('data.access_token'))->toBeString()
        ->and($response->json('data.refresh_token'))->toBeString();

    // Verify the newly issued access token works on protected routes
    $newAccessToken = $response->json('data.access_token');
    $meResponse = $this->withHeader('Authorization', "Bearer {$newAccessToken}")
        ->getJson('/api/auth/me');

    $meResponse->assertOk();
});

test('refreshToken rejects invalid or expired refresh token', function () {
    $response = $this->postJson('/api/auth/refresh-token', [
        'refresh_token' => 'invalid.refresh.token',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Invalid or expired refresh token.',
            'data' => null,
        ]);
});

test('refreshToken rejects access token passed as refresh token', function () {
    $state = State::create(['name' => 'Goa', 'code' => 'GA']);
    $user = User::create([
        'name' => 'Wrong Token User',
        'mobile_number' => '9555555555',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $accessToken = $jwtService->generateAccessToken($user);

    $response = $this->postJson('/api/auth/refresh-token', [
        'refresh_token' => $accessToken,
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Invalid or expired refresh token.',
            'data' => null,
        ]);
});

test('protected route rejects refresh token used as access token', function () {
    $state = State::create(['name' => 'Goa', 'code' => 'GA']);
    $user = User::create([
        'name' => 'Protected Test User',
        'mobile_number' => '9444444444',
        'country_code' => '+91',
        'state_id' => $state->id,
        'terms_accepted' => true,
    ]);

    $jwtService = app(JwtService::class);
    $refreshToken = $jwtService->generateRefreshToken($user);

    $response = $this->withHeader('Authorization', "Bearer {$refreshToken}")
        ->getJson('/api/auth/me');

    $response
        ->assertStatus(401)
        ->assertJson([
            'hasError' => true,
            'errorCode' => 401,
            'message' => 'Unauthenticated.',
            'data' => null,
        ]);
});
