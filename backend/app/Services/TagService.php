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
        //handle case tag is c++
        $pre_slug = str_replace('+', 'p', data_get($data, 'name'));

        $slug = Str::slug($pre_slug);
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
     * update a tag
     */
    public function update(array $data, string $slug): bool
    {
        $pre_slug = str_replace('+', 'p', data_get($data, 'name'));

        $tag = Tag::query()->where('slug', $slug)->firstOrFail();

        $new_slug = Str::slug($pre_slug);
        if ($new_slug !== $slug) {
            if (Tag::query()->where('slug', $new_slug)->first()) {
                return false;
            }
        }

        $data['slug'] = $new_slug;
        $tag->update($data);
        return true;
    }

    /**
     * delete a tag by slug
     */
    public function delete(string $slug): bool
    {
        return Tag::query()->where('slug', $slug)->firstOrFail()->delete();
    }
}
