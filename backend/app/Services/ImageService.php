<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Create image and return path
     */
    public function create(array $data): string
    {
        $path = Storage::putFile('', data_get($data, 'image'));

        if (!$path) {
            abort(500, 'Server Error');
        }

        Image::query()->create([
            'user_id' => Auth::user()->id,
            'url' => $path
        ]);

        return $path;
    }

    /**
     * get list images by user id
     */
    public function getList()
    {
        return Image::query()->where('user_id', Auth::user()->id)->get();
    }

    /**
     * get an image
     */
    public function find(string $name): string
    {
        $host = env('FRONTEND_URL') . '/images';
        $image = Image::query()->where('user_id', Auth::user()->id)->where('name', $name)->firstOrFail();
        return $host . sprintf('%s/%s', data_get($image, 'path'), data_get($image, 'name'));
    }

    /**
     * Delete an image
     */
    public function delete(string $name)
    {
        $host = env('FRONTEND_URL') . '/images';
        $image = Image::query()->where('user_id', Auth::user()->id)->where('name', $name)->firstOrFail();
        $path = data_get($image, 'path') ? data_get($image, 'path') . '/' : '';
        $name = data_get($image, 'name');
        if (Storage::exists($path . $name)) {
            Storage::delete($path . $name);
        }
        return $image->delete();
    }
}
