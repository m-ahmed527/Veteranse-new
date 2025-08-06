<?php

namespace App\Http\Controllers\Api\User;

use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
}
