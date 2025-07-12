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
        try {
            $orders = auth()->user()->orders()->with('products')->get();
            return responseSuccess('Orders retrieved', $orders);
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

    public function craeteOrderWithWallet(Request $request)
    {
        try {
            $user = auth()->user();
            $cart = $user->cart->load('products');
            if (!$cart->products()->exists()) {
                return responseError('Cart is empty', 400);
            }
            if ($user->wallet_balance < $cart->total_amount) {
                return responseError('Insufficient wallet balance,please use other payment method', 400);
            }

            DB::beginTransaction();
            $order = $this->createOrderFromCart($cart, 'wallet');
            $this->updateOrderWithPaymentDetails($order, null, null); // No payment method or intent for wallet
            $user->debitWallet($cart->total_amount, 'Purchased products from cart');
            DB::commit();
            return responseSuccess('Order created successfully', ['wallet_used' => true, 'order' => $order->load('products')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 500);
        }
    }

    private function createOrderFromCart($cart, $paymentType = 'card')
    {
        $order = auth()->user()->orders()->create([
            'total_amount' => $cart->total_amount,
            'order_status' => $paymentType == 'wallet' ? 'placed' : 'pending',
            'payment_type' => $paymentType,
            'payment_status' => $paymentType == 'wallet' ? 'succeeded' : 'pending',
        ]);
        $data = $this->prepareOrderProductData($cart);
        $order->products()->sync($data);
        $cart->products()->detach();
        return $order;
    }


    private function prepareOrderProductData($cart)
    {
        $data = [];
        $tax = getTax();
        foreach ($cart->products as $product) {
            $vendor = $product->user;
            $taxPrice = $vendor->has_subscribed ? 0 : $tax->taxAmount($product->pivot->product_total);
            $vendorCut = $product->pivot->product_total - $taxPrice;
            $data[$product->id] = [
                'product_price' => $product->price,
                'quantity' => $product->pivot->product_quantity,
                'total_price' => $product->pivot->product_total,
                'tax_price' => $taxPrice,
                'vendor_cut' => $vendorCut,
                'vendor_id' => $vendor->id,
            ];
        }

        return $data;
    }

    private function updateOrderWithPaymentDetails($order, $paymentMethod = null, $paymentIntent = null)
    {
        $order->update([
            'payment_method' => $paymentMethod->id ?? null,
            'payment_intent' => $paymentIntent->id ?? null,
        ]);
    }
}
