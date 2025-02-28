<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Create presigned url
     */
    public function createPresignedURL(string $name): array
    {
        $ext = substr($name, strrpos($name, '.'));
        $new_name = Str::uuid() . $ext;

        ['url' => $url, 'headers' => $headers] = Storage::temporaryUploadUrl($new_name, now()->addMinutes(5));
        return ['url' => $url, 'name' => $new_name];
    }

    /**
     * Create an image
     */
    public function create(array $data, string $user_id): void
    {
        $attribute = [
            'path' => data_get($data, 'name')
        ];
        $image = new Image($attribute);
        $image->user_id = $user_id;
        $image->save();
    }

    /**
     * get list images by user id
     */
    public function getList(): array
    {
        $images =  Image::query()->where('user_id', Auth::user()->id)->paginate(18);
        return [
            'data' => $images->items(),
            'page' => $images->currentPage(),
            'size' => $images->perPage(),
            'total' => $images->total(),
        ];
    }

    /**
     * get an url of image
     */
    public function getUrl(string $name): string
    {
        return Storage::url($name);
    }

    /**
     * Delete an image
     */
    public function delete(string $name): bool
    {
        $location = "{$name}";
        $image = Image::query()->where('user_id', Auth::user()->id)->where('path', $location)->first();
        throw_if(!$image, new ResourceNotFoundException('Không tìm thấy ảnh'));
        $image->delete();
        if (Storage::exists($location)) {
            Storage::delete($location);
        }
        return true;
    }
}
