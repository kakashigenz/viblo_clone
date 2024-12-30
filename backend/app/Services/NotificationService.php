<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\URL;

class NotificationService
{
    public function getList(User $user, int $size = 5): array
    {
        $notifications = $user->notifications()->paginate($size);
        $data = array_map(function ($item) {
            $notification = data_get($item, 'data');
            $notification['id'] = data_get($item, 'id');
            $notification['read_at'] = data_get($item, 'read_at');
            $notification['created_at'] = data_get($item, 'created_at');

            return $notification;
        }, $notifications->items());
        return [
            'has_next' => $notifications->hasMorePages(),
            'data' => $data,
            'unread_count' => $user->unreadNotifications()->count()
        ];
    }

    public function markAllRead(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    public function markAsRead(User $user, string $id): void
    {
        $user->unreadNotifications()->findOrFail($id)->update(['read_at' => now()]);
    }
}
