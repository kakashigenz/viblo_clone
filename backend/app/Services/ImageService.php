<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Create image and return path
     */
    public function createPresignedURL(string $name): array
    {
        $ext = substr($name, strrpos($name, '.'));
        $new_name = Str::uuid() . $ext;

        ['url' => $url, 'headers' => $headers] = Storage::temporaryUploadUrl($new_name, now()->addMinutes(5));
        return ['url' => $url, 'name' => $new_name];
    }

    /**
     * get list images by user id
     */
    public function getList(): Collection
    {
        return Image::query()->where('user_id', Auth::user()->id)->get();
    }

    /**
     * get an url of image
     */
    public function getUrl(string $name)
    {
        return Storage::url($name);
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
