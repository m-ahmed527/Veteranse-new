<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('product_price', 'quantity', 'total_price', 'vendor_id', 'tax_price', 'vendor_cut')->withTimestamps();
    }
}
