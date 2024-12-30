<?php

namespace App\Services;

use App\Events\PostComment;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Models\Vote;
use App\Notifications\CommentPosted;
use Illuminate\Support\Facades\Gate;

class CommentService
{
    /**
     * get list comment group by article use pagination
     */
    public function getList(string $slug, int $size): array
    {
        $article = Article::query()->withoutGlobalScope('public')->where('slug', $slug)->firstOrFail();
        $comments = Comment::with(['user'])->withCount('subComments')->where('article_id', data_get($article, 'id'))->whereNull('parent_id')->orderByDesc('point')->paginate($size);

        $current_user = auth()->guard('sanctum')->user();

        $votes = Vote::query()->where('user_id', data_get($current_user, 'id'))->where('voteable_type', Comment::class)->get()->groupBy('voteable_id');
        foreach ($comments as $comment) {
            $vote = data_get($votes, sprintf("%s", data_get($comment, 'id')));
            $vote_type = data_get(data_get($vote, '0'), 'type');
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
    public function create(array $data, string $slug, User $user): Comment
    {
        $article = Article::query()->where('slug', $slug)->firstOrFail();
        $comment = new Comment($data);
        $comment->user_id = data_get($user, 'id');
        $comment->point = 0;
        $comment->is_visible = true;
        $article->comments()->save($comment);
        $author = User::query()->find(data_get($article, 'user_id'));
        broadcast(new PostComment($comment->load('user'), $slug, 'create'))->toOthers();
        $author->notify(new CommentPosted($user, 'đã bình luận bài viết của bạn'));
        return $comment->load('user');
    }

    /**
     * Get a comment
     */
    public function find(string $id): Comment
    {
        return Comment::query()->findOrFail($id);
    }

    /**
     * update a comment
     */
    public function update(array $data, string $id): Comment
    {
        $comment = Comment::query()->findOrFail($id);
        Gate::authorize('edit', $comment);
        $comment->fill($data);
        $comment->save();
        $article = Article::query()->where('id', data_get($comment, 'article_id'))->first();
        broadcast(new PostComment($comment->load('user'), data_get($article, 'slug'), 'edit'))->toOthers();
        return $comment;
    }

    /**
     * delete a comment
     */
    public function delete(string $id): void
    {
        $comment = Comment::query()->findOrFail($id);
        Gate::authorize('edit', $comment);
        $comment->delete();
    }

    /**
     * reply comment
     */
    public function reply(array $data, string $comment_id, User $user): Comment
    {
        $comment = Comment::query()->findOrFail($comment_id);
        $author = $comment->user;
        $article = $comment->article;

        $matches = [];
        preg_match_all("/@(\w+)/", data_get($data, 'content'), $matches);
        $receivers = User::query()->whereIn('user_name', $matches[1])->get();

        $sub_comment = new Comment($data);
        $sub_comment->user_id = data_get($user, 'id');
        $sub_comment->article_id = data_get($comment, 'article_id');
        $sub_comment->point = 0;
        $sub_comment->is_visible = true;
        $comment->subComments()->save($sub_comment);
        broadcast(new PostComment($sub_comment->load('user'), data_get($article, 'slug'), 'create'))->toOthers();

        if ($receivers->isEmpty()) {
            if (data_get($user, 'id') !== data_get($author, 'id')) {
                $author->notify(new CommentPosted($user, 'đã bình luận trả lời bình luận của bạn'));
            }
        } else {
            foreach ($receivers as $receiver) {
                if (data_get($user, 'id') !== data_get($receiver, 'id')) {
                    $receiver->notify(new CommentPosted($user, 'đã nhắc đến bạn'));
                }
            }
        }
        return $sub_comment->load('user');
    }

    public function getSubComments(string $comment_id, int $size): array
    {
        $comments = Comment::with('user')->where('parent_id', $comment_id)->orderByDesc('point')->paginate($size);

        return [
            'data' => $comments->items(),
            'hasNext' => $comments->hasMorePages()
        ];
    }
}
