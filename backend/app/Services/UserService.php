<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * get an user
     */
    public function find(string $user_name, array $relations = [])
    {
        return User::with($relations)->where('user_name', $user_name)->firstOrFail();
    }

    /**
     * update an user
     */
    public function update(array $data, User $user)
    {
        $user->fill($data);
        $user->save();
    }

    public function updateAvatar(User $user, string $name)
    {
        $path = '';
        $location = sprintf('%s%s', $path, $user->getRawOriginal('avatar'));
        if (Storage::exists($location)) {
            Storage::delete($location);
        }
        $user->avatar = $name;
        $user->save();
        return data_get($user, 'avatar');
    }
}
