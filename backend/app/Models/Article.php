<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'content', type: 'string'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'point', type: 'number'),
        new OA\Property(property: 'status', type: 'string'),
        new OA\Property(property: 'view', type: 'number'),
    ]
)]
class Article extends Model
{
    use HasFactory;

    public const SPAM = 0;
    public const DRAFT = 1;
    public const VISIBLE = 2;

    public const STATUS_VALUES = ['spam', 'draft', 'visible'];

    protected $guarded = ['id', 'created_at', 'updated_at', 'user_id'];

    public function jsonSerialize(): mixed
    {
        $data = parent::jsonSerialize();

        $data['status'] = static::STATUS_VALUES[$this->status];
        return $data;
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id');
    }
}
