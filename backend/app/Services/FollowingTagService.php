<?php

namespace App\Services;

use App\Models\User;

class FollowingTagService
{
    protected TagService $tag_service;

    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
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
}
