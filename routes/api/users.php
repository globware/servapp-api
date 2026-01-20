<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\ServiceController;
use App\Http\Controllers\User\IndexController;
use App\Http\Controllers\User\ProviderController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\ServiceRequestController;

use App\Http\Controllers\UtilityController;

Route::group(['middleware' => 'UserAuth', 'prefix' => '/user', 'namespace' => 'User',], function () {
    Route::get("/dashboard", [IndexController::class, "dashboard"]);
    Route::get("/logged_in_user", [IndexController::class, "loggedInUser"]);
    Route::group(['prefix' => '/services'], function () {
        Route::get("", [ServiceController::class, "getServices"]);
        Route::get("/get_by_location", [ServiceController::class, "getServicesByLocation"]);
        Route::get("/{serviceId}", [ServiceController::class, "getService"]);
    });
    Route::group(['prefix' => '/user_services'], function () {
        Route::group(['prefix' => '/requests'], function () {
            Route::post("/make_request", [ServiceRequestController::class, "requestService"]);
            Route::get("/{requestId}", [ServiceRequestController::class, "getRequest"])->middleware('NumericParam:requestId');
            Route::post("/send_message", [ServiceRequestController::class, "sendMessage"]);
            Route::get("/chat_messages/{requestId}", [ServiceRequestController::class, "getRequestChats"])->middleware('NumericParam:requestId');
        });

        Route::post("/send_message", [ServiceController::class, "sendMessage"]);
        Route::get("/{userServiceId}", [ServiceController::class, "getUserService"]);
        Route::post("/review", [ServiceController::class, "review"]);
        Route::post("/rate", [ServiceController::class, "rate"]);
        Route::patch("/read_messages/{serviceId}", [ServiceController::class, "readMessage"]);
        Route::post("/complain", [ServiceController::class, "complain"]);
    });
    Route::group(['prefix' => '/providers'], function () {
        Route::get("", [ProviderController::class, "providers"]);
    });
    Route::group(['prefix' => '/messages'], function () {
        Route::get("/conversations", [MessageController::class, "conversations"]);
    });
});
