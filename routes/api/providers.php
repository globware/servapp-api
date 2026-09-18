<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\Provider\MessageController;
use App\Http\Controllers\Provider\ServiceRequestController;
use App\Http\Controllers\Provider\ComplaintController;

use App\Http\Controllers\UtilityController;

use App\Http\Controllers\Provider\ProductController;
use App\Http\Controllers\Provider\ProductRequestController;
use App\Http\Controllers\Provider\ClaimController;

Route::group(['middleware' => 'UserAuth', 'prefix' => '/provider', 'namespace' => 'Provider',], function () {
    Route::group(['prefix' => '/services'], function () {
        Route::group(['prefix' => '/requests'], function () {
            Route::get("", [ServiceRequestController::class, "getRequests"]);
            Route::get("/stats", [ServiceRequestController::class, "stats"]);
            Route::get("/stats/{serviceId}", [ServiceRequestController::class, "stats"])->middleware('NumericParam:serviceId');
            Route::post("/send_message", [ServiceRequestController::class, "sendMessage"]);
            Route::get("/chat_messages/{requestId}", [ServiceRequestController::class, "getRequestChats"])->middleware('NumericParam:requestId');
            Route::post("/accept/{requestId}", [ServiceRequestController::class, "accept"])->middleware('NumericParam:requestId');
            Route::patch("/complete/{requestId}", [ServiceRequestController::class, "completed"])->middleware('NumericParam:requestId');
            Route::patch("/treat_completed/{requestId}", [ServiceRequestController::class, "treatCompleted"])->middleware('NumericParam:requestId');
            Route::get("/{requestId}", [ServiceRequestController::class, "getRequest"])->middleware('NumericParam:requestId');
        });
        Route::get("", [ServiceController::class, "services"]);
        Route::post("/add", [ServiceController::class, "save"]);
        Route::post("/save_media", [ServiceController::class, "saveMedia"]);
        Route::delete("/media/{mediaId}", [ServiceController::class, "deleteMedia"])->middleware('NumericParam:mediaId');
        Route::patch("/add_media/{serviceId}", [ServiceController::class, "addServiceMedia"])->middleware('NumericParam:serviceId');
        Route::patch("/add_tag/{serviceId}", [ServiceController::class, "addServiceTags"])->middleware('NumericParam:serviceId');
        Route::patch("/toggle_activate/{serviceId}", [ServiceController::class, "toggleActivate"])->middleware('NumericParam:serviceId');
        Route::delete("/remove_tag/{serviceId}/{tagId}", [ServiceController::class, "removeTag"])->middleware('NumericParam:serviceId,tagId');
    
        Route::group(['prefix' => '/{serviceId}'], function () {
            Route::delete("", [ServiceController::class, "delete"])->middleware('NumericParam:serviceId');
            Route::post("", [ServiceController::class, "update"])->middleware('NumericParam:serviceId');
            Route::get("", [ServiceController::class, "service"])->middleware('NumericParam:serviceId');


            //Messages
            Route::group(['prefix' => '/messages'], function () {
                Route::get("", [MessageController::class, "conversations"]);
                Route::post("/send_message", [MessageController::class, "sendMessage"]);
                Route::patch("/read_messages/{userId}", [MessageController::class, "readMessage"]);
            });
        });

        Route::group(['prefix' => '/complaints'], function () {
            Route::post("/save", [ComplaintController::class, "save"]);
        });
    });

    // MVP Product Routes
    Route::group(['prefix' => '/products'], function () {
        Route::get("", [ProductController::class, "index"]);
        Route::post("", [ProductController::class, "store"]);
        Route::patch("/{id}", [ProductController::class, "update"])->middleware('NumericParam:id');
        Route::delete("/{id}", [ProductController::class, "destroy"])->middleware('NumericParam:id');
    });

    Route::group(['prefix' => '/product_requests'], function () {
        Route::get("", [ProductRequestController::class, "index"]);
        Route::get("/{id}", [ProductRequestController::class, "show"])->middleware('NumericParam:id');
        Route::patch("/{id}/accept", [ProductRequestController::class, "accept"])->middleware('NumericParam:id');
        Route::patch("/{id}/decline", [ProductRequestController::class, "decline"])->middleware('NumericParam:id');
        Route::patch("/{id}/fulfill", [ProductRequestController::class, "fulfill"])->middleware('NumericParam:id');
        Route::get("/{id}/chats", [ProductRequestController::class, "getChats"])->middleware('NumericParam:id');
        Route::post("/{id}/chats", [ProductRequestController::class, "sendMessage"])->middleware('NumericParam:id');
    });

    // MVP Claiming Engine
    Route::post('/claim/{leadId}', [ClaimController::class, 'claim'])->middleware('NumericParam:leadId');
});
