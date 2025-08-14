<?php

namespace App\Http\Controllers\Api\User;

use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $users = User::where('role', 'user')->filter([
                UserFilter::class
            ])->paginate(50);
            if ($users->isEmpty()) {
                return responseError("No user found", 404);
            }
            return responseSuccess("Users retrieved successfully", $users);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    public function becomeVendor(Request $request)
    {
        try {
            $data = $this->validateRequest($request);
            $user = auth()->user();

            if ($user->role == 'vendor') {
                return responseError('You are already a vendor, you can use our both services', 403);
            }
            DB::beginTransaction();
            $user->update($data);
            DB::commit();
            return responseSuccess("You are now become a vendor,you can use our both services", $user->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage());
        }
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'vendor_store_title' => 'required|string',
            'vendor_store_description' => 'required|string',
            'vendor_store_image' => 'required|image',
            'vendor_store_gallery'      => 'sometimes|required|array',
            'vendor_store_gallery.*'    => 'sometimes|required|image',
        ]);
        $data = $this->sanitized($request);
        return $data;
    }
    private function sanitized(Request $request)
    {
        $data = [
            'vendor_store_title' => $request->vendor_store_title,
            'vendor_store_description' => $request->vendor_store_description,
            'role' => 'vendor',
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
        return $data;
    }
}
