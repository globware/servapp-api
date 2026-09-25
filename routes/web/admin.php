<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
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

    
    Route::group(['prefix' => '/leads'], function () {
        Route::get('', [LeadController::class, 'index'])->name('admin.leads.index');
        Route::post('{leadId}/approve', [LeadController::class, 'approve'])->name('admin.leads.approve');
        Route::post('{leadId}/reject', [LeadController::class, 'reject'])->name('admin.leads.reject');
    });

    Route::group(['prefix' => '/services'], function () {
        Route::get('', [DashboardController::class, 'services'])->name('admin.services.index');
        Route::get('{serviceId}', [ServiceController::class, 'show'])->name('admin.services.show');
        Route::post('{serviceId}/approve', [ServiceController::class, 'approve'])->name('admin.services.approve');
        Route::patch('{serviceId}/verify', [ServiceController::class, 'verify'])->name('admin.services.verify');
    });
});