<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

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
