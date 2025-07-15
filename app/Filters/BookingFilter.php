<?php

namespace App\Filters;

use Closure;

class BookingFilter
{
    /**
     * Create a new class instance.
     */
    public function handle($query, Closure $next)
    {

        if ((request()->has('month') && request('month') != null)  || (request()->has('year') && request('year') != null)) {
            $month = request('month');
            $year = request('year');
            $query->whereMonth('booking_date', $month)->whereYear('booking_date', $year);
        } else {
            return $next($query);
        }


        return $next($query);
    }
}
