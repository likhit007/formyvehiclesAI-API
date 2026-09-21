<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('signup returns the expected api envelope and creates a user', function () {
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
        ->and($response->json('data.user.name'))->toBe('Lucy');
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
        ->and($response->json('message'))->toContain('not found');
});
