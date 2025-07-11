<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'payment_method_id' => 'required|string',
            ]);
            $user = auth()->user();
            $cart = $user->cart->load('products');
            if (!$cart->products()->exists()) {
                return responseError('Cart is empty', 400);
            }
            setStripeKey();
            if (!$user->stripe_customer_id) {
                $customer = createStripeCustomer($user);
                $user->update([
                    'stripe_customer_id' => $customer->id,
                ]);
            }

            $paymentMethod = attachPaymentMethodToCustomer($request->payment_method_id, $user);

            DB::beginTransaction();
            $order = $this->createOrderFromCart($cart);
            $paymentIntent = createPaymentIntent($request, $order->total_amount,  $user, 'Product', [
                'user_id' => $user->id,
                'order_id' => $order->id,
            ]);
            $this->updateOrderWithPaymentDetails($order, $paymentMethod, $paymentIntent);
            DB::commit();
            return responseSuccess('Order created successfully', $order->load('products'));
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 500);
        }
    }


    private function createOrderFromCart($cart)
    {
        $order = auth()->user()->orders()->create([
            'total_amount' => $cart->total_amount,
            'order_status' => 'pending',
        ]);
        $data = $this->prepareOrderProductData($cart);
        $order->products()->sync($data);
        $cart->products()->detach();
        return $order;
    }


    private function prepareOrderProductData($cart)
    {
        $data = [];

        foreach ($cart->products as $product) {
            $data[$product->id] = [
                'product_price' => $product->price,
                'quantity' => $product->pivot->product_quantity,
                'total_price' => $product->pivot->product_total,
            ];
        }

        return $data;
    }

    private function updateOrderWithPaymentDetails($order, $paymentMethod, $paymentIntent)
    {
        $order->update([
            'payment_method' => $paymentMethod->id ?? null,
            'payment_intent' => $paymentIntent->id ?? null,
        ]);
    }
}
