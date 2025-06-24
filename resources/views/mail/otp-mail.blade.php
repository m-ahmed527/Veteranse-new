<x-mail::message>
    # OTP Mail

    {{ $message }}.
    This OTP will expire in 5 minutes.


    Thanks,
    {{ config('app.name') }}
</x-mail::message>
