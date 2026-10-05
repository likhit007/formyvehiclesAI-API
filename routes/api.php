<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\VehicleModelController;
use App\Http\Controllers\Api\VehicleTypeController;
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
    Route::apiResource('/user/vehicles', VehicleController::class);

    Route::prefix('vehicle')->group(function () {
        Route::match(['get', 'post'], '/vehicle-types-list', [VehicleTypeController::class, 'index'])->name('vehicle-types.list');
        Route::get('/vehicle-types', [VehicleTypeController::class, 'index'])->name('vehicle-types.index');

        Route::match(['get', 'post'], '/brands-list', [BrandController::class, 'index'])->name('brands.list');
        Route::get('/vehicle-brands', [BrandController::class, 'index'])->name('brands.index');

        Route::match(['get', 'post'], '/vehicle-models-list', [VehicleModelController::class, 'index'])->name('vehicle-models.list');
        Route::get('/vehicle-models', [VehicleModelController::class, 'index'])->name('vehicle-models.index');
    });
});
