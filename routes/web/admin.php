<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceController;

Route::middleware('guest:web')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('admin.login');
    Route::post('login', [AuthController::class, 'authenticate'])->name('admin.login.store');
});

Route::middleware('auth:web')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('', [DashboardController::class, 'index'])->name('admin.index');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Route::get('users', DashboardController::class)->only(['index']);
    // Route::resource('services', DashboardController::class)->only(['index']);

    Route::group(['prefix' => '/users'], function () {
        Route::get('', [DashboardController::class, 'users'])->name('admin.users.index');
    });

    Route::group(['prefix' => '/services'], function () {
        Route::get('', [DashboardController::class, 'services'])->name('admin.services.index');
        Route::get('{serviceId}', [ServiceController::class, 'show'])->name('admin.services.show');
        Route::patch('{serviceId}/verify', [DashboardController::class, 'verifyService'])->name('admin.services.verify');
    });
});