<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UnverifiedEmailException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    #[OA\Post(
        path: '/register',
        summary: 'Register a new user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/RegisterRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Register successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: "success"
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Unprocessable Content",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string'
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                        )
                    ]
                )
            )
        ]
    )]
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $this->service->register($data);
        return response()->json(['message' => 'success']);
    }

    #[OA\Post(
        path: '/login',
        summary: 'Login a user',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/LoginRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Failed to login',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Unverified Email',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string'
                        )
                    ]
                )
            )
        ]
    )]
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $res = $this->service->login($data);
        if (!$res) {
            return response()->json(['message' => 'Tài khoản hoặc mật khẩu không chính xác'], 401);
        }

        return $res;
    }

    public function spaLogin(LoginRequest $request)
    {
        $data = $request->validated();
        if (!$this->service->spaLogin($data)) {
            return response()->json(['message' => 'Tài khoản hoặc mật khẩu không chính xác'], 401);
        }
        if (!$request->user()->hasVerifiedEmail()) {
            abort(403, 'Vui lòng xác thực email!');
        }
        return [
            'message' => 'success',
            'user' => auth()->guard()->user()
        ];
    }

    public function logout(Request $request)
    {
        if ($request->bearerToken()) {
            $request->user()->currentAccessToken()->delete();
        } else {
            Auth::guard('web')->logout();
            Session::invalidate();
            Session::regenerateToken();
        }
        return ['message' => 'success'];
    }

    public function checkAuthorization(Request $request)
    {
        return $request->user();
    }

    public function getResetForm(Request $request, string $token)
    {
        if (!$this->service->isValidResetToken($request->input('email'), $token)) {
            return redirect(sprintf("%s/not-found", env('FRONTEND_URL')));
        }
        return redirect(sprintf("%s?token=%s&email=%s", env('FRONTEND_URL') . '/reset-password', $token, $request->input('email')));
    }

    public function sendLinkResetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email'
        ]);
        $res = $this->service->sendLinkResetPassword($data);
        $status = $res == Password::RESET_LINK_SENT ? 'success' : 'fail';
        return ['status' => $status, 'data' => [
            'message' => __($res)
        ]];
    }

    public function resetPassword(Request $request)
    {
        $data =  $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $res = $this->service->resetPassword($data);
        $status = $res == Password::PASSWORD_RESET ? 'success' : 'fail';
        return ['status' => $status, 'data' => [
            'message' => __($res)
        ]];
    }
}
