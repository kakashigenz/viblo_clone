<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Services\BookmarkService;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    protected BookmarkService $service;

    public function __construct(BookmarkService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $article_slug)
    {
        $user = $request->user();

        $status = $this->service->bookmark($user, $article_slug);
        if (empty($status)) {
            abort(400, 'Bad request');
        }
        return ['message' => 'success'];
    }
}
