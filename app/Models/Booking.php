<?php

namespace App\Models;

use App\Traits\Filter;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use Filter;
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
