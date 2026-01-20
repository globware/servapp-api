<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\Provider\MessageController;
use App\Http\Controllers\Provider\ServiceRequestController;
use App\Http\Controllers\Provider\ComplaintController;

use App\Http\Controllers\UtilityController;

Route::group(['middleware' => 'UserAuth', 'prefix' => '/provider', 'namespace' => 'Provider',], function () {
    Route::group(['prefix' => '/services'], function () {
        Route::group(['prefix' => '/requests'], function () {
            Route::get("/{requestId}", [ServiceRequestController::class, "getRequest"])->middleware('NumericParam:requestId');
            Route::post("/send_message", [ServiceRequestController::class, "sendMessage"]);
            Route::get("/chat_messages/{requestId}", [ServiceRequestController::class, "getRequestChats"])->middleware('NumericParam:requestId');
        });
        Route::get("", [ServiceController::class, "services"]);
        Route::post("/add", [ServiceController::class, "save"]);
        Route::post("/save_media", [ServiceController::class, "saveMedia"]);
        Route::delete("/media/{mediaId}", [ServiceController::class, "deleteMedia"])->middleware('NumericParam:mediaId');
        Route::patch("/add_media/{serviceId}", [ServiceController::class, "addServiceMedia"])->middleware('NumericParam:serviceId');
        Route::patch("/add_tag/{serviceId}", [ServiceController::class, "addServiceTags"])->middleware('NumericParam:serviceId');
        Route::delete("/remove_tag/{serviceId}/{tagId}", [ServiceController::class, "removeTag"])->middleware('NumericParam:serviceId,tagId');
    
        Route::group(['prefix' => '/{serviceId}'], function () {
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
});
