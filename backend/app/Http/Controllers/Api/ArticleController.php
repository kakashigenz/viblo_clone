<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\StoreUpdateArticleRequest;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
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
    #[OA\Get(
        path: '/articles',
        summary: 'Get a list of articles',
        tags: ['Article'],
        parameters: [
            new OA\Parameter(
                in: 'query',
                name: 'page',
                schema: new OA\Schema(
                    type: 'integer',
                ),
            ),
            new OA\Parameter(
                in: 'query',
                name: 'size',
                schema: new OA\Schema(
                    type: 'integer',
                ),
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ListArticleResponse'
                )
            ),
            new OA\Response(
                response: "5XX",
                description: 'Unexpected error',
            )
        ]
    )]
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
    #[OA\Post(
        path: '/articles',
        summary: 'Store an article',
        tags: ['Article'],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/StoreUpdateArticleRequest'),
            required: true
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Create successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Article',
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Unprocessable Data",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string')
                        )
                    ]
                )
            )
        ],
        security: [['bearerAuth' => []]]
    )]
    public function store(StoreUpdateArticleRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $article = $this->service->create($data, data_get($user, 'id'));
        return response()->json($article, 201);
    }

    #[OA\Get(
        path: '/articles/{slug}',
        summary: 'Get an article',
        tags: ['Article'],
        description: '**Get an article by slug**',
        parameters: [
            new OA\Parameter(
                in: 'path',
                name: 'slug',
                schema: new OA\Schema(
                    type: 'string',
                    example: "why-we-should-learn-python"
                ),
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/ArticleResponse'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: "Not found"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: "5XX",
                description: 'Unexpected Error',
            )
        ],
    )]
    /**
     * Get the specofied resource in storage
     */
    public function show(Request $request, string $slug)
    {
        $user = $request->user();
        return $this->service->find($user, $slug);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: '/articles/{slug}',
        summary: 'Update an article',
        tags: ['Article'],
        parameters: [
            new OA\Parameter(
                in: 'path',
                name: 'slug',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    example: "why-we-should-learn-python"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/StoreUpdateArticleRequest'),
            required: true
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Update successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Article',
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Unprocessable Data",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string')
                        )
                    ]
                )
            )

        ],
        security: [['bearerAuth' => []]]
    )]
    public function update(StoreUpdateArticleRequest $request, string $slug)
    {
        $data = $request->validated();

        $article = $this->service->update($data, $slug);
        return $article;
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/articles/{slug}',
        summary: 'Delete an article',
        tags: ['Article'],
        parameters: [
            new OA\Parameter(
                in: 'path',
                name: 'slug',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    example: "why-we-should-learn-python",
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Delete successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                        )
                    ]
                )
            ),
        ],
        security: [['bearerAuth' => []]]
    )]
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
