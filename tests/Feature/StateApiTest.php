<?php

use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('states-list api returns the list of states via GET', function () {
    State::create(['name' => 'Karnataka', 'code' => 'KA']);
    State::create(['name' => 'Maharashtra', 'code' => 'MH']);

    $response = $this->getJson('/api/states-list');

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
                    'code',
                ],
            ],
        ]);

    expect($response->json('hasError'))->toBeFalse()
        ->and($response->json('errorCode'))->toBe(0)
        ->and($response->json('data'))->toHaveCount(2)
        ->and($response->json('data.0.name'))->toBe('Karnataka')
        ->and($response->json('data.1.name'))->toBe('Maharashtra');
});

test('states-list api returns the list of states via POST', function () {
    State::create(['name' => 'Goa', 'code' => 'GA']);

    $response = $this->postJson('/api/states-list');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'States retrieved successfully.',
        ]);

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Goa');
});

test('states-list api filters states by search query', function () {
    State::create(['name' => 'Karnataka', 'code' => 'KA']);
    State::create(['name' => 'Kerala', 'code' => 'KL']);
    State::create(['name' => 'Tamil Nadu', 'code' => 'TN']);

    $response = $this->getJson('/api/states-list?search=karn');

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Karnataka');

    $responseCode = $this->getJson('/api/states-list?search=TN');

    $responseCode->assertOk();

    expect($responseCode->json('data'))->toHaveCount(1)
        ->and($responseCode->json('data.0.name'))->toBe('Tamil Nadu');
});

test('states-list returns empty array when no states exist', function () {
    $response = $this->getJson('/api/states-list');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'message' => 'States retrieved successfully.',
            'data' => [],
        ]);
});
