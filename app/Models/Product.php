<?php

namespace App\Models;

use App\Traits\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Product extends Model
{
    use Filter;
    protected $guarded = ['id'];

    protected $casts = [
        'image' => 'json'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cart()
    {
        return $this->belongsToMany(Cart::class, 'cart_products', 'product_id', 'cart_id')
            ->withPivot('product_quantity', 'product_total')
            ->withTimestamps();
    }


    public function wishlistedByUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'wishlistable', 'wishlists')->withTimestamps();
    }
}
