<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // public function vendor()
    // {
    //     return $this->belongsTo(User::class, 'vendor_id');
    // }

    public function addOns()
    {
        return $this->belongsToMany(AddOn::class, 'booking_add_on')
            ->withPivot('add_on_name', 'add_on_price')
            ->withTimestamps();
    }
}
