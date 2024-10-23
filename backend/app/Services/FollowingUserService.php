<?php

namespace App\Services;

use App\Models\User;

class FollowingUserService
{
    protected UserService $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * Follow an user
     */
    public function follow(User $follower, string $user_name): bool
    {
        $user = $this->user_service->find($user_name);

        if ($follower->followings()->wherePivot('user_id', data_get($user, 'id'))->first()) {
            return false;
        }
        $follower->followings()->attach(data_get($user, 'id'), ['created_at' => now(), 'updated_at' => now()]);

        return true;
    }
}
