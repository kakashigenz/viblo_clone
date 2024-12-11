<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentPosted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $from_user, $content;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $from_user, string $content)
    {
        $this->from_user = $from_user;
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'avatar' => $this->from_user->avatar,
            'name' => $this->from_user->name,
            'notification' => $this->content,
            'created_at' => now()
        ];
    }
}
