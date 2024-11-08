<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register an user
     */
    public function register(array $data)
    {
        $data['password'] = bcrypt(data_get($data, 'password'));
        $user = new User($data);
        $user->role = User::USER_ROLE;
        $user->is_banned = false;
        $user->save();
    }

    /**
     * Login and get a token
     */
    public function login(array $data): ?string
    {
        $user = User::query()->where('user_name', data_get($data, 'user_name'))
            ->orWhere('email', data_get($data, 'user_name'))->first();
        if ($user && Hash::check(data_get($data, 'password'), data_get($user, 'password'))) {
            return $user->createToken('api-token')->plainTextToken;
        }

        return null;
    }

    public function spaLogin(array $data)
    {
        $credentials = [
            'password' => data_get($data, 'password')
        ];

        if (filter_var(data_get($data, 'user_name'), FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = data_get($data, 'user_name');
        } else {
            $credentials['user_name'] = data_get($data, 'user_name');
        }

        if (!Auth::attempt($credentials)) {
            return false;
        }
        return true;
    }
}
