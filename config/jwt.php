<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign the tokens. By default it uses the APP_KEY.
    |
    */
    'secret' => env('JWT_SECRET', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | The hashing algorithm used to sign the JWT.
    | Supported: "HS256"
    |
    */
    'algo' => 'HS256',

    /*
    |--------------------------------------------------------------------------
    | JWT Access Token Time to Live (TTL)
    |--------------------------------------------------------------------------
    |
    | The lifetime of the access token in minutes. Default is 60 minutes.
    |
    */
    'ttl' => (int) env('JWT_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | JWT Refresh Token Time to Live (TTL)
    |--------------------------------------------------------------------------
    |
    | The lifetime of the refresh token in minutes. Default is 30 days (43200 minutes).
    |
    */
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 43200),
];
