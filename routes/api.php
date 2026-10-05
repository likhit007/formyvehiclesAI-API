<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StateController;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/states-list', [StateController::class, 'index'])->name('states.list');
Route::get('/states', [StateController::class, 'index'])->name('states.index');

Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('auth.refresh');
    Route::post('/refresh', [AuthController::class, 'refreshToken']);

    Route::middleware('jwt.auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    });
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('/user', [AuthController::class, 'me'])->name('user.profile');
});
