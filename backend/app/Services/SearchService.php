<?php

namespace App\Services;

use App\Models\Article;

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
}
