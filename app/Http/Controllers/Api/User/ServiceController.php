<?php

namespace App\Http\Controllers\Api\User;

use App\Filters\AddOnFilter;
use App\Filters\CategoryFilter;
use App\Filters\PriceFilter;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function getAllServices()
    {
        try {
            $services = Service::with(['category', 'addOns', 'user'])->get();
            // $query = $services->filter([
            //     PriceFilter::class,
            //     CategoryFilter::class,
            //     AddOnFilter::class
            // ]);
            // dd($query->get());
            return responseSuccess('Services retrieved successfully', $services);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }


    public function show($id)
    {
        try {
            $service = Service::with(['category', 'addOns', 'user'])->find($id);
            if (!$service) {
                return responseError('Service not found', 404);
            }
            return responseSuccess('Service retrieved successfully', $service);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
}
