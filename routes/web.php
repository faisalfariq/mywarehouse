<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppLogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MutationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Simple Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {

    Route::prefix('web')->group(function () {
        Route::resources([
            'app-logs' => AppLogController::class,
            'products' => ProductController::class,
            'locations' => LocationController::class,
            'mutations' => MutationController::class,
            'users' => \App\Http\Controllers\UserController::class,
        ], ['names' => [
            'app-logs' => 'web.app-logs',
            'products' => 'web.products',
            'locations' => 'web.locations',
            'mutations' => 'web.mutations',
            'users' => 'web.users',
        ]]);
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
