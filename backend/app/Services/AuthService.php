<?php

namespace App\Services;

use App\Exceptions\UnverifiedEmailException;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthService
{
    /**
     * Register an user
     */
    public function register(array $data): void
    {
        $data['password'] = bcrypt(data_get($data, 'password'));
        $user = new User($data);
        $user->role = User::USER_ROLE;
        $user->is_banned = false;
        $user->save();
        event(new Registered($user));
    }

    /**
     * Login and get a api key
     */
    #[OA\Schema(
        schema: 'LoginResponse',
        properties: [
            new OA\Property(property: 'user', type: 'object'),
            new OA\Property(property: 'token', type: 'string')
        ]
    )]
    public function login(array $data): ?array
    {
        $user = User::query()->where('user_name', data_get($data, 'user_name'))
            ->orWhere('email', data_get($data, 'user_name'))->first();
        if ($user && Hash::check(data_get($data, 'password'), data_get($user, 'password'))) {
            if (!$user->hasVerifiedEmail()) {
                throw new UnverifiedEmailException("Email chưa được xác thực");
            }
            return [
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken,
            ];
        }

        return null;
    }

    /**
     * Login with session
     */
    public function spaLogin(array $data): bool
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
