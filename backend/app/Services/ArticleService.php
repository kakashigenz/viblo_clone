<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
        $data = Article::with('tags')->skip($start)->take($size)->get();
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
        try {
            DB::beginTransaction();
            $slug = Str::slug(data_get($data, 'title'));
            if (Article::query()->where('slug', $slug)->first()) {
                $slug .= '-' . Str::random(8);
            }

            $addition_data = [
                'slug' => $slug,
                'point' => 0,
                'status' => Article::VISIBLE,
                'view' => 0
            ];

            $data = array_merge($data, $addition_data);
            $article = new Article($data);
            $article->user_id = 1;
            $article->save();

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
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    /**
     * Find an article
     */
    public function find(string $slug): Article
    {
        $item = Article::with('tags')->where('slug', $slug)->firstOrFail();
        return $item;
    }

    /**
     * Update an article
     */
    public function update(array $data, string $slug): bool
    {
        try {
            $article = Article::query()->where('slug', $slug)->firstOrFail();
            Gate::authorize('edit', $article);

            DB::beginTransaction();

            $new_slug = Str::slug(data_get($data, 'title'));
            if ($new_slug !== $slug) {
                if (Article::query()->where('slug', $new_slug)->whereNot('id', data_get($article, 'id'))->first()) {
                    $new_slug .= '-' . Str::random(8);
                }
            }

            $addition_data = [
                'slug' => $new_slug,
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
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Delete an article
     */
    public function delete(string $slug): bool
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();
        Gate::authorize('edit', $article);
        return $article->delete();
    }
}
