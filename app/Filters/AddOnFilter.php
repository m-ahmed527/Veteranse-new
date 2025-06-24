<?php

namespace App\Filters;

use Closure;

class AddOnFilter
{
    /**
     * Create a new class instance.
     */
    public function handle($query, Closure $next)
    {

        // // Apply price range filter
        if (request()->has('add_on') && request()->has('max_price')) {
            $query->whereHas('addOns', function ($q) {
                $q->where('name', "like", "%" . request('add_on') . "%");
            });
        } else {
            return $next($query);
        }


        return $next($query);
    }
}
