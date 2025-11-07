<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $unreadCount;

    public function __construct(public Notification $notification)
    {
        $this->unreadCount = Notification::query()
            ->where('user_id', $notification->user_id)
            ->where('is_read', false)
            ->count();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'.$this->notification->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'user_id' => $this->notification->user_id,
                'type' => $this->notification->type,
                'title' => $this->notification->title,
                'message' => $this->notification->message,
                'data' => $this->notification->data,
                'is_read' => (bool) $this->notification->is_read,
                'read_at' => $this->notification->read_at?->toIso8601String(),
                'created_at' => $this->notification->created_at?->toIso8601String(),
                'action_url' => $this->notification->action_url,
            ],
            'unread_count' => $this->unreadCount,
            'timestamp' => now()->timestamp,
        ];
    }
}
