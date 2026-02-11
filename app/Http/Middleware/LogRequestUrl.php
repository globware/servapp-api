<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequestUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        dd([
            'full_url' => $request->fullUrl(),
            'url' => $request->url(),
            'path' => $request->path(),
            'method' => $request->method(),
            'host' => $request->getHost(),
            'scheme' => $request->getScheme(),
        ]);
        return $next($request);
    }
}
