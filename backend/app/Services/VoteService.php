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

    public function upvote(string $user_id, string $id, int $type): array
    {
        $value = 0;
        switch ($type) {
            case Vote::TYPE_ARTICLE:
                $article = $this->article_service->findById($id);
                $vote = $article->votes()->where('user_id', $user_id)->first();

                //unvote if exist
                if ($vote && data_get($vote, 'type') === Vote::UPVOTE) {
                    $article->point -= 1;
                    $article->save();
                    $vote->delete();
                    $value = $article->point;
                    return ['vote_type' => Vote::UNVOTE, 'value' => $value];
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
                $value = $article->point;
                break;

            case Vote::TYPE_COMMENT:
                $comment = $this->comment_service->find($id);
                $vote = $comment->votes()->where('user_id', $user_id)->first();

                //unvote if exist
                if ($vote && data_get($vote, 'type') === Vote::UPVOTE) {
                    $comment->point -= 1;
                    $comment->save();
                    $vote->delete();
                    $value = $comment->point;
                    return ['vote_type' => Vote::UNVOTE, 'value' => $value];
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
                $value = $comment->point;
                break;

            default:
                # code...
                break;
        }
        return ['vote_type' => Vote::UPVOTE, 'value' => $value];
    }

    public function downvote(string $user_id, string $id, int $type): array
    {
        $value = 0;
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
                    $value = $article->point;
                    return ['vote_type' => Vote::UNVOTE, 'value' => $value];
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
                $value = $article->point;
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
                    $value = $comment->point;
                    return ['vote_type' => Vote::UNVOTE, 'value' => $value];
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
                $value = $comment->point;
                break;

            default:
                # code...
                break;
        }
        return ['vote_type' => Vote::DOWNVOTE, 'value' => $value];
    }

    protected function unvote() {}
}
