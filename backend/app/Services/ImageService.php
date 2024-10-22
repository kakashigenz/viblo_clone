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
    public function createPresignedURL(string $name, string $user_id): string
    {
        ['url' => $url, 'headers' => $headers] = Storage::temporaryUploadUrl($name, now()->addMinutes(5));

        Image::query()->create([
            'user_id' => $user_id,
            'path' => '',
            'name' => $name
        ]);

        $url = str_replace(env('AWS_ENDPOINT') . '/' . env('AWS_BUCKET'), env('IMAGE_SERVER'), $url);
        return $url;
    }

    /**
     * get list images by user id
     */
    public function getList(): Collection
    {
        return Image::query()->where('user_id', Auth::user()->id)->get();
    }

    /**
     * get an image
     */
    public function find(string $name)
    {
        //
    }

    /**
     * Delete an image
     */
    public function delete(string $name): bool
    {
        $image = Image::query()->where('user_id', Auth::user()->id)->where('name', $name)->firstOrFail();
        $location =  data_get($image, 'path') . '/' . data_get($image, 'name');
        $image->delete();
        if (Storage::exists($location)) {
            Storage::delete($location);
        }
        return true;
    }
}
