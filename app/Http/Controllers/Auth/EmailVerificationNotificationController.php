<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $request->user()->forceFill(['verification_code' => $code])->save();
        
        \Illuminate\Support\Facades\Mail::to($request->user()->email)->send(new \App\Mail\VerificationCodeMail($code));

        return back()->with('status', 'verification-link-sent');
    }
}
