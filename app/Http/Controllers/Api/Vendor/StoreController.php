<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{


    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $vendor)
    {
        try {
            // dd($request->all());
            $data = $this->sanitizedRequest($request, $vendor);
            $vendor->update($data);
            return responseSuccess('Store updated succesfully', $vendor->fresh());
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    protected function sanitizedRequest(Request $request, $vendor): array
    {
        $request->validate([
            'vendor_store_image'        => 'sometimes|required|image',
            'vendor_store_gallery'      => 'sometimes|required|array',
            'vendor_store_gallery.*'    => 'sometimes|required|image',
            'vendor_store_title'        => 'sometimes|required|string',
            'vendor_store_description'  => 'sometimes|required|string',
        ]);
        $data = [
            'vendor_store_title' => $request->vendor_store_title ?? $vendor->vendor_store_title,
            'vendor_store_description' => $request->vendor_store_description ?? $vendor->vendor_store_description,
        ];
        if ($request->hasFile('vendor_store_image')) {
            $imageName = time() . '.' . $request->vendor_store_image->getClientOriginalExtension();
            $request->vendor_store_image->move(public_path('vendor/store/covers'), $imageName);
            $data['vendor_store_image'] = asset('vendor/store/covers') . '/' . $imageName;
        }
        if ($request->has('vendor_store_gallery')) {
            $imageNames = [];
            foreach ($request->vendor_store_gallery as $key => $gallery) {
                $imageName = time() . '_' . uniqid() . '.' . $gallery->getClientOriginalExtension();
                $gallery->move(public_path('vendor/store/gallery'), $imageName);
                $imageNames[] = asset('vendor/store/gallery') . '/' . $imageName;
            }
            $data['vendor_store_gallery'] = $imageNames;
        }
        // dd($data);
        return $data;
    }
}
