<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Filters\BookingFilter;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $booking = Booking::with(['addOns', 'service', 'user'])->find($id);
            if (!$booking) {
                return responseError('Booking not found', 404);
            }

            if ($booking->vendor_id != auth()->user()->id) {
                return responseError('You are not authorized to view this booking', 403);
            }
            return responseSuccess('Booking retrieved successfully', $booking);
        } catch (\Exception $e) {
            return responseError('Failed to retrieve booking', $e->getMessage());
        }
    }

    public function cancelBooking(Request $request,  $id)
    {
        try {
            $vendor = auth()->user();
            $booking = Booking::findOrFail($id);
            $user = $booking->user;
            if ($booking->vendor_id != $vendor->id) {
                return responseError('You are not authorized to cancel this booking', 403);
            }
            if ($booking->booking_status == 'cancelled') {
                return responseError('This booking has already been cancelled', 400);
            }
            if (!Carbon::parse($booking->booking_date)->isFuture()) {
                return responseError('You can only cancel bookings that are in the future', 400);
            }
            if ($booking->payment_status == 'succeeded' && $booking->booking_status == 'confirmed') {
                DB::beginTransaction();
                $booking->update(['booking_status' => 'cancelled']);
                $user->creditWallet($booking->total_price, 'Booking cancelled by vendor for : ' . $booking->service_name);
                DB::commit();
                return responseSuccess('Booking cancelled successfully and wallet credited', $booking->load(['service', 'addOns']));
            }
            if ($booking->payment_status != "succeeded") {
                DB::beginTransaction();
                $booking->update(['booking_status' => 'cancelled']);
                DB::commit();
                return responseSuccess('Booking cancelled successfully', $booking->load(['service', 'addOns']));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }
}
