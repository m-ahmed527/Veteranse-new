<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Logic to retrieve orders for the vendor
            $orders = Order::whereHas('products', function ($query) {
                $query->where('vendor_id', auth()->user()->id);
            })->with(['products', 'user'])->get();
            if ($orders->isEmpty()) {
                return responseSuccess('No orders found for the vendor');
            }
            return responseSuccess('Orders retrieved successfully', $orders);
        } catch (\Exception $e) {
            return responseError('Failed to retrieve orders', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
