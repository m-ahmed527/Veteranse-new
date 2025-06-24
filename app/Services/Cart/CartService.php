<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function addToCart($request)
    {
        try {

            DB::beginTransaction();
            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
            $product = Product::findOrFail($request->product_id);
            // dd($request->all(), $product, $cart);
            $total = $product->price * $request->quantity;

            $cart->products()->syncWithoutDetaching([
                $product->id => [
                    'product_quantity' => $request->quantity,
                    'product_total' => $total,

                ]
            ]);
            $this->recalculateCart($cart);
            $cartProduct = $cart->load(['products' => function ($q) use ($product) {
                $q->where('product_id', $product->id)->with('category');
            }]);
            DB::commit();
            return responseSuccess("Product added to cart", $cartProduct);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    private function recalculateCart($cart)
    {
        $subTotal = $cart->products->sum(function ($product) {
            return $product->pivot->product_total;
        });
        $totalItems = $cart->products->count();

        $cart->update([
            'cart_name' => auth()->user()->name . '-Cart',
            'total_items' => $totalItems,
            'sub_total_amount' => $subTotal,
            'total_amount' => $subTotal // discount logic can go here
        ]);
    }
}
