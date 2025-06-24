<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FutureBookingTime implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', request('booking_date') . ' ' . request('booking_time_from'));

        // Check if the date-time is in the future
        if ($bookingDateTime->isPast()) {
            $fail('The booking date and time must be in the future.');
        }
    }
}
