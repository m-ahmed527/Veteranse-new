<?php

namespace App\Filters;

use Closure;

class CategoryFilter
{
    public function handle($query, Closure $next)
    {

        // // Apply price range filter
        if (request()->has('category')) {
            $query->whereHas('category', function ($q) {
                $q->where('name', "like", "%" . request('category') . "%");
            });
        } else {
            return $next($query);
        }


        return $next($query);
    }
}
