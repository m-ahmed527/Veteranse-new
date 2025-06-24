<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class AddOnForService implements ValidationRule
{
    protected $serviceId;

    public function __construct($serviceId)
    {
        $this->serviceId = $serviceId; // Store the service ID to compare with add-ons
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('add_on_service')
            ->where('service_id', $this->serviceId)
            ->where('add_on_id', $value)  // Check if the add-on ID exists for this service
            ->exists();

        if (!$exists) {
            $fail('The selected add-on is not available for this service.');
        }
    }
}
