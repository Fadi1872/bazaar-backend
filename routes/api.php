<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

//? Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //? Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile Routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'update']);

    // Addresses Routes
    Route::apiResource('/address', AddressController::class);
    Route::get("/addresses", [AddressController::class, 'userAddresses']);

    // Store Routes
    Route::apiResource('/stores', StoreController::class);
});
