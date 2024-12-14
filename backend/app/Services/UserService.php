<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    public function changePassword(User $user, string $password, string $new_password)
    {
        if (!Hash::check($password, data_get($user, 'password'))) {
            abort(400, 'Mật khẩu cũ không chính xác');
        }
        $user->password = Hash::make($new_password);
        $user->save();
    }
}
