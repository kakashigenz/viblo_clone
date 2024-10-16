<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function update(User $user, Article $article): bool
    {
        return data_get($user, 'id') === data_get($article, 'user_id');
    }

    public function delete(User $user, Article $article): bool
    {
        return data_get($user, 'id') === data_get($article, 'user_id');
    }
}
