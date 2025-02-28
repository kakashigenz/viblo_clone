<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\IncorrectPasswordException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $user_name)
    {
        return $this->service->find($user_name);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $this->service->update($data, $user);
        return ['message' => 'success'];
    }

    public function getCurrentUser()
    {
        return Auth::user();
    }

    public function updateAvatar(StoreImageRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $new_avatar = $this->service->updateAvatar($user, data_get($data, 'name'));
        return ['new_avatar' => $new_avatar];
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $this->service->changePassword($user, data_get($data, 'password'), data_get($data, 'new_password'));
        return ['message' => 'success'];
    }

    public function getTopUser(Request $request)
    {
        return [
            'status' => 'success',
            'data' => $this->service->getUsersOrderByFollower()
        ];
    }
}
