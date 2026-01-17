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
        if ($request->getHost() === config('app.api_domain') &&
            ! preg_match('#^v\d+/#', ltrim($request->path(), '/'))) {
                dd($request->getHost().' === '.config('app.api_domain'). ' and '. $request->path(). ' = ' . ! preg_match('#^v\d+/#', ltrim($request->path(), '/')));
            abort(404);
        }

        return $next($request);
    }
}
