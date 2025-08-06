<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class UserFilter
{
    public function handle($request, Closure $next)
    {
        $search = request('search');
        // dd($search);
        if ($search) {
            $request->where(function (Builder $query) use ($search) {
                // Search own columns using CONCAT
                $query->orWhereRaw("
                    CONCAT_WS(' ',
                        COALESCE(name, ''),
                        COALESCE(email, ''),
                        COALESCE(phone, ''),
                        COALESCE(gender, ''),
                        COALESCE(gender, '')
                    ) LIKE ?
                ", ["%{$search}%"]);
                // Search category name using relation
                // $query->orWhereHas('category', function ($q) use ($search) {
                //     $q->where('name', 'like', "%{$search}%");
                // });
            });
        }

        return $next($request);
    }
}
