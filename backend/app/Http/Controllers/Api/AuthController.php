<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $service;

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

    public function login(Request $request)
    {
        $data = $request->validate([
            'user_name' => 'required',
            'password' => 'required'
        ]);

        $token = $this->service->login($data);
        if (!$token) {
            return response()->json(['message' => 'Tài khoản hoặc mật khẩu không chính xác'], 401);
        }
        return response()->json(['token' => $token], 200);
    }
}
