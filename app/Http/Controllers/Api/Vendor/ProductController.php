<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = auth()->user()->products()->with('category')->get();
            return responseSuccess('Products retrieved successfully', $products);
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
            $data = $this->sanitizedRequest($request);
            // dd($data);
            DB::beginTransaction();
            $product = auth()->user()->products()->create($data);
            DB::commit();
            return responseSuccess('Product created successfully', $product->load('category'));
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $product = auth()->user()->products()->with('category')->find($id);

            if (!$product) {
                return responseError('Product not found', 404);
            }

            return responseSuccess('Product retrieved successfully', $product);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            Gate::authorize('update', $product);
            $data = $this->sanitizedRequest($request, $product);
            // dd($data);
            DB::beginTransaction();
            $product->update($data);
            DB::commit();
            return responseSuccess('Product updated successfully', $product->load('category'));
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    protected function sanitizedRequest(Request $request, $product = null): array
    {
        // dd($product, $request->all());

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'image' => 'sometimes|required|array',
            'image.*' => 'sometimes|required|image', // Allow multiple images
        ], [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Category does not exist',
            'status.boolean' => 'Status must be 1 or 0',
        ]);


        $data = [
            'category_id' => $request->category_id ?? $product->category_id,
            'name' => $request->name ?? $product->name,
            'company' => $request->company ?? $product->company,
            'description' => $request->description ?? $product->description,
            'price' => $request->price ?? $product->price,
            'status' => $request->status ?? $product->status,
        ];
        if ($request->hasFile('image')) {
            $images = [];
            foreach ($request->file('image') as $key => $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('vendor/products/images'), $imageName);
                $images[] = asset('vendor/products/images') . '/' . $imageName;
            }
            $data['image'] = $images;
        } else {
            $data['image'] = $product->image;
        }
        return $data;
    }
}
