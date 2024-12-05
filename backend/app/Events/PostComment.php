<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostComment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $comment;
    protected $slug_article;
    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment, string $slug_article)
    {
        $this->comment = $comment;
        $this->slug_article = $slug_article;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("comment.{$this->slug_article}"),
        ];
    }
}
