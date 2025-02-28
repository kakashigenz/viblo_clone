<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $service;

    public function __construct(TagService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $data = $request->validated();
        $size = data_get($data, 'size', 20);
        $start = ((int)data_get($data, 'page', 1) - 1) * $size;
        $res = $this->service->getList($start, $size);
        return $res;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);

        $tag = $this->service->create($data);
        if (!$tag) {
            return response()->json(['message' => 'Tag has existed'], 400);
        }
        return response()->json($tag, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $res = $this->service->find($slug);
        return response()->json($res);
    }

    public function getTopTag()
    {
        return [
            'status' => 'success',
            'data' => $this->service->getTopTag()
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $this->service->delete($slug);
        return response()->json(['message' => 'success']);
    }
}
