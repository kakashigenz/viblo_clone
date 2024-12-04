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
        $res = null;
        if ($request->routeIs('article.upvote')) {
            $res = $this->service->upvote(data_get($user, 'id'), $id, Vote::TYPE_ARTICLE);
        } else if ($request->routeIs('comment.upvote')) {
            $res = $this->service->upvote(data_get($user, 'id'), $id, Vote::TYPE_COMMENT);
        }

        return array_merge(['message' => 'success'], $res);
    }

    /**
     * Downvote
     */
    public function downvote(Request $request, string $id)
    {
        $user = $request->user();
        $res = null;
        if ($request->routeIs('article.downvote')) {
            $res = $this->service->downvote(data_get($user, 'id'), $id, Vote::TYPE_ARTICLE);
        } else if ($request->routeIs('comment.downvote')) {
            $res = $this->service->downvote(data_get($user, 'id'), $id, Vote::TYPE_COMMENT);
        }

        return array_merge(['message' => 'success'], $res);
    }
}
