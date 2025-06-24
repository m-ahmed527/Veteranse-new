<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return responseSuccess('Categories retrieved successfully', $categories);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return responseError('Category not found', 404);
            }
            return responseSuccess('Category retrieved successfully', $category);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
}
