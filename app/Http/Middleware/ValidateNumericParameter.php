<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateNumericParameter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$params): Response
    {
        foreach ($params as $param) {
            $value = $request->route($param);
    
            if (!ctype_digit((string) $value)) {
                return response()->json([
                    'statusCode' => 422,
                    'error' => "$param must be numeric"
                ], 422);
            }
        }

        return $next($request);
    }
}
