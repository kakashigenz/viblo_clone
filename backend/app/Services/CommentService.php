<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentService
{
    protected $article_service;

    public function __construct(ArticleService $service)
    {
        $this->article_service = $service;
    }

    /**
     * get list comment group by article use pagination 
     */
    public function getList(string $slug, int $start, int $size)
    {
        $article = $this->article_service->find($slug);
        $comments = $article->comments()->skip($start)->take($size)->get();
        foreach ($comments as $comment) {
            $comment->user;
        }
        return $comments;
    }

    /**
     * create a comment
     */
    public function create(array $data, string $slug)
    {
        $article = $this->article_service->find($slug);
        $comment = new Comment($data);
        $comment->user_id = Auth::user()->id;
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
}
