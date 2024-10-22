<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreUpdateCommentRequest;
use App\Services\CommentService;

class CommentController extends Controller
{
    protected CommentService $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request, string $slug)
    {
        $data = $request->validated();
        $size = data_get($data, 'size', 10);
        $res = $this->service->getList($slug, $size);
        return $res;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateCommentRequest $request, string $slug)
    {
        $data = $request->validated();
        $user = $request->user();

        $comment = $this->service->create($data, $slug, data_get($user, 'id'));
        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateCommentRequest $request, string $id)
    {
        $data = $request->validated();

        $this->service->update($data, $id);
        return response()->json(['message' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'success']);
    }

    public function reply(StoreUpdateCommentRequest $request, string $comment_id)
    {
        $data = $request->validated();
        $user = $request->user();

        $sub_comment = $this->service->reply($data, $comment_id, data_get($user, 'id'));
        return response()->json($sub_comment, 201);
    }
}
