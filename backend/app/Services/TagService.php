<?php

namespace App\Services;

use App\Models\Tag;
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
        return Tag::query()->where('slug', $slug)->firstOrFail();
    }

    /**
     * delete a tag by slug
     */
    public function delete(string $slug): bool
    {
        return Tag::query()->where('slug', $slug)->firstOrFail()->delete();
    }

    /**
     * find tag by name
     */
    public function findTagByName(string $name)
    {
        return $this->find($this->generateSlug($name));
    }

    public function getTopTag()
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
