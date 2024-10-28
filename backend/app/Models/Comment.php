<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'user_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subComments()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'voteable');
    }
}
