<?php

namespace App\Services;

use App\Models\User;

class FollowingTagService
{
    protected TagService $tag_service;
    protected UserService $user_service;


    public function __construct(TagService $tag_service, UserService $user_service)
    {
        $this->tag_service = $tag_service;
        $this->user_service = $user_service;
    }

    /**
     * follow a tag
     */
    public function follow(User $follower, string $tag_slug): bool
    {
        $tag = $this->tag_service->find($tag_slug);

        if ($follower->tags()->wherePivot('tag_id', data_get($tag, 'id'))->first()) {
            return false;
        }

        $follower->tags()->attach(data_get($tag, 'id'), ['created_at' => now(), 'updated_at' => now()]);

        return true;
    }

    /**
     * get tag following list
     */
    public function getList(string $user_name, int $size)
    {
        $follower = $this->user_service->find($user_name);

        $tags = $follower->tags()->paginate($size);

        return [
            'data' => $tags->items(),
            'page' => $tags->currentPage(),
            'size' => $tags->perPage(),
            'total' => $tags->total()
        ];
    }
}
