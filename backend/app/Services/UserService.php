<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * get an user
     */
    public function find(string $user_name)
    {
        return User::query()->where('user_name', $user_name)->firstOrFail();
    }

    /**
     * update an user
     */
    public function update(array $data, string $user_name)
    {
        return User::query()->where('user_name', $user_name)->update($data);
    }
}
