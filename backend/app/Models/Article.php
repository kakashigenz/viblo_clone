<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;
use OpenApi\Attributes as OA;

#[OA\Schema(
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: "Why we should lear Python"),
        new OA\Property(property: 'content', type: 'string', example: "Because Python is fun!"),
        new OA\Property(property: 'slug', type: 'string', example: "why-we-should-learn-python"),
        new OA\Property(property: 'point', type: 'number', example: 100),
        new OA\Property(
            property: 'status',
            type: 'integer',
            enum: [Article::VISIBLE, Article::DRAFT],
            example: Article::VISIBLE,
            description: "Status: " . Article::VISIBLE . "is visible, " . Article::DRAFT . " is draff"
        ),
        new OA\Property(property: 'view', type: 'integer', example: 100),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
    ]
)]
class Article extends Model
{
    use HasFactory, Searchable;

    public const SPAM = 0;
    public const DRAFT = 1;
    public const VISIBLE = 2;


    protected $guarded = ['id', 'created_at', 'updated_at', 'user_id'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'article_id', 'id');
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bookmarks()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'article_id', 'user_id');
    }

    public function searchableAs()
    {
        return 'articles_index';
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'user' => $this->user
        ];
    }

    public function shouldBeSearchable()
    {
        return $this->status == self::VISIBLE;
    }

    public function makeSearchableUsing(Collection $models)
    {
        return $models->load('user');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('public', function (Builder $builder) {
            $builder->where('status', static::VISIBLE);
        });
    }
}
