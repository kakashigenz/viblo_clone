<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function index(Request $request)
    {
        $data = $request->validate([
            'page' => 'integer|gt:0',
            'size' => 'integer|gt:0'
        ]);
        $size = data_get($data, 'size', 20);
        $start = ((int)data_get($data, 'page', 1) - 1) * $size;
        $res = $this->service->getList($start, $size);
        return response()->json($res);
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
        if ($tag) {
            return response()->json(['message' => 'success'], 201);
        }
        return response()->json(['message' => 'Tag has existed'], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $res = $this->service->find($slug);
        return response()->json($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        if ($this->service->update($data, $slug)) {
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'Tag has existed'], 400);
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
