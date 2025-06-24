<?php

namespace App\Services\Cart;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Create a new class instance.
     */
    public function register()
    {
        $this->app->singleton('cart', function ($app) {
            return new CartService();
        });
    }
}
