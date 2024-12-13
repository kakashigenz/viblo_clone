<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreUpdateArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use OpenApi\Attributes as OA;

class ArticleController extends Controller
{
    protected ArticleService $service;

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
        $size = data_get($data, 'size', 35);
        $res = $this->service->getList($size);
        return $res;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateArticleRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $article = $this->service->create($data, data_get($user, 'id'));
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
    public function update(StoreUpdateArticleRequest $request, string $slug)
    {
        $data = $request->validated();

        $article = $this->service->update($data, $slug);
        return $article;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $this->service->delete($slug);
        return response()->json(['message' => 'success']);
    }

    public function getMyArticles(IndexRequest $request)
    {
        $res = [];
        $user = $request->user();
        if ($request->routeIs('article.draft')) {
            $res = $this->service->getMyArticles(Article::DRAFT, $user);
        } else if ($request->routeIs('article.public')) {
            $res = $this->service->getMyArticles(Article::VISIBLE, $user);
        }
        return $res;
    }
}
