<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IdempotencyKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->isMethod('POST') && !$request->isMethod('PATCH')) {
            return $next($request);
        }

        $key = $request->header('Idempotency-Key');
        if (!$key) {
            return $next($request);
        }

        $cacheKey = 'idempotency_' . auth()->id() . '_' . $key;

        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            return response()->json($cachedResponse['content'], $cachedResponse['status'], $cachedResponse['headers']);
        }

        $response = $next($request);

        if ($response->status() >= 200 && $response->status() < 300) {
            Cache::put($cacheKey, [
                'content' => json_decode($response->getContent(), true),
                'status' => $response->status(),
                'headers' => $response->headers->all()
            ], now()->addHours(24));
        }

        return $response;
    }
}
