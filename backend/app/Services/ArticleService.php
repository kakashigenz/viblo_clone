<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    protected $tag_service;

    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
    }

    /**
     * get list article use pagination
     */
    public function getList(int $start, int $size): array
    {
        $data = Article::query()->skip($start)->take($size)->get();
        // convert to text status
        foreach ($data as $item) {
            $item['status'] = Article::STATUS_VALUES[$item['status']]; //TODO: refactor code
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
        DB::beginTransaction();
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

        //add tags to article
        $tags = data_get($data, 'tags');

        $tag_ids = []; //array contain the tag ids that be sent
        foreach ($tags as $tag_name) {
            $tag = $this->tag_service->create([
                'name' => $tag_name
            ]);

            if (!$tag) {
                $tag = $this->tag_service->findTagByName((string)$tag_name);
            }
            $tag_ids[] = data_get($tag, 'id');
        }
        $article->tags()->sync($tag_ids);
        DB::commit();
        return $article;
    }


    /**
     * Find an article
     */
    public function find(string $slug): Article
    {
        $item = Article::query()->where('slug', $slug)->firstOrFail();
        $item['status'] = Article::STATUS_VALUES[$item['status']];
        //TODO: refactor code

        return $item;
    }

    /**
     * Update an article
     */
    public function update(array $data, string $slug): bool
    {
        DB::beginTransaction();
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

        // update tags
        $tags = data_get($data, 'tags');
        $tag_ids = [];

        foreach ($tags as $tag_name) {
            $tag = $this->tag_service->create([
                'name' => $tag_name
            ]);

            if (!$tag) {
                $tag = $this->tag_service->findTagByName((string)$tag_name);
            }
            $tag_ids[] = data_get($tag, 'id');
        }
        $article->tags()->sync($tag_ids);
        DB::commit();
        return true;
    }

    /**
     * Delete an article
     */
    public function delete(string $slug): bool
    {
        return Article::query()->where('slug', $slug)->firstOrFail()->delete();
    }
}
