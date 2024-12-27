<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use OpenApi\Attributes as OA;

#[
    OA\Schema(
        properties: [
            new OA\Property(property: 'id', type: 'integer', format: 'int64', example: "1"),
            new OA\Property(property: 'name', type: 'string', example: "Python"),
            new OA\Property(property: 'slug', type: 'string', example: "python"),
            new OA\Property(property: 'created_at', type: 'datetime', example: "2023-09-10T12:34:56"),
            new OA\Property(property: 'updated_at', type: 'datetime', example: "2023-09-10T12:34:56")
        ]
    )
]
class Tag extends Model
{
    use HasFactory, Searchable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tags', 'tag_id', 'article_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'following_tags', 'tag_id', 'follower_id');
    }

    public function searchableAs()
    {
        return 'tags_index';
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name
        ];
    }
}
