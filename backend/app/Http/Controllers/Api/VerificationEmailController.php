<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificationEmailRequest;
use Illuminate\Http\Request;

class VerificationEmailController extends Controller
{
    public function verify(VerificationEmailRequest $request)
    {
        if (!$request->user->hasVerifiedEmail()) {
            $request->user->markEmailAsVerified();
        }

        return redirect(env('FRONTEND_URL'));
    }

    public function resendEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return ['message' => 'success'];
    }
}
