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

    /**
     * get list following
     */
    public function getList(string $user_name, int $size): mixed
    {
        $follower = $this->user_service->find($user_name);

        $followings = $follower->followings()->paginate();
        return [
            'data' => $followings->items(),
            'page' => $followings->currentPage(),
            'size' => $followings->perPage(),
            'total' => $followings->total()
        ];
    }

    /**
     * Unfollow an user
     */
    public function unfollow(User $follower, string $user_name): bool
    {
        $user = $this->user_service->find($user_name);

        if (!$follower->followings()->wherePivot('user_id', data_get($user, 'id'))->first()) {
            return false;
        }

        $follower->followings()->detach(data_get($user, 'id'));
        return true;
    }
}
