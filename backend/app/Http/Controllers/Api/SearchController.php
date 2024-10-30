<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $service;

    public function __construct(SearchService $service)
    {
        $this->service = $service;
    }

    public function searchArticle(Request $request)
    {
        $query = $request->input('q');
        $res = $this->service->searchArticle($query);
        return $res;
    }
}
