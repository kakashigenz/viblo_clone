<?php

namespace App\Services;

use App\Events\PostComment;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class CommentService
{
    /**
     * get list comment group by article use pagination
     */
    public function getList(string $slug, int $size)
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();
        $comments = Comment::with(['user'])->withCount('subComments')->where('article_id', data_get($article, 'id'))->whereNull('parent_id')->orderByDesc('point')->paginate($size);

        $current_user = auth()->guard('sanctum')->user();
        foreach ($comments as $comment) {
            $vote_type = data_get($comment->votes()->where('user_id', data_get($current_user, 'id'))->first(), 'type');
            $comment['vote_type'] = $vote_type;
        }
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
    public function create(array $data, string $slug, User $user)
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();
        $comment = new Comment($data);
        $comment->user_id = data_get($user, 'id');
        $comment->point = 0;
        $comment->is_visible = true;
        $article->comments()->save($comment);
        $comment['user'] = $user->only(['avatar', 'id', 'name', 'user_name']);
        broadcast(new PostComment($comment, $slug))->toOthers();
        return $comment->load('user');
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
        $comment = Comment::query()->findOrFail($id);
        Gate::authorize('edit', $comment);
        $comment->fill($data);
        $comment->save();
        $article = Article::query()->where('id', data_get($comment, 'article_id'))->first();
        broadcast(new PostComment($comment->load('user'), data_get($article, 'slug')))->toOthers();
    }

    /**
     * delete a comment
     */
    public function delete(string $id)
    {
        $comment = Comment::query()->findOrFail($id);
        Gate::authorize('edit', $comment);
        $comment->delete();
    }

    /**
     * reply comment
     */
    public function reply(array $data, string $comment_id, string $user_id)
    {
        $comment = Comment::query()->findOrFail($comment_id);

        $sub_comment = new Comment($data);
        $sub_comment->user_id = $user_id;
        $sub_comment->article_id = data_get($comment, 'article_id');
        $sub_comment->point = 0;
        $sub_comment->is_visible = true;
        $comment->subComments()->save($sub_comment);

        return $sub_comment->load('user');
    }

    public function getSubComments(string $comment_id, int $size)
    {
        $comments = Comment::with('user')->where('parent_id', $comment_id)->orderByDesc('point')->paginate($size);

        return [
            'data' => $comments->items(),
            'hasNext' => $comments->hasMorePages()
        ];
    }
}
