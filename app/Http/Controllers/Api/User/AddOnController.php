<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\Request;

class AddOnController extends Controller
{
    public function index()
    {

        try {
            $addOns = AddOn::all();
            return responseSuccess('Add Ons retrieved successfully', $addOns);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $addOn = AddOn::find($id);
            if (!$addOn) {
                return responseError('Add On not found', 404);
            }
            return responseSuccess('Add On retrieved successfully', $addOn);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
}
