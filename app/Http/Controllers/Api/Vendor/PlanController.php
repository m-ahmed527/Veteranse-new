<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Str;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $plans = Plan::all();
            return responseSuccess('Available plans retrieved successfully', $plans);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'name' => 'required|string|max:255',
    //             'price' => 'required|numeric|min:0',
    //         ]);
    //         $validatedData['slug'] = \Str::slug($validatedData['name'], '-');
    //         $plan = Plan::create($validatedData);
    //         return responseSuccess('Plan created successfully', $plan);
    //     } catch (ValidationException $e) {
    //         return responseValidationError($e->getMessage(), 422);
    //     } catch (\Exception $e) {
    //         return responseError($e->getMessage(), 400);
    //     }
    // }

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
