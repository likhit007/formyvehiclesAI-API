<?php

use App\Http\Controllers\Api\VehicleWorkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/vehicle/add-info', [VehicleWorkController::class, 'store'])->middleware('jwt.auth');
Route::match(['get', 'post'], '/vehicle/info-types', [VehicleWorkController::class, 'infoTypes'])->middleware('jwt.auth');
Route::match(['get', 'post'], '/vehicle/info-list', [VehicleWorkController::class, 'infoList'])->middleware('jwt.auth');
