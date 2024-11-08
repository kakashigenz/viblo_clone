<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

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

        $token = $this->service->login($data);
        if (!$token) {
            return response()->json(['message' => 'Tài khoản hoặc mật khẩu không chính xác'], 401);
        }
        return response()->json(['token' => $token], 200);
    }

    public function spaLogin(LoginRequest $request)
    {
        $data = $request->validated();
        if (!$this->service->spaLogin($data)) {
            return response()->json(['message' => 'Tài khoản hoặc mật khẩu không chính xác'], 401);
        }
        return response()->json(['message' => 'success'], 200);
    }
}
