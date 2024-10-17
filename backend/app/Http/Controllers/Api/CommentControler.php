<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentControler extends Controller
{
    protected $service;

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
        $start = ((int)data_get($data, 'page', 1) - 1) * $size;
        $res = $this->service->getList($slug, $start, $size);
        return $res;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug)
    {
        $data = $request->validate([
            'content' => 'required',
        ]);

        $comment = $this->service->create($data, $slug);
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
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'content' => 'required'
        ]);

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
}
