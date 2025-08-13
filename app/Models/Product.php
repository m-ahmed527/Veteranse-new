<?php

namespace App\Models;

use App\Traits\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use Filter;
    protected $guarded = ['id'];
    protected $appends = ['type', 'wishlist'];


    protected $casts = [
        'image' => 'json'
    ];
    public function getTypeAttribute()
    {
        return 'product'; // or 'service' in Service model
    }

    public function getWishlistAttribute()
    {
        $user = auth()->user();

        if (!$user) {
            return false; // Guest users ke liye wishlist false
        }

        return $this->wishlistedByUsers()
            ->where('user_id', $user->id)
            ->exists();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // ->withTrashed();
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

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('product_price', 'quantity', 'total_price', 'vendor_id', 'tax_price', 'vendor_cut')
            ->withTimestamps();
    }

    // public function reviewedByUsers(): MorphToMany
    // {
    //     return $this->morphToMany(User::class, 'reviewable', 'reviews')->withTimestamp();
    // }
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
