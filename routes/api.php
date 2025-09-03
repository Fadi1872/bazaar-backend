<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BazaarCategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreCategoryController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

//? Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //? Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/profileImage', [AuthController::class, 'deleteProfileImage']);
    
    // Profile Routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'update']);

    // Addresses Routes
    Route::apiResource('/address', AddressController::class);
    Route::get("/addresses", [AddressController::class, 'userAddresses']);

    // Store Category Routes
    Route::apiResource('/store-categories', StoreCategoryController::class)->except(['show', 'update']);

    // Store Routes
    Route::apiResource('/stores', StoreController::class);
    Route::post('/stores/{store}/comment', [StoreController::class, 'addComment']);
    Route::get('/stores/{store}/comments', [StoreController::class, 'comments']);
    Route::get('/stores/{store}/products', [StoreController::class, 'getCategoryProducts']);

    // Comment Routes
    Route::apiResource('/comments', CommentController::class)->except(['index', 'store', 'show']);
    Route::post('comments/{comment}/like', [CommentController::class, 'like']);
    Route::delete('comments/{comment}/like', [CommentController::class, 'unlike']);

    // Product Category Routes
    Route::apiResource('/product-categories', ProductCategoryController::class)->except(['show', 'update']);

    // Products Routes
    Route::apiResource('/products', ProductController::class);
    Route::post('/products/{product}/comment', [ProductController::class, 'addComment']);
    Route::get('/products/{product}/comments', [ProductController::class, 'comments']);

    // Bazaar Category Routes
    Route::apiResource('/bazaar-categories', BazaarCategoryController::class)->except(['show', 'update']);
});
