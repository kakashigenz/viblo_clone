<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;

class BookmarkService
{
    protected UserService $user_service;


    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * bookmark an article
     */
    public function bookmark(User $user, string $article_slug): bool
    {
        $article = Article::query()->where('slug', $article_slug)->firstOrFail();

        if ($user->bookmarks()->wherePivot('article_id', data_get($article, 'id'))->first()) {
            return false;
        }

        $user->bookmarks()->attach(data_get($article, 'id'), ['created_at' => now(), 'updated_at' => now()]);
        return true;
    }

    /**
     * get bookmark list
     */
    public function getList(string $user_name, int $size)
    {
        $user = $this->user_service->find($user_name);

        $bookmarks = $user->bookmarks()->paginate($size);

        return [
            'data' => $bookmarks->items(),
            'page' => $bookmarks->currentPage(),
            'size' => $bookmarks->perPage(),
            'total' => $bookmarks->total()
        ];
    }

    /**
     * unbookmark an article
     */
    public function unbookmark(User $user, string $article_slug): bool
    {
        $article = Article::query()->where('slug', $article_slug)->firstOrFail();

        if (empty($user->bookmarks()->wherePivot('article_id', data_get($article, 'id'))->first())) {
            return false;
        }

        $user->bookmarks()->detach(data_get($article, 'id'));
        return true;
    }
}
