<?php

use App\Enums\AppPolicyType;

test('returns terms text via post when type is 1', function () {
    $response = $this->postJson('/api/generic/app-policy', [
        'type' => 1,
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                'content',
            ],
        ]);

    expect($response->json('hasError'))->toBeFalse()
        ->and($response->json('errorCode'))->toBe(0)
        ->and($response->json('data.content'))->toBe(AppPolicyType::Terms->content());
});

test('returns privacy policy text via post when type is 2', function () {
    $response = $this->postJson('/api/generic/app-policy', [
        'type' => 2,
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'hasError',
            'errorCode',
            'message',
            'data' => [
                'content',
            ],
        ]);

    expect($response->json('hasError'))->toBeFalse()
        ->and($response->json('errorCode'))->toBe(0)
        ->and($response->json('data.content'))->toBe(AppPolicyType::PrivacyPolicy->content());
});

test('returns terms text via get when type is 1', function () {
    $response = $this->getJson('/api/generic/app-policy?type=1');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'data' => [
                'content' => AppPolicyType::Terms->content(),
            ],
        ]);
});

test('returns privacy policy text via get when type is 2', function () {
    $response = $this->getJson('/api/generic/app-policy?type=2');

    $response
        ->assertOk()
        ->assertJson([
            'hasError' => false,
            'errorCode' => 0,
            'data' => [
                'content' => AppPolicyType::PrivacyPolicy->content(),
            ],
        ]);
});

test('fails validation when type is missing', function () {
    $response = $this->postJson('/api/generic/app-policy', []);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

test('fails validation when type is invalid value', function () {
    $response = $this->postJson('/api/generic/app-policy', [
        'type' => 3,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['type']);

    $responseString = $this->postJson('/api/generic/app-policy', [
        'type' => 'invalid',
    ]);

    $responseString
        ->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});
