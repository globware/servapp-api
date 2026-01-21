<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RejectWebOnApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow request-docs and health check routes
        $allowedPaths = [
            'request-docs',
            'up', // health check
        ];

        $path = ltrim($request->path(), '/');

        // Check if the path starts with any allowed path
        foreach ($allowedPaths as $allowedPath) {
            if (str_starts_with($path, $allowedPath)) {
                return $next($request);
            }
        }

        // Block non-versioned API routes on API domain
        if ($request->getHost() === config('app.api_domain') &&
            ! preg_match('#^v\d+/#', $path)) {
            abort(404);
        }
        // if ($request->getHost() === config('app.api_domain') &&
        //     ! preg_match('#^v\d+/#', ltrim($request->path(), '/'))) {
        //         dd($request->getHost().' === '.config('app.api_domain'). ' and '. $request->path(). ' = ' . ! preg_match('#^v\d+/#', ltrim($request->path(), '/')));
        //     abort(404);
        // }

        return $next($request);
    }
}
