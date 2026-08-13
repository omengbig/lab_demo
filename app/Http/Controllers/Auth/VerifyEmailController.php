<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($user->verification_code === $request->code) {
            $user->markEmailAsVerified();
            $user->forceFill(['verification_code' => null])->save();
            event(new Verified($user));

            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        return back()->withErrors(['code' => 'The provided verification code is incorrect.']);
    }
}
