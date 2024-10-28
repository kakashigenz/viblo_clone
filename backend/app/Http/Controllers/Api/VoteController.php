<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use App\Services\VoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class VoteController extends Controller
{
    protected VoteService $service;

    public function __construct(VoteService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Upvote
     */
    public function upvote(Request $request, string $id)
    {
        $user = $request->user();
        $status = null;
        if ($request->routeIs('article.upvote')) {
            $status = $this->service->upvote(data_get($user, 'id'), $id, Vote::TYPE_ARTICLE);
        } else if ($request->routeIs('comment.upvote')) {
            $status = $this->service->upvote(data_get($user, 'id'), $id, Vote::TYPE_COMMENT);
        }

        return ['message' => 'success', 'statusVote' => $status];
    }

    /**
     * Downvote
     */
    public function downvote(Request $request, string $id)
    {
        $user = $request->user();
        $status = null;
        if ($request->routeIs('article.downvote')) {
            $status = $this->service->downvote(data_get($user, 'id'), $id, Vote::TYPE_ARTICLE);
        } else if ($request->routeIs('comment.downvote')) {
            $status = $this->service->downvote(data_get($user, 'id'), $id, Vote::TYPE_COMMENT);
        }

        return ['message' => 'success', 'statusVote' => $status];
    }
}
