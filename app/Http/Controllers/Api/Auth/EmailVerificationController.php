<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        if (! URL::hasValidSignature($request)) {
            return responseError('Invalid or expired verification link.', 400);
        }

        // Make sure the user's email hasn't been verified already
        if ($user->hasVerifiedEmail()) {
            return responseError('Email is already verified.', 400);
        }
        $user->markEmailAsVerified();

        return responseSuccess('Email verified successfully.', null);
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return responseError('No user found with this email.', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return responseError('Email is already verified.', 400);
        }

        // Send the verification notification again
        $user->sendEmailVerificationNotification();

        return responseSuccess('Verification email resent successfully.', null);
    }
}
