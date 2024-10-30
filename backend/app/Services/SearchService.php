<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;

class SearchService
{

    public function searchArticle(string $query)
    {
        $articles = Article::search($query)->query(fn($query) => $query->with('user'))->paginate(10);
        return [
            'data' => $articles->items(),
            'page' => $articles->currentPage(),
            'size' => $articles->perPage(),
            'total' => $articles->total()
        ];
    }

    public function searchUser(string $query)
    {
        $users = User::search($query)->paginate(20);
        return [
            'data' => $users->items(),
            'page' => $users->currentPage(),
            'size' => $users->perPage(),
            'total' => $users->total()
        ];
    }
}
