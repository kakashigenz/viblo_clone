<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentService
{
    protected ArticleService $article_service;

    public function __construct(ArticleService $service)
    {
        $this->article_service = $service;
    }

    /**
     * get list comment group by article use pagination 
     */
    public function getList(string $slug, int $size)
    {
        $article = $this->article_service->find($slug);
        $comments = Comment::with(['user', 'subComments' => function ($query) {
            $query->with('user')->limit(2);
        }])->where('article_id', data_get($article, 'id'))->whereNull('parent_id')->paginate($size);

        return [
            'data' => $comments->items(),
            'page' => $comments->currentPage(),
            'size' => $comments->perPage(),
            'total' => $comments->total()
        ];
    }


    /**
     * create a comment
     */
    public function create(array $data, string $slug, string $user_id)
    {
        $article = $this->article_service->find($slug);
        $comment = new Comment($data);
        $comment->user_id = $user_id;
        $comment->point = 0;
        $comment->is_visible = true;
        $article->comments()->save($comment);
        return $comment;
    }

    /**
     * Get a comment
     */
    public function find(string $id)
    {
        return Comment::query()->findOrFail($id);
    }

    /**
     * update a comment
     */
    public function update(array $data, string $id)
    {
        $comment = $this->find($id);
        Gate::authorize('edit', $comment);
        $comment->fill($data);
        $comment->save();
    }

    /**
     * delete a comment
     */
    public function delete(string $id)
    {
        $comment = $this->find($id);
        Gate::authorize('edit', $comment);
        $comment->delete();
    }

    /**
     * reply comment
     */
    public function reply(array $data, string $comment_id, string $user_id)
    {
        $comment = $this->find($comment_id);

        $sub_comment = new Comment($data);
        $sub_comment->user_id = $user_id;
        $sub_comment->article_id = data_get($comment, 'article_id');
        $sub_comment->point = 0;
        $sub_comment->is_visible = true;
        $comment->subComments()->save($sub_comment);

        return $sub_comment;
    }

    public function getSubComment() {}
}
