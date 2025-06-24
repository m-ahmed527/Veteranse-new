<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $cart = auth()->user()->cart()->with('products.category')->first();
            if (!$cart) {
                return responseError('Your Cart is Empty', 404);
            }
            return responseSuccess('Cart retrieved successfully', $cart);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);
            DB::beginTransaction();
            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
            $product = Product::findOrFail($request->product_id);
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
            return responseSuccess('Product added to cart', $cartProduct);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    public function removeFromCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);
            $cart = auth()->user()->cart()->first();
            if (!$cart->products()->exists()) {
                return responseError('Your Cart is Empty', 400);
            }
            if ($cart && $cart->products()->where('product_id', $request->product_id)->exists()) {
                $cart->products()->detach($request->product_id);
                $this->recalculateCart($cart);
                if (!$cart->products()->exists()) {
                    return responseSuccess('Your Cart is Empty Now');
                }
                return responseSuccess('Product removed from cart');
            } else {
                return responseError('Product not found in cart', 404);
            }
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        try {
            $cart = auth()->user()->cart()->first();
            if ($cart) {
                $cart->products()->detach();
                $cart->update(['total_items' => 0, 'sub_total_amount' => 0, 'total_amount' => 0, 'discount_value' => 0, 'discount_type' => null]);
                return responseSuccess('Cart has been cleared');
            } else {
                return responseError('Cart not found', 404);
            }
        } catch (\Exception $e) {
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
