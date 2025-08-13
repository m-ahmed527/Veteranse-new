<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        try {
            $reviews = Review::with(['user', 'reviewable'])->latest()->get();

            return responseSuccess('All reviews retrieved successfully', $reviews);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
    public function store(Request $request)
    {
        try {
            $this->validateRequest($request);
            $model = $request->type == 'product' ? Product::class : Service::class;
            $item = $model::find($request->id);

            if (!$item) {
                return responseError(ucfirst($request->type) . ' not found', 404);
            }

            DB::beginTransaction();

            // Review create karo directly relation se
            $submittedReview = $item->reviews()->create([
                'user_id' => auth()->id(),
                'review' => $request->review,
                'rating' => $request->rating,
            ]);

            DB::commit();

            return responseSuccess(
                'Review submitted successfully',
                $submittedReview->load('user')
            );
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
