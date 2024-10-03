<?php

namespace App\Services;

use App\Models\Article;

class ArticleService
{
    public function show()
    {
        return ['name' => 'abc', 'age' => 18];
    }

    public function create(array $data): Article
    {
        Article::query()->create($data);
        return new Article();
    }
}
