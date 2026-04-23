<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleController;

use App\Http\Controllers\UtilityController;
use App\Http\Controllers\ServiceCategoryController;

use App\Http\Middleware\LogRequestUrl;

Route::domain(config('app.api_domain'))->group(function () {
    // dd('api route '.config('app.api_domain'));
    

    Route::group(['prefix' => '/auth'], function () {
        Route::post('/login', [AuthController::class, "login"]);
        Route::post('/refresh_token', [AuthController::class, "refreshToken"]);
        Route::post('/send_email_verification_mail', [AuthController::class, "sendVerificationMail"]);
        Route::post('/verify_email_token', [AuthController::class, "verifyEmailToken"]);
        Route::post('/register', [AuthController::class, "register"]);

        Route::post('/send_password_reset_code', [AuthController::class, "sendPasswordResetCode"]);
        Route::post('/verify_password_reset_code', [AuthController::class, "verifyPasswordResetToken"]);
        Route::post('/reset_password', [AuthController::class, "resetPassword"]);

        Route::post('/google', [GoogleController::class, 'loginOrRegister']);
    });

    Route::group(['middleware' => 'UserAuth', 'prefix' => '/user'], function () {
        Route::post('/save_fcm_token', [UserController::class, "saveFcmToken"]);
    });

    Route::group(['middleware' => 'UserAuth', 'prefix' => '/auth'], function () {
        Route::post('/send_email_verification_mail', [AuthController::class, "sendVerificationMail"]);
        Route::post('/verify_email_token', [AuthController::class, "verifyEmailToken"]);
    });

    Route::group(['prefix' => '/utilities'], function () {
        Route::get("/states", [UtilityController::class, "states"]);
    });

    Route::group(['middleware' => 'UserAuth', 'prefix' => '/service_categories'], function () {
        Route::get("", [ServiceCategoryController::class, "categories"]);
    });

    // Route::group(['prefix' => '/users'], function () {
    //     // Route::post("", [UserController::class, "save"]);
    // });

    require __DIR__ . '/api/users.php';
    require __DIR__ . '/api/providers.php';
});
