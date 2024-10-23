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
}
