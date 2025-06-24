<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $wishlistCount = $user->wishlistedProducts()->count() + $user->wishlistedServices()->count();
            $products = $user->wishlistedProducts()->with(['category', 'user'])->get();
            $services = $user->wishlistedServices()->with(['category', 'addOns', 'user'])->get();

            if ($wishlistCount == 0) {
                return responseError('Wishlist is empty', 404);
            }

            $data = [
                'wishlist_count' => $wishlistCount,
                'products' => $products,
                'services' => $services,
            ];
            return responseSuccess('Wishlist retrieved', $data);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
    public function add(Request $request)
    {


        try {
            $request->validate([
                'type' => 'required|in:product,service',
                'id' => 'required|integer',
            ]);

            // Check if product/service exists
            if ($request->type === 'product') {
                if (!Product::where('id', $request->id)->exists()) {
                    return responseError('Product not found', 404);
                }
            } else {
                if (!Service::where('id', $request->id)->exists()) {
                    return responseError('Service not found', 404);
                }
            }
            DB::beginTransaction();

            $user = auth()->user();

            if ($request->type === 'product') {
                $user->wishlistedProducts()->syncWithoutDetaching([$request->id]);
            } else {
                $user->wishlistedServices()->syncWithoutDetaching([$request->id]);
            }
            $wishlistCount = $user->wishlistedProducts()->count() + $user->wishlistedServices()->count();
            DB::commit();

            return responseSuccess('Item added to wishlist', [
                'wishlist_count' => $wishlistCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    // public function add(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'type' => 'required|in:product,service',
    //             'id' => 'required|integer',
    //         ]);
    //         if ($request->type == 'product' && !(Product::where('id', $request->id)->exists())) {
    //             return responseError('Product not found', 404);
    //         }
    //         if ($request->type == 'service' && !(Service::where('id', $request->id)->exists())) {
    //             return responseError('Service not found', 404);
    //         }

    //         DB::beginTransaction();
    //         $user = auth()->user();

    //         if ($request->type == 'product') {
    //             $user->wishlistedProducts()->syncWithoutDetaching([$request->id]);
    //         } else {
    //             $user->wishlistedServices()->syncWithoutDetaching([$request->id]);
    //         }
    //         DB::commit();
    //         return responseSuccess('Item added to wishlist');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return responseError($e->getMessage(), 400);
    //     }
    // }

    public function remove(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:product,service',
                'id' => 'required|integer',
            ]);
            $user = auth()->user();
            // Check if product/service exists
            if ($request->type === 'product') {
                if (!$user->wishlistedProducts()->where('wishlistable_id', $request->id)->where('wishlistable_type', Product::class)->exists()) {
                    return responseError('Product not found in wishlist ', 404);
                }
            } else {
                if (!$user->wishlistedServices()->where('wishlistable_id', $request->id)->where('wishlistable_type', Service::class)->exists()) {
                    return responseError('Service not found in wishlist', 404);
                }
            }
            if ($user->wishlistedProducts()->count() + $user->wishlistedServices()->count() == 0) {
                return responseError('Wishlist is empty', 400);
            }
            DB::beginTransaction();

            if ($request->type == 'product') {
                $user->wishlistedProducts()->detach($request->id);
            } else {
                $user->wishlistedServices()->detach($request->id);
            }
            DB::commit();
            // Recalculate wishlist count
            $wishlistCount = $user->wishlistedProducts()->count() + $user->wishlistedServices()->count();
            if ($wishlistCount == 0) {
                return responseSuccess('Wishlist is empty now', [
                    'wishlist_count' => $wishlistCount
                ]);
            }
            return responseSuccess('Item removed from wishlist', [
                'wishlist_count' => $wishlistCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    public function clear()
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $user->wishlistedProducts()->detach();
            $user->wishlistedServices()->detach();
            DB::commit();
            return responseSuccess('Wishlist cleared');
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }
}
