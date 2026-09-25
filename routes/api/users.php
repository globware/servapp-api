<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\ServiceController;
use App\Http\Controllers\User\IndexController;
use App\Http\Controllers\User\ProviderController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\ServiceRequestController;

use App\Http\Controllers\UtilityController;

use App\Http\Controllers\User\BlockController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\ProductRequestController;
use App\Http\Controllers\User\ScoutController;

Route::group(['middleware' => 'UserAuth', 'prefix' => '/user', 'namespace' => 'User',], function () {
    Route::get("/dashboard", [IndexController::class, "dashboard"]);
    
    Route::group(['prefix' => '/service_categories'], function () {
        Route::get("", [ServiceController::class, "getServices"]);
        Route::get("/get_by_location", [ServiceController::class, "getServicesByLocation"]);
        Route::get("/{serviceId}", [ServiceController::class, "getService"]);
    });
    Route::group(['prefix' => '/user_services'], function () {
        Route::get("", [ServiceController::class, "getUserServices"]);
        Route::group(['prefix' => '/requests'], function () {
            Route::get("", [ServiceRequestController::class, "getRequests"]);
            Route::post("/make_request", [ServiceRequestController::class, "requestService"]);
            Route::patch("/{requestId}/accept_quote", [ServiceRequestController::class, "acceptQuote"])->middleware("NumericParam:requestId");
            Route::patch("/cancel/{requestId}", [ServiceRequestController::class, "cancel"])->middleware('NumericParam:requestId');
            Route::get("/{requestId}", [ServiceRequestController::class, "getRequest"])->middleware('NumericParam:requestId');
            Route::post("/send_message", [ServiceRequestController::class, "sendMessage"]);
            Route::patch("/complete/{requestId}", [ServiceRequestController::class, "completed"]);
            Route::patch("/treat_completed/{requestId}", [ServiceRequestController::class, "treatCompleted"])->middleware('NumericParam:requestId');
            Route::patch("/feedback/{id}", [ServiceRequestController::class, "feedback"])->middleware('NumericParam:id');
            Route::get("/chat_messages/{requestId}", [ServiceRequestController::class, "getRequestChats"])->middleware('NumericParam:requestId');
        });

        Route::post("/send_message", [ServiceController::class, "sendMessage"]);
        Route::get("/{userServiceId}", [ServiceController::class, "getUserService"]);
        Route::patch("/read_messages/{serviceId}", [ServiceController::class, "readMessage"]);
        Route::post("/complain", [ServiceController::class, "complain"]);
    });
    Route::group(['prefix' => '/providers'], function () {
        Route::get("", [ProviderController::class, "providers"]);
    });
    
    Route::group(['prefix' => '/block'], function () {
        Route::get('', [BlockController::class, 'index']);
        Route::post('/{userId}', [BlockController::class, 'block'])->middleware('NumericParam:userId');
        Route::delete('/{userId}', [BlockController::class, 'unblock'])->middleware('NumericParam:userId');
    });

    Route::group(['prefix' => '/messages'], function () {
        Route::get("/conversations", [MessageController::class, "conversations"]);
    });

    // MVP Product Routes
    Route::group(['prefix' => '/products'], function () {
        Route::get("", [ProductController::class, "index"]);
        Route::get("/{id}", [ProductController::class, "show"])->middleware('NumericParam:id');
    });

    Route::group(['prefix' => '/product_requests'], function () {
        Route::post("", [ProductRequestController::class, "store"]);
        Route::get("", [ProductRequestController::class, "index"]);
        Route::get("/{id}", [ProductRequestController::class, "show"])->middleware('NumericParam:id');
        Route::patch("/{id}/confirm", [ProductRequestController::class, "confirm"])->middleware('NumericParam:id');
        Route::patch("/{id}/cancel", [ProductRequestController::class, "cancel"])->middleware('NumericParam:id');
        Route::post("/{id}/review", [ProductRequestController::class, "review"])->middleware('NumericParam:id');
        Route::patch("/{id}/read", [ProductRequestController::class, "read"])->middleware('NumericParam:id');
        Route::get("/{id}/chats", [ProductRequestController::class, "getChats"])->middleware('NumericParam:id');
        Route::post("/{id}/chats", [ProductRequestController::class, "sendMessage"])->middleware('NumericParam:id');
    });

    // MVP Scouting Engine
    Route::post('/scout/suggest', [ScoutController::class, 'suggest']);
    Route::get('/scout/suggestions', [ScoutController::class, 'suggestions']);
});
