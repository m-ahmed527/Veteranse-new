<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd(123);
        if (auth()->guard('sanctum')->guest()) {
            // Agar request JSON expect karti hai, to custom JSON response return karein
            if ($request->expectsJson()) {
                // dd('Guest user detected in AuthenticateApi middleware.');
                return response()->json([
                    'success' => false,
                    'message' => 'You are not aunthenticated.',
                ], 401);
            }

            // Agar request JSON nahi expect karti, to default behavior follow karein
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
