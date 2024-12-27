<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificationEmailRequest;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class VerificationEmailController extends Controller
{
    public function verify(VerificationEmailRequest $request)
    {
        if (!$request->user->hasVerifiedEmail()) {
            $request->user->markEmailAsVerified();
        }

        return redirect(env('FRONTEND_URL'));
    }

    #[
        OA\Post(
            path: '/email/verification-notification',
            summary: 'Resend email verification notification',
            tags: ['Auth'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Success',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(
                                property: 'message',
                                type: 'string'
                            )
                        ]
                    )
                )
            ],
            security: [
                ['bearerAuth' => []]
            ]
        )
    ]
    public function resendEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return ['message' => 'success'];
    }
}
