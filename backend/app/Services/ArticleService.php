<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Parsedown;

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
        $articles = Article::with(['tags', 'user'])->withCount(['bookmarks', 'comments', 'votes'])->paginate($size);
        $char_limit = 160;

        $article_content = array_map(function ($article) use ($char_limit) {
            $parsed_content = Parsedown::instance()->text(data_get($article, 'content'));
            $len_origin = strlen(data_get($article, 'content'));

            $matches = [];
            $truncated_content = '';
            preg_match_all("/<p\b>.*?<\/p\b>/", $parsed_content, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as $match) {
                $truncated_content .= preg_replace("/<(?:\/?)(p|strong|em|h[1-6])\b[^>]*>/", '', $match[0]);
            }

            $article['content'] = mb_substr($truncated_content, 0, $char_limit);
            if ($char_limit < $len_origin) {
                $article['content'] .= '...';
            }
            return $article;
        }, $articles->items());

        return [
            'data' => $article_content,
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
    public function find(string $slug): Article
    {
        $article = Article::with(['tags'])->withCount(['bookmarks', 'comments', 'votes'])->where('slug', $slug)->firstOrFail();
        $user = User::query()->where('id', data_get($article, 'user_id'))
            ->select([
                'id',
                'name',
                'user_name',
                'avatar',
                'is_banned',
            ])->withCount(['followings', 'articles'])
            ->first();

        $current_user = auth()->guard()->user();
        $follower = $user->followers->first(function ($key, $value) use ($current_user) {
            data_get($value, 'id') === data_get($current_user, 'id');
        });
        $user['is_following'] = !empty($follower);
        $article['user'] = $user;
        return $article;
    }

    /**
     * Update an article
     */
    public function update(array $data, string $slug): bool
    {
        try {
            $article = Article::query()->where('slug', $slug)->firstOrFail()($slug);
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
        $article = Article::query()->where('slug', $slug)->firstOrFail();
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
