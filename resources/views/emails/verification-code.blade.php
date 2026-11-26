<x-mail::message>
# Verify Your Email Address

Thanks for creating an account! Please use the following 6-digit verification code to complete your registration.

<x-mail::panel>
# {{ $code }}
</x-mail::panel>

If you did not create an account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
