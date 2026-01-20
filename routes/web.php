<?php


use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::domain(config('app.admin_domain'))->group(function () {
    // Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__ . '/web/admin.php';
});

