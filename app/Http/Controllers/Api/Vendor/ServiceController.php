<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $services = auth()->user()->services()->with(['category', 'addOns'])->get();
            return responseSuccess('Services retrieved successfully', $services);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
    public function getAllServices()
    {
        try {
            $services = Service::with(['category', 'addOns', 'user'])->get();
            return responseSuccess('Services retrieved successfully', $services);
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
            // dd($request->all());
            $data =  $this->sanitizedRequest($request, auth()->user());
            DB::beginTransaction();
            $service = auth()->user()->services()->create($data);
            if ($request->has('add_ons') && $request->has('add_on_price')) {
                $addonData = $this->prepareAddOns($request);
                $service->addOns()->sync($addonData);
            }
            DB::commit();
            return responseSuccess('Service created successfully', $service->load(['category', 'addOns']));
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
            $service = Service::with(['category', 'addOns', 'user'])->find($id);
            if (!$service) {
                return responseError('Service not found', 404);
            }
            return responseSuccess('Service retrieved successfully', $service);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        try {
            Gate::authorize('update', $service);
            $data = $this->sanitizedUpdateRequest($request, $service);
            // dd($service->load(['category', 'addOns']));
            DB::beginTransaction();
            $service->update($data);
            if ($request->has('add_ons') && $request->has('add_on_price')) {
                $addonData = $this->prepareAddOns($request);
                $service->addOns()->sync($addonData);
            }
            DB::commit();
            return responseSuccess('Service updated successfully', $service->fresh()->load(['category', 'addOns']));
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    public function updateStatus(Request $request, Service $service)
    {
        try {

            $request->validate([
                'status' => 'required|boolean',
            ], [
                'status.boolean' => 'Status must be 1 or 0',
            ]);
            $service->update(['status' => $request->status]);
            return responseSuccess('Service status updated successfully', $service->load(['category', 'addOns']));
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }


    protected function sanitizedRequest(Request $request, $vendor): array
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string',
                'company' => 'nullable|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'status' => 'required|boolean',
                'image' => 'required|array',
                'image.*' => 'required|image', // Assuming image is an array of images
                'add_ons' => 'nullable|array',
                'add_ons.*' => 'exists:add_ons,id',
                'add_on_price' => 'nullable|array',
                'add_on_price.*' => 'numeric|min:0',
            ],
            [
                'category_id.required' => 'Category is required',
                'category_id.exists' => 'Category does not exist',
                'status.boolean' => 'Status must be 1 or 0',
            ]
        );
        // Custom logic: if add_ons is present, add_on_price becomes required
        $validator->sometimes('add_on_price', 'required|array', function ($input) {
            return !empty($input->add_ons);
        });

        $validator->sometimes('add_on_price.*', 'required|numeric|min:0', function ($input) {
            return !empty($input->add_ons);
        });

        $validator->validate();


        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'company' => $request->company,
            'description' => $request->description,
            'price' => $request->price,
            'status' => $request->status,
        ];
        if ($request->hasFile('image')) {
            $images = [];
            foreach ($request->file('image') as $key => $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('vendor/services/images'), $imageName);
                $images[] = asset('vendor/services/images') . '/' . $imageName;
            }
            $data['image'] = $images;
        }
        return $data;
    }

    public function sanitizedUpdateRequest(Request $request, Service $service): array
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string',
                'company' => 'nullable|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'status' => 'required|boolean',
                'image' => 'nullable|array',
                'image.*' => 'nullable|image', // Assuming image is an array of images
                'add_ons' => 'nullable|array',
                'add_ons.*' => 'exists:add_ons,id',
                'add_on_price' => 'nullable|array',
                'add_on_price.*' => 'numeric|min:0',
            ],
            [
                'category_id.required' => 'Category is required',
                'category_id.exists' => 'Category does not exist',
                'status.boolean' => 'Status must be 1 or 0',
            ]
        );

        // Custom logic: if add_ons is present, add_on_price becomes required
        $validator->sometimes('add_on_price', 'required|array', function ($input) {
            return !empty($input->add_ons);
        });

        $validator->sometimes('add_on_price.*', 'required|numeric|min:0', function ($input) {
            return !empty($input->add_ons);
        });

        $validator->validate();

        $data = [
            'category_id' => $request->category_id ?? $service->category_id,
            'name' => $request->name ?? $service->name,
            'company' => $request->company ?? $service->company,
            'description' => $request->description ?? $service->description,
            'price' => $request->price ?? $service->price,
            'status' => $request->status ?? $service->status,
        ];
        if ($request->hasFile('image')) {
            $images = [];
            foreach ($request->file('image') as $key => $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('vendor/services/images'), $imageName);
                $images[] = asset('vendor/services/images') . '/' . $imageName;
            }
            $data['image'] = $images;
        }
        return $data;
    }

    protected function prepareAddOns($request)
    {
        // Step 2: Attach add-ons with pivot data

        $addonData = [];

        foreach ($request->add_ons as $index => $addonId) {
            $addOn = AddOn::find($addonId);
            $price = $request->add_on_price[$index] ?? 0;
            $addonData[$addonId] = [
                'add_on_name' => $addOn->name,
                'add_on_price' => $price,
                'service_name' => $request->name,
            ];
        }
        return $addonData;
    }
}
