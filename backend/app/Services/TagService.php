<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TagService
{
    /**
     * create a tag
     */
    public function create(array $data): ?Tag
    {
        $slug = $this->generateSlug(data_get($data, 'name'));
        if (Tag::query()->where('slug', $slug)->first()) {
            return null;
        }
        $data['slug'] = $slug;
        $tag = Tag::query()->create($data);
        return $tag;
    }

    /**
     * get list tag use pagination
     */
    public function getList(int $start, int $size): array
    {
        $data = Tag::query()->skip($start)->take($size)->get();
        $res = [
            'data' => $data,
            'total' => Tag::all()->count()
        ];
        return $res;
    }


    /**
     * get a tag by slug
     */
    public function find(string $slug): Tag
    {
        $tag = Tag::query()->where('slug', $slug)->first();
        throw_if(!$tag, new ResourceNotFoundException("Không tìm thấy thẻ"));
        return $tag;
    }

    /**
     * delete a tag by slug
     */
    public function delete(string $slug): bool
    {
        $tag = Tag::query()->where('slug', $slug)->first();
        throw_if(!$tag, new ResourceNotFoundException("Không tìm thấy thẻ"));
        return $tag->delete();
    }

    /**
     * find tag by name
     */
    public function findTagByName(string $name): Tag
    {
        return $this->find($this->generateSlug($name));
    }

    public function getTopTag(): Collection
    {
        return Tag::query()->withCount(['articles', 'followers'])->orderBy('followers_count', 'desc')->take(2)->get();
    }

    protected function generateSlug(string $text): string
    {
        //handle case tag is c++,c#
        $pre_slug = str_replace(['+', '#'], ['p', 'sharp'], $text);
        return Str::slug($pre_slug);
    }
}
