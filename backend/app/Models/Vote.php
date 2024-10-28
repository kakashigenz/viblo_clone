<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    public const TYPE_ARTICLE = 1;
    public const TYPE_COMMENT = 2;

    public const UNVOTE = 0;
    public const UPVOTE = 1;
    public const DOWNVOTE = 2;


    protected $guarded = ['id', 'user_id', 'created_at', 'updated_at'];

    public function voteable()
    {
        return $this->morphTo();
    }
}
