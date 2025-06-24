<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $guarded = ['id'];


    public function taxAmount($amount)
    {
        if ($this->type == 'percentage') {
            return ($this->rate / 100) * $amount;
        } elseif ($this->type === 'fixed') {
            return $this->rate;
        }
        return 0; // Default case if type is not recognized
    }
}
