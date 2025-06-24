<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Tax;
use App\Rules\AddOnForService;
use App\Rules\FutureBookingTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        try {
            $bookings = auth()->user()->bookings()->with(['service', 'addOns'])->get();
            return responseSuccess('Bookings retrieved', $bookings);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $this->sanitizedRequest($request);
            // $vendorId = $service->user_id;
            DB::beginTransaction();
            $booking = Booking::create($data);
            if ($request->add_ons) {
                $this->bindAddOnsToBooking($request, $booking, $data);
            }
            DB::commit();
            return responseSuccess('Now Please make payment to confirm your booking', $booking->load(['service', 'addOns']));
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    protected function bindAddOnsToBooking($request, $booking, $data)
    {
        $service = Service::find($request->service_id);
        $total = $data['base_price'] + 20; // Base price + tax
        foreach ($request->add_ons as $addOnId) {
            // $addOn = AddOn::find($addOnId);
            $addOn = $service->addOns()->where('add_on_id', $addOnId)->first();
            // dd($addOnId, $addOn);
            $booking->addOns()->attach($addOn->id, [
                'add_on_name' => $addOn->name,
                'add_on_price' => $addOn->pivot->add_on_price,
            ]);
            $total += $addOn->pivot->add_on_price;
        }
        $booking->update(['total_price' => $total]);
    }

    protected function sanitizedRequest(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time_from' => 'required|date_format:H:i',
            'booking_time_to' => 'required|date_format:H:i|after:booking_time_from',
            'add_ons' => 'nullable|array',
            'add_ons.*' => ['exists:add_on_service,add_on_id', new AddOnForService($request->service_id)],
            // 'booking_date_time' => ['required', new FutureBookingTime], // Apply custom rule here
        ], [
            'service_id.required' => 'Service is required.',
            'service_id.exists' => 'The selected service does not exist.',
            'booking_date.required' => 'Booking date is required.',
            'booking_date.date' => 'Booking date must be a valid date.',
            'booking_date.after_or_equal' => 'Booking date must be today or in the future.',
            'booking_time_from.required' => 'Booking start time is required.',
            'booking_time_from.date_format' => 'Booking start time must be in 24-hour format (HH:mm), e.g., 14:30.',
            'booking_time_to.required' => 'Booking end time is required.',
            'booking_time_to.date_format' => 'Booking end time must be in 24-hour format (HH:mm), e.g., 18:45.',
            'booking_time_to.after' => 'Booking end time must be after the start time.',
            'add_ons.array' => 'Add-ons must be an array.',
            'add_ons.*.exists' => 'One or more selected add-ons are invalid.',
        ]);

        $request->validate([
            'booking_date' => ['required', new FutureBookingTime], // Custom validation for future date-time
        ]);
        $service = Service::find($request->service_id);
        $tax = Tax::where('name', 'plateform tax')->where('is_active', true)->first();
        $basePrice = $service->price;
        $data = [
            'user_id' => auth()->id(),
            'service_id' => $service->id,
            'service_name' => $service->name,
            // 'vendor_id' => $vendorId,
            'booking_date' => $request->booking_date,
            'booking_time_from' => $request->booking_time_from ?? null,
            'booking_time_to' => $request->booking_time_to ?? null,
            'base_price' => $basePrice,
            'tax_price' => $tax->taxAmount($basePrice), // Assuming a fixed tax price for simplicity
            'total_price' => $basePrice,
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
        ];
        return $data;
    }
}
