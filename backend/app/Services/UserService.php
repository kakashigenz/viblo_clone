<?php

namespace App\Services;

use App\Exceptions\IncorrectPasswordException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * get an user
     */
    public function find(string $user_name, array $relations = []): User
    {
        return User::with($relations)->where('user_name', $user_name)->firstOrFail();
    }

    /**
     * update an user
     */
    public function update(array $data, User $user): void
    {
        $user->fill($data);
        $user->save();
    }

    public function updateAvatar(User $user, string $name): string
    {
        $path = '';
        $location = sprintf('%s%s', $path, $user->getRawOriginal('avatar'));
        if (trim($location) && Storage::exists($location)) {
            Storage::delete($location);
        }
        $user->avatar = $name;
        $user->save();
        return data_get($user, 'avatar');
    }
    public function changePassword(User $user, string $password, string $new_password): void
    {
        if (!Hash::check($password, data_get($user, 'password'))) {
            throw new IncorrectPasswordException("Mật khẩu không chính xác");
        }
        $user->password = Hash::make($new_password);
        $user->save();
    }

    public function getUsersOrderByFollower(): Collection
    {
        return User::query()->withCount(['followers', 'articles'])->orderBy('followers_count', 'desc')->take(3)->get();
    }
}
