<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BazaarCategoryController;
use App\Http\Controllers\Api\BazaarController;
use App\Http\Controllers\Api\BazaarJoinRequestController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckOutController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PaymentMethodController;
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
    Route::get('/own/store', [StoreController::class, 'ownStore']);
    Route::get('/stores/{store}/products', [StoreController::class, 'getCategoryProducts']);
    Route::post('/stores/{store}/comment', [StoreController::class, 'addComment']);
    Route::get('/stores/{store}/comments', [StoreController::class, 'comments']);
    Route::post('/stores/{store}/favorite', [StoreController::class, 'toggleStore']);

    // Comment Routes
    Route::apiResource('/comments', CommentController::class)->except(['index', 'store', 'show']);
    Route::post('comments/{comment}/like', [CommentController::class, 'like']);
    Route::delete('comments/{comment}/like', [CommentController::class, 'unlike']);

    // Product Category Routes
    Route::apiResource('/product-categories', ProductCategoryController::class)->except(['show', 'update']);

    // Products Routes
    Route::apiResource('/products', ProductController::class);
    Route::get('/own/products', [ProductController::class, 'ownProducts']);
    Route::post('/products/{product}/comment', [ProductController::class, 'addComment']);
    Route::get('/products/{product}/comments', [ProductController::class, 'comments']);
    Route::post('/products/{product}/favorite', [ProductController::class, 'toggleProduct']);

    // Bazaar Category Routes
    Route::apiResource('/bazaar-categories', BazaarCategoryController::class)->except(['show', 'update']);

    // Bazaar Routes
    Route::apiResource('/bazaars', BazaarController::class);
    Route::get('/own/bazaars', [BazaarController::class, 'MyBazaars']);
    Route::post('/bazaars/{bazaar}/comment', [BazaarController::class, 'addComment']);
    Route::get('/bazaars/{bazaar}/comments', [BazaarController::class, 'comments']);
    Route::get('/bazaars/{bazaar}/products', [BazaarController::class, 'getCategoryProducts']);
    Route::post('/bazaars/{bazaar}/favorite', [BazaarController::class, 'toggleBazaar']);
    Route::get('/own/bazaars', [BazaarController::class, 'ownBazaar']);

    Route::get('bazaars/{bazaar}/requests', [BazaarJoinRequestController::class, 'index']);

    // Bazaar Join Request Routes
    Route::post('bazaars/{bazaar}/requests', [BazaarJoinRequestController::class, 'store']);
    Route::get('requests/{joinRequest}', [BazaarJoinRequestController::class, 'show']);
    Route::put('requests/{joinRequest}', [BazaarJoinRequestController::class, 'update']);
    Route::delete('requests/{joinRequest}', [BazaarJoinRequestController::class, 'destroy']);
    Route::post('requests/{joinRequest}/accept', [BazaarJoinRequestController::class, 'accept']);
    Route::post('requests/{joinRequest}/reject', [BazaarJoinRequestController::class, 'reject']);

    // Cart Routes
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add/{product}', [CartController::class, 'addItem']);
    Route::post('/remove/{product}', [CartController::class, 'removeOne']);
    Route::delete('/remove/{product}', [CartController::class, 'deleteItem']);

    // Checkout Routes
    Route::post('/checkout', [CheckOutController::class, 'store']);

    // Payment Methods Routes
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
});
