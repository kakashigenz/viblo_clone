<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function edit(User $user, Comment $comment)
    {
        return data_get($user, 'id') === data_get($comment, 'user_id');
    }
}
