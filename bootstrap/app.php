<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\UserAuth;
use App\Http\Middleware\ValidateNumericParameter;
use App\Http\Middleware\RejectWebOnApi;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // API domain routes
            Route::middleware('api')
                ->domain(env('API_DOMAIN'))
                ->group(base_path('routes/api.php'));

            // Web/Admin routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },

            // $host = request()->getHost();

            /*
            |--------------------------------------------------------------------------
            | API DOMAIN → load API routes only
            |--------------------------------------------------------------------------
            */
            // if (in_array($host, config('app.api_domains'))) {
            // if($host == config('app.api_domain')) {
            //     Route::middleware('api')
            //         ->prefix('v1') // optional but recommended
            //         ->group(base_path('routes/api.php'));

            //     return;
            // }
            // Load API routes with domain constraint

            // Route::middleware('api')
            //     ->prefix('v1')
            //     ->domain(config('app.api_domain'))
            //     ->group(base_path('routes/api.php'));

            /*
            |--------------------------------------------------------------------------
            | WEB / ADMIN / MAIN DOMAIN → load web routes only
            |--------------------------------------------------------------------------
            */
            // Route::middleware('web')
            //     ->group(base_path('routes/web.php'));

            // Load web routes only on non-API domains

            // if($host != config('app.api_domain')) {
            //     Route::middleware('web')
            //         ->group(base_path('routes/web.php'));
            // }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // $middleware->append(RejectWebOnApi::class);

        // Add redirect configuration for unauthenticated users
        $middleware->redirectGuestsTo('/login');
        
        $middleware->alias([
            'UserAuth'    => UserAuth::class,
            'NumericParam'=> ValidateNumericParameter::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         // api: __DIR__.'/../routes/api.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//         then: function ($router) {
//             Route::prefix('api')
//                 ->namespace('App\Http\Controllers')
//                 ->group(base_path('routes/api.php'));
//         } 
//     )
//     ->withMiddleware(function (Middleware $middleware): void {
//         $middleware->alias([
//             'UserAuth' => UserAuth::class,
//             'NumericParam' => ValidateNumericParameter::class
//         ]);
//     })
//     ->withExceptions(function (Exceptions $exceptions): void {
//         //
//     })->create();
