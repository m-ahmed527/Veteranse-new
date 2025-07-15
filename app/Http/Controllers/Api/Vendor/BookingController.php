<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Filters\BookingFilter;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Logic to retrieve bookings for the vendor
            $bookings = Booking::where('vendor_id', auth()->user()->id)
                ->with(['addOns', 'service', 'user'])
                ->filter([
                    BookingFilter::class,
                ])->get();
            if ($bookings->isEmpty()) {
                return responseSuccess('No bookings found for the vendor');
            }
            return responseSuccess('Bookings retrieved successfully', $bookings);
        } catch (\Exception $e) {
            return responseError('Failed to retrieve bookings', $e->getMessage());
        }
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
