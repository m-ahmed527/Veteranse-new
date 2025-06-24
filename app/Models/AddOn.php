<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AddOn extends Model
{
    protected $guarded = [];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'add_on_service', 'add_on_id', 'service_id')
            ->withPivot('add_on_name', 'service_name', 'add_on_price')
            ->withTimestamps();
    }
}
