<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'required|image|max:2048|min:1'
        ]);

        $path = $this->service->create($data);

        return [
            'path' => $path
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $name)
    {
        $path = $this->service->find($name);
        return [
            'path' => $path
        ];
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
        // abort(500, 'err');
        return ['message' => 'success'];
    }
}
