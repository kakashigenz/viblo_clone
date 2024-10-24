<?php

namespace App\Services;

use App\Models\User;

class BookmarkService
{
    protected ArticleService $article_service;
    protected UserService $user_service;


    public function __construct(ArticleService $article_service, UserService $user_service)
    {
        $this->article_service = $article_service;
        $this->user_service = $user_service;
    }

    /**
     * bookmark an article
     */
    public function bookmark(User $user, string $article_slug): bool
    {
        $article = $this->article_service->find($article_slug);

        if ($user->bookmarks()->wherePivot('article_id', data_get($article, 'id'))->first()) {
            return false;
        }

        $user->bookmarks()->attach(data_get($article, 'id'), ['created_at' => now(), 'updated_at' => now()]);
        return true;
    }
}
