<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Article;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Parsedown;
use OpenApi\Attributes as OA;

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
    #[OA\Schema(
        schema: "ListArticleResponse",
        properties: [
            new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(ref: "#/components/schemas/ListArticleResource")
            ),
            new OA\Property(
                property: 'page',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'size',
                type: 'integer',
                example: 10
            ),
            new OA\Property(
                property: 'total',
                type: 'integer',
                example: 100
            )
        ]
    )]
    #[OA\Schema(
        schema: "ListArticleResource",
        properties: [
            new OA\Property(property: 'title', type: 'string', example: "Why we should lear Python"),
            new OA\Property(property: 'content', type: 'string', example: "Because Python is fun!"),
            new OA\Property(property: 'slug', type: 'string', example: "why-we-should-learn-python"),
            new OA\Property(property: 'point', type: 'number', example: 100),
            new OA\Property(property: 'status', type: 'integer', enum: [Article::VISIBLE, Article::DRAFT], example: Article::VISIBLE),
            new OA\Property(property: 'view', type: 'integer', example: 100),
            new OA\Property(property: 'user', type: 'object', ref: "#/components/schemas/User"),
            new OA\Property(property: 'bookmarks_count', type: 'integer', example: 50),
            new OA\Property(property: 'comments_count', type: 'integer', example: 3),
            new OA\Property(property: 'votes_count', type: 'integer', example: 10),
        ]
    )]
    public function getList(int $size)
    {
        $articles = Article::with(['tags', 'user'])->withCount(['bookmarks', 'comments', 'votes'])->orderByDesc('created_at')->paginate($size);
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
    #[OA\Schema(
        schema: "ArticleResponse",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "title", type: "string", example: "Why we should learn Python"),
            new OA\Property(property: "content", type: "string", example: "Because Python is fun!"),
            new OA\Property(property: "slug", type: "string", example: "why-we-should-learn-python"),
            new OA\Property(property: "point", type: "number", example: 100),
            new OA\Property(property: "status", type: "integer", example: 2),
            new OA\Property(property: "view", type: "integer", example: 100),
            new OA\Property(property: "tags", type: "array", items: new OA\Items(ref: "#/components/schemas/Tag")),
            new OA\Property(property: "bookmarks_count", type: "integer", example: 10),
            new OA\Property(property: "comments_count", type: "integer", example: 5),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(
                property: "user",
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "user_name", type: "string", example: "johndoe"),
                    new OA\Property(property: "avatar", type: "string", example: "avatar.jpg"),
                    new OA\Property(property: "is_banned", type: "boolean", example: false),
                    new OA\Property(property: "followings_count", type: "integer", example: 100),
                    new OA\Property(property: "articles_count", type: "integer", example: 20),
                    new OA\Property(property: "is_following", type: "boolean", example: true),
                ]
            ),
            new OA\Property(
                property: "vote_type",
                type: "integer",
                enum: [Vote::UPVOTE, Vote::DOWNVOTE, null],
                example: null
            )
        ]
    )]
    public function find(?User $current_user, string $slug): Article
    {
        $article = Article::with(['tags'])->withoutGlobalScope('public')->withCount(['bookmarks', 'comments'])
            ->where('slug', $slug)->first();
        throw_if(!$article, new ResourceNotFoundException("Không tìm thấy bài viết"));
        #avoid use where in collumn isn't created index
        if ($article->status == Article::DRAFT && $article->user_id != data_get($current_user, 'id')) {
            abort(404, 'Not found');
        }

        $user = User::query()->where('id', data_get($article, 'user_id'))
            ->select([
                'id',
                'name',
                'user_name',
                'avatar',
                'is_banned',
            ])->withCount(['followings', 'articles'])
            ->first();

        $is_following = !empty($user->followers()->where('follower_id', data_get($current_user, 'id'))->first());

        $vote_type = data_get($article->votes()->where('user_id', data_get($current_user, 'id'))->first(), 'type');
        $user['is_following'] = $is_following;
        $article['user'] = $user;
        $article['vote_type'] = $vote_type;
        return $article;
    }

    /**
     * Update an article
     */
    public function update(array $data, string $slug): Article
    {
        try {
            $article = Article::query()->withoutGlobalScope('public')->where('slug', $slug)->first();
            throw_if(!$article, new ResourceNotFoundException("Không tìm thấy bài viết"));
            Gate::authorize('edit', $article);

            DB::beginTransaction();

            $new_slug = Str::slug(data_get($data, 'title'));
            if ($new_slug !== $slug) {
                if (Article::query()->withoutGlobalScope('public')
                    ->where('slug', $new_slug)->whereNot('id', data_get($article, 'id'))->first()
                ) {
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
            return $article;
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
        $article = Article::query()->withoutGlobalScope('public')->where('slug', $slug)->first();
        throw_if(!$article, new ResourceNotFoundException("Không tìm thấy bài viết"));
        Gate::authorize('edit', $article);
        return $article->delete();
    }

    /**
     * get an article by id
     */
    public function findById(string $id): Article
    {
        return Article::query()->findOrFail($id);
    }

    public function getMyArticles(int $type, User $user): array
    {
        $articles = [];
        $size = 10;
        if ($type == Article::DRAFT) {
            $articles = Article::with(['tags'])->withoutGlobalScope('public')->where('user_id', data_get($user, 'id'))
                ->where('status', Article::DRAFT)->select(['id', 'title', 'slug', 'updated_at'])->paginate($size);
        } else {
            $articles = Article::with(['tags'])->where('user_id', data_get($user, 'id'))->select(['id', 'title', 'slug', 'updated_at'])->paginate($size);
        }
        return [
            'data' => $articles->items(),
            'page' => $articles->currentPage(),
            'size' => $articles->perPage(),
            'total' => $articles->total()
        ];
    }
}
