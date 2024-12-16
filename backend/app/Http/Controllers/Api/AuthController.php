<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $this->service->register($data);
        return response()->json(['message' => 'success']);
    }

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
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        return ['message' => 'success'];
    }

    public function checkAuthorization(Request $request)
    {
        return $request->user();
    }
}
