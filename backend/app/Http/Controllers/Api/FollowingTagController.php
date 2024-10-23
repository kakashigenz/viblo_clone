<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Services\FollowingTagService;
use Illuminate\Http\Request;

class FollowingTagController extends Controller
{
    protected FollowingTagService $service;

    public function __construct(FollowingTagService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request, string $user_name)
    {
        $data = $request->validated();
        $size = (int)data_get($data, 'size', 15);
        $res = $this->service->getList($user_name, $size);
        return $res;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function follow(Request $request, string $tag_slug)
    {
        $follower = $request->user();

        $status = $this->service->follow($follower, $tag_slug);

        if (empty($status)) {
            abort(400, 'Bad request');
        }
        return ['message' => 'success'];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
