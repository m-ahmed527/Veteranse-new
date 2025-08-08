<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        //
    }
    public function store(Request $request)
    {
        try {
            $this->validateRequest($request);
            if ($request->type == 'product') {
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
            $data = $this->prepareReview($request);
            if ($request->type == 'product') {
                $user->reviewedProducts()->attach($data);
            } else {
                $user->reviewedServices()->attach($data);
            }
            $submittedReview = $user->reviews()->where('reviewable_type', $request->type == 'product' ? Product::class : Service::class)->where('reviewable_id', $request->id)->latest()->first();
            DB::commit();
            return responseSuccess('Review submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }
    private function validateRequest(Request $request)
    {
        $request->validate([
            "type" => "required|in:product,service",
            "id" => "required|integer",
            "rating" => "required|integer|in:1,2,3,4,5",
            "review" => "required|string",
        ]);
    }
    private function prepareReview(Request $request)
    {
        $data = [
            $request->id => [
                'rating' => $request->rating,
                'review' => $request->review,
            ],
        ];
        return $data;
    }
}
