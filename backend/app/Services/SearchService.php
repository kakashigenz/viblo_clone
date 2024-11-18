<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Tag;
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

    public function searchMulti(string $query)
    {
        $articles = Article::search($query, function ($meiliSearch, string $query, array $options) {
            $options['attributesToHighlight'] = ['title', 'content'];
            $options['highlightPreTag'] = '<strong>';
            $options['highlightPostTag'] = '</strong>';
            $options['attributesToSearchOn'] = ['title', 'content'];
            $options['attributesToCrop'] = ['content'];
            $options['cropLength'] = 30;

            return $meiliSearch->search($query, $options);
        })->paginateRaw(3, '');
        $users = User::search($query, function ($meiliSearch, string $query, array $options) {
            $options['attributesToSearchOn'] = ['name', 'user_name'];

            return $meiliSearch->search($query, $options);
        })->query(fn($query) => $query->withCount('followings')->withCount('articles'))->paginate(3, '');
        $tags = Tag::search($query, function ($meiliSearch, string $query, array $options) {
            $options['attributesToSearchOn'] = ['name'];

            return $meiliSearch->search($query, $options);
        })->query(fn($query) => $query->withCount('followers')->withCount('articles'))->paginate(3, '');

        $formatted_articles = array_map(
            fn($item) => data_get($item, '_formatted'),
            data_get($articles->items(), 'hits')
        );

        $res = [
            'articles' => $formatted_articles,
            'users' => $users->items(),
            'tags' => $tags->items()
        ];
        return [
            'results' => $res
        ];
    }
}
