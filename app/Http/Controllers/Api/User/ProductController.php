<?php

namespace App\Http\Controllers\Api\User;

use App\Filters\CategoryFilter;
use App\Filters\PriceFilter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function getAllProducts()
    {
        try {
            $products = Product::with(['category', 'user'])->get();
            // $query = $products->filter([
            //     CategoryFilter::class,
            //     PriceFilter::class,

            // ]);
            // dd($query->get());
            return responseSuccess('Products retrieved successfully', $products);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with(['category', 'user'])->find($id);
            if (!$product) {
                return responseError('Product not found', 404);
            }
            return responseSuccess('Product retrieved successfully', $product);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
}
