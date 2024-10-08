<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleService
{
    /**
     * get list article use pagination
     */
    public function getList(int $start, int $size): array
    {
        $data = Article::query()->skip($start)->take($size)->get();
        // convert to text status
        foreach ($data as $item) {
            $item['status'] = Article::STATUS_VALUES[$item['status']];
        }
        $res = [
            'data' => $data,
            'total' => Article::all()->count()
        ];
        return $res;
    }

    /**
     * Create an article
     */
    public function create(array $data): Article
    {
        $slug = Str::slug(data_get($data, 'title'));
        if (Article::query()->where('slug', $slug)->first()) {
            $slug .= '-' . Str::random(8);
        }

        $addition_data = [
            'slug' => $slug,
            'user_id' => 1, // i will add logic get current user id
            'point' => 0,
            'status' => Article::VISIBLE,
            'view' => 0
        ];

        $data = array_merge($data, $addition_data);
        $article = Article::query()->create($data);
        return $article;
    }


    /**
     * Find an article
     */
    public function find(string $slug): Article
    {
        $item = Article::query()->where('slug', $slug)->firstOrFail();
        $item['status'] = Article::STATUS_VALUES[$item['status']];

        return $item;
    }

    /**
     * Update an article
     */
    public function update(array $data, string $slug): void
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();

        $new_slug = Str::slug(data_get($data, 'title'));
        if ($new_slug !== $slug) {
            if (Article::query()->where('slug', $new_slug)->whereNot('id', data_get($article, 'id'))->first()) {
                $new_slug .= '-' . Str::random(8);
            }
        }

        $addition_data = [
            'slug' => $new_slug,
            'user_id' => 1, // i will add logic get current user id
            'point' => 0,
            'status' => Article::VISIBLE,
            'view' => 0
        ];

        $data = array_merge($data, $addition_data);
        $article->update($data);
    }

    /**
     * Delete an article
     */
    public function delete(string $slug)
    {
        Article::query()->where('slug', $slug)->delete();
    }
}
