<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ArticleService
{
    protected TagService $tag_service;

    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
    }

    /**
     * get list article use pagination
     */
    public function getList(int $size)
    {
        $articles = Article::with('tags')->paginate($size);
        return [
            'data' => $articles->items(),
            'page' => $articles->currentPage(),
            'size' => $articles->perPage(),
            'total' => $articles->total()
        ];
    }

    /**
     * Create an article
     */
    public function create(array $data, string $user_id): Article
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
                'view' => 0
            ];

            $data = array_merge($data, $addition_data);
            $article = new Article($data);
            $article->user_id = $user_id;
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
            $article->tags()->attach($tag_ids, ['created_at' => now(), 'updated_at' => now()]);
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
    public function find(string $slug, array $relations = []): Article
    {
        if (empty($relations)) {
            return Article::query()->where('slug', $slug)->firstOrFail();
        }
        return Article::with($relations)->where('slug', $slug)->firstOrFail();
    }

    /**
     * Update an article
     */
    public function update(array $data, string $slug): bool
    {
        try {
            $article = $this->find($slug);
            Gate::authorize('update', $article);

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
        $article = $this->find($slug);
        Gate::authorize('update', $article);
        return $article->delete();
    }

    /**
     * get an article by id
     */
    public function findById(string $id)
    {
        return Article::query()->findOrFail($id);
    }
}
