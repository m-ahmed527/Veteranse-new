<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function update(Request $request, User $user)
    {
        try {
            $data = $this->sanitizedRequest($request, $user);


            DB::beginTransaction();
            $user->update($data);
            DB::commit();

            return responseSuccess('Profile updated successfully', $user->fresh());
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Sanitize the request data.
     */
    protected function sanitizedRequest(Request $request, $user): array
    {
        $request->validate([
            'avatar' => 'nullable|image',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'gender' => 'required',
            'dob' => 'required|date',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $data = [
            'name' => $request->name ?? $user->name,
            'phone' => $request->phone ?? $user->phone,
            'gender' => $request->gender ?? $user->gender,
            'dob' => $request->dob ?? $user->dob,
        ];
        if ($request->hasFile('avatar')) {
            $imageName = time() . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->move(public_path('avatars'), $imageName);
            $data['avatar'] = asset('avatars') . '/' . $imageName;
        }
        if ($request->has('password')) {
            $data['password'] = $request->password;
        }
        return $data;
    }
}
