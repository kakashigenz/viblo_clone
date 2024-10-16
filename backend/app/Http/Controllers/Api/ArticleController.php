<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ArticleController extends Controller
{
    protected $service;

    public function __construct(ArticleService $service)
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
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'required|array|between:1,5'
        ]);

        $article = $this->service->create($data);
        return response()->json($article, 201);
    }

    #[OA\Get(
        path: '/article',
        summary: 'Get an article',
        tags: ['Article'],
        description: '**Get an article by id**',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Article'
                )
            )
        ]
    )]
    /**
     * Get the specofied resource in storage
     */
    public function show(string $slug)
    {
        return $this->service->find($slug);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'tags' => 'required|array|between:1,5'
        ]);

        $this->service->update($data, $slug);
        return response()->json(['message' => 'success']);
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
