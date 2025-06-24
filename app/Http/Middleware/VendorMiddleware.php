<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request->route('product'));
        // $product = $request->route('product');
        // $service = $request->route('service');
        if (!$request->user() || !$request->user()->isVendor()) {
            return responseError('Unauthorized access', 403);
        }

        // if ($product && ($product->user_id !== $request->user()->id)) {
        //     return responseError('Unauthorized access', 403);
        // }

        // if ($service && ($service->user_id !== $request->user()->id)) {
        //     return responseError('Unauthorized access', 403);
        // }
        return $next($request);
    }
}
