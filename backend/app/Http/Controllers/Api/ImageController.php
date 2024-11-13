<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePresignedURLRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    protected $service;

    public function __construct(ImageService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->service->getList();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createPresignedURL(CreatePresignedURLRequest $request)
    {
        $data = $request->validated();

        $res = $this->service->createPresignedURL(data_get($data, 'name'));

        return $res;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $name)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $name)
    {
        $this->service->delete($name);
        return ['message' => 'success'];
    }
}
