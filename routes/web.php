<?php

use App\Http\Middleware\LogRequestUrl;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::domain(config('app.admin_domain'))->middleware(LogRequestUrl::class)->group(function () {
    // Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__ . '/web/admin.php';
});

