<?php

namespace App\Http\Controllers\Api\User;

use App\Filters\BookingFilter;
use App\Http\Controllers\Controller;
use App\Models\AddOn;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Tax;
use App\Rules\AddOnForService;
use App\Rules\FutureBookingTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;

class BookingController extends Controller
{
    public function getAllBookings()
    {
        try {
            $bookings = Booking::with(['service', 'addOns'])->filter([
                BookingFilter::class,
            ])->get();
            if ($bookings->isEmpty()) {
                return responseSuccess('No bookings found');
            }
            return responseSuccess('Bookings retrieved', $bookings);
        } catch (\Exception $e) {
            return responseError($e->getMessage(), 400);
        }
    }
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
            $user = auth()->user();
            $service = Service::find($request->service_id);
            $vendor = $service->user; //the service has a user_id field for the vendor
            $data = $this->sanitizedRequest($request, $service, $vendor);
            setStripeKey();
            if (!$user->stripe_customer_id) {
                $customer = createStripeCustomer($user);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }

            $paymentMethod = attachPaymentMethodToCustomer($request->payment_method_id, $user);

            $paymentIntent = createPaymentIntent($request, 10,  $user, 'Booking', [
                'user_id' => $user->id,
                'service_id' => $data['service_id'],
            ]);
            $data['payment_intent'] = $paymentIntent->id;
            $data['payment_method'] = $paymentMethod->id;
            DB::beginTransaction();
            $booking = Booking::create($data);
            if ($request->add_ons) {
                $this->bindAddOnsToBooking($request, $booking, $data, $service, $vendor);
            }
            DB::commit();
            return responseSuccess('Booking created successfully', $booking->load(['service', 'addOns']));
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $booking = Booking::with(['addOns', 'service', 'user'])->find($id);
            if (!$booking) {
                return responseError('Booking not found', 404);
            }

            if ($booking->user_id != auth()->id()) {
                return responseError('You are not authorized to view this booking', 403);
            }
            return responseSuccess('Booking retrieved successfully', $booking);
        } catch (\Exception $e) {
            return responseError($e->getMessage());
        }
    }

    public function createBookingWithWallet(Request $request)
    {
        try {
            $user = auth()->user();
            $service = Service::find($request->service_id);
            $vendor = $service->user; //the service has a user_id field for the vendor
            $data = $this->sanitizedRequest($request, $service, $vendor);

            if ($user->wallet_balance < $data['total_price']) {
                return responseError('Insufficient wallet balance for this booking,please use other payment method', 400);
            }

            DB::beginTransaction();
            $booking = Booking::create($data);
            if ($request->add_ons) {
                $this->bindAddOnsToBooking($request, $booking, $data, $service, $vendor);
            }
            $user->debitWallet($data['total_price'], 'Booked a service: ' . $service->name);
            DB::commit();
            return responseSuccess('Booking created successfully', ['wallet_used' => true, 'booking' => $booking->load(['service', 'addOns'])]);
        } catch (\Exception $e) {
            DB::rollBack();
            return responseError($e->getMessage(), 400);
        }
    }


    public function cancelBooking(Request $request, string $id)
    {
        try {
            $user = auth()->user();
            $booking = Booking::findOrFail($id);
            // dd(Carbon::parse($booking->booking_date)->isAfter(now()), Carbon::parse($booking->booking_date)->isFuture());
            if ($booking->user_id != $user->id) {
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
                $user->creditWallet($booking->total_price, 'Booking cancelled for : ' . $booking->service_name);
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
    protected function bindAddOnsToBooking($request, $booking, $data, $service = null, $vendor = null)
    {

        $tax = getTax();
        $from = Carbon::parse($data['booking_time_from']);
        $to = Carbon::parse($data['booking_time_to']);
        $hours = $from->diffInHours($to) > 1 ? ceil($from->diffInHours($to)) : 1; // pure integer hours
        $total = $data['base_price'] * $hours;
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
        $taxPrice = $vendor->has_subscribed ? 0 : $tax->taxAmount($total);
        $vendorCut = $total - $taxPrice;
        $booking->update([
            'total_price' => $total,
            'tax_price' => $taxPrice,
            'vendor_cut' => $vendorCut,
            'charge_hour' => $hours,
        ]);
    }

    protected function sanitizedRequest(Request $request, $service = null, $vendor = null)
    {
        $request->validate([
            'payment_method_id' => 'sometimes|required|string',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time_from' => 'required|date_format:H:i',
            'booking_time_to' => 'required|date_format:H:i|after:booking_time_from',
            'add_ons' => 'nullable|array',
            'add_ons.*' => ['exists:add_on_service,add_on_id', new AddOnForService($request->service_id)],
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'phone' => 'nullable|string',
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

        $basePrice = $service->discounted_price ?? $service->price; // Use discounted price if available, otherwise use regular price
        $data = [
            'user_id' => auth()->id(),
            'service_id' => $service->id,
            'service_name' => $service->name,
            'vendor_id' => $vendor->id,
            'booking_date' => $request->booking_date,
            'booking_time_from' => $request->booking_time_from ?? null,
            'booking_time_to' => $request->booking_time_to ?? null,
            'base_price' => $basePrice,
            // 'tax_price' => $vendor->has_subscribed ? 0 : $tax->taxAmount($basePrice), // Assuming a fixed tax price for simplicity
            'total_price' => $basePrice,
            'booking_status' => 'pending',
            'payment_status' => 'pending',
            'address' => $request->address ?? null,
            'city' => $request->city ?? null,
            'state' => $request->state ?? null,
            'country' => $request->country ?? null,
            'zip_code' => $request->zip_code ?? null,
            'phone' => $request->phone ?? null,
        ];
        return $data;
    }
}
