<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

class VoteService
{
    protected $article_service;
    protected $comment_service;

    public function __construct(ArticleService $article_service, CommentService $comment_service)
    {
        $this->article_service = $article_service;
        $this->comment_service = $comment_service;
    }

    public function upvote(string $user_id, string $id, int $type): int
    {
        switch ($type) {
            case Vote::TYPE_ARTICLE:
                $article = $this->article_service->findById($id);
                $vote = $article->votes()->where('user_id', $user_id)->first();

                //unvote if exist
                if ($vote && data_get($vote, 'type') === Vote::UPVOTE) {
                    $article->point -= 1;
                    $article->save();
                    $vote->delete();
                    return Vote::UNVOTE;
                } else if ($vote && data_get($vote, 'type') === Vote::DOWNVOTE) {
                    $article->point += 1;
                    $article->save();
                    $vote->delete();
                }

                //upvote
                DB::transaction(function () use ($user_id, $article) {
                    $new_vote = new Vote();
                    $new_vote->type = Vote::UPVOTE;
                    $new_vote->user_id = $user_id;
                    $article->votes()->save($new_vote);
                    $article->point += 1;
                    $article->save();
                });
                break;

            case Vote::TYPE_COMMENT:
                $comment = $this->comment_service->find($id);
                $vote = $comment->votes()->where('user_id', $user_id)->first();

                //unvote if exist
                if ($vote && data_get($vote, 'type') === Vote::UPVOTE) {
                    $comment->point -= 1;
                    $comment->save();
                    $vote->delete();
                    return Vote::UNVOTE;
                } else if ($vote && data_get($vote, 'type') === Vote::DOWNVOTE) {
                    $comment->point += 1;
                    $comment->save();
                    $vote->delete();
                }

                //upvote
                DB::transaction(function () use ($user_id, $comment) {
                    $vote = new Vote();
                    $vote->type = Vote::UPVOTE;
                    $vote->user_id = $user_id;
                    $comment->votes()->save($vote);
                    $comment->point += 1;
                    $comment->save();
                });
                break;

            default:
                # code...
                break;
        }
        return Vote::UPVOTE;
    }

    public function downvote(string $user_id, string $id, int $type): int
    {
        switch ($type) {
            case Vote::TYPE_ARTICLE:
                $article = $this->article_service->findById($id);
                $vote = $article->votes()->where('user_id', $user_id)->first();
                //unvote if exist
                if ($vote && data_get($vote, 'type') === Vote::UPVOTE) {
                    $article->point -= 1;
                    $article->save();
                    $vote->delete();
                } else if ($vote && data_get($vote, 'type') === Vote::DOWNVOTE) {
                    $article->point += 1;
                    $article->save();
                    $vote->delete();
                    return Vote::UNVOTE;
                }

                //downvote
                DB::transaction(function () use ($user_id, $article) {
                    $new_vote = new Vote();
                    $new_vote->type = Vote::DOWNVOTE;
                    $new_vote->user_id = $user_id;
                    $article->votes()->save($new_vote);
                    $article->point -= 1;
                    $article->save();
                });
                break;

            case Vote::TYPE_COMMENT:
                $comment = $this->comment_service->find($id);
                $vote = $comment->votes()->where('user_id', $user_id)->first();
                //unvote if exist
                if ($vote && data_get($vote, 'type') === Vote::UPVOTE) {
                    $comment->point -= 1;
                    $comment->save();
                    $vote->delete();
                } else if ($vote && data_get($vote, 'type') === Vote::DOWNVOTE) {
                    $comment->point += 1;
                    $comment->save();
                    $vote->delete();
                    return Vote::UNVOTE;
                }

                //downvote
                DB::transaction(function () use ($user_id, $comment) {
                    $vote = new Vote();
                    $vote->type = Vote::DOWNVOTE;
                    $vote->user_id = $user_id;
                    $comment->votes()->save($vote);
                    $comment->point -= 1;
                    $comment->save();
                });
                break;

            default:
                # code...
                break;
        }
        return Vote::DOWNVOTE;
    }

    protected function unvote() {}
}
