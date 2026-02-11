<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleController;

use App\Http\Controllers\UtilityController;

Route::domain(config('app.api_domain'))->group(function () {
    // dd('api route '.config('app.api_domain'));
    Route::group(['prefix' => '/auth'], function () {
        Route::post('/login', [AuthController::class, "login"]);
        Route::post('/refresh_token', [AuthController::class, "refreshToken"]);
        Route::post('/send_email_verification_mail', [AuthController::class, "sendVerificationMail"]);
        Route::post('/verify_email_token', [AuthController::class, "verifyEmailToken"]);
        Route::post('/register', [AuthController::class, "register"]);

        Route::post('/google', [GoogleController::class, 'loginOrRegister']);
    });

    Route::group(['prefix' => '/utilities'], function () {
        Route::get("/states", [UtilityController::class, "states"]);
    });

    // Route::group(['prefix' => '/users'], function () {
    //     // Route::post("", [UserController::class, "save"]);
    // });

    require __DIR__ . '/api/users.php';
    require __DIR__ . '/api/providers.php';
});
