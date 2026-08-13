<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, please enter the 6-digit verification code we emailed to you.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification code has been sent to the email address you provided during registration.') }}
        </div>
    @endif
    
    @if ($errors->has('code'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ $errors->first('code') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.verify.code') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Verification Code')" />
            <x-text-input id="code" class="block mt-1 w-full text-center tracking-widest text-lg font-bold" type="text" name="code" required autofocus autocomplete="one-time-code" maxlength="6" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Verify Account') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 flex items-center justify-between border-t pt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Resend Code') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
