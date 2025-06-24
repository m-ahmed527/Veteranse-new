<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_products', 'cart_id', 'product_id')
            ->withPivot('product_quantity', 'product_total')
            ->withTimestamps();
    }
    // public function totalPrice()
    // {
    //     return $this->products->sum(function ($product) {
    //         return $product->pivot->price * $product->pivot->quantity;
    //     });
    // }
    // public function totalQuantity()
    // {
    //     return $this->products->sum('pivot.quantity');
    // }
}
