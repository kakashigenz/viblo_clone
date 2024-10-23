<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Services\FollowingUserService;
use Illuminate\Http\Request;

class FollowingUserController extends Controller
{
    protected FollowingUserService $service;

    public function __construct(FollowingUserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request, string $user_name)
    {
        $data = $request->validated();
        $size = data_get($data, 'size', 15);
        $res = $this->service->getList($user_name, $size);
        return $res;
    }

    /**
     * Follow an user
     */
    public function follow(Request $request, string $user_name)
    {
        $follower = $request->user();

        $status = $this->service->follow($follower, $user_name);

        if (!$status) {
            abort(400, 'Bad request');
        }

        return ['message' => 'success'];
    }

    /**
     * Unfollow an user
     */
    public function unfollow(Request $request, string $user_name)
    {
        $follower = $request->user();

        $status = $this->service->unfollow($follower, $user_name);

        if (!$status) {
            abort(400, 'Bad request');
        }

        return ['message' => 'success'];
    }
}
