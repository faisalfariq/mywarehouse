<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\MutationApiController;
use App\Http\Controllers\Api\AppLogApiController;

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthApiController::class, 'login']);
    Route::post('/auth/register', [AuthApiController::class, 'register']);
});

// Protected routes (authentication required)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/me', [AuthApiController::class, 'me']);
    
    // User management
    Route::apiResource('users', UserApiController::class, ['names' => [
        'index' => 'api.users.index',
        'store' => 'api.users.store',
        'show' => 'api.users.show',
        'update' => 'api.users.update',
        'destroy' => 'api.users.destroy',
    ]]);
    
    // Product management
    Route::apiResource('products', ProductApiController::class, ['names' => [
        'index' => 'api.products.index',
        'store' => 'api.products.store',
        'show' => 'api.products.show',
        'update' => 'api.products.update',
        'destroy' => 'api.products.destroy',
    ]]);
    
    // Location management
    Route::apiResource('locations', LocationApiController::class, ['names' => [
        'index' => 'api.locations.index',
        'store' => 'api.locations.store',
        'show' => 'api.locations.show',
        'update' => 'api.locations.update',
        'destroy' => 'api.locations.destroy',
    ]]);
    
    // Mutation management
    Route::apiResource('mutations', MutationApiController::class, ['names' => [
        'index' => 'api.mutations.index',
        'store' => 'api.mutations.store',
        'show' => 'api.mutations.show',
        'update' => 'api.mutations.update',
        'destroy' => 'api.mutations.destroy',
    ]]);
    
    // AppLog management (read-only)
    Route::get('app-logs', [AppLogApiController::class, 'index'])->name('api.app-logs.index');
    Route::get('app-logs/{id}', [AppLogApiController::class, 'show'])->name('api.app-logs.show');
}); 