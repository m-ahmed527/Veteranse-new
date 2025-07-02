<?php

namespace App\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    public function handle($request, Closure $next)
    {
        $search = request('search');

        if ($search) {
            $request->where(function (Builder $query) use ($search) {
                // Search own columns using CONCAT
                $query->orWhereRaw("
                    CONCAT_WS(' ',
                        COALESCE(name, ''),
                        COALESCE(company, ''),
                        COALESCE(description, ''),
                        COALESCE(price, '')
                    ) LIKE ?
                ", ["%{$search}%"]);
                // Search category name using relation
                $query->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $next($request);
    }
}
