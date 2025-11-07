<?php

namespace App\Models;

use App\Events\NotificationCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    protected $dispatchesEvents = [
        'created' => NotificationCreated::class,
    ];

    protected $appends = [
        'action_url',
    ];

    protected static array $workshopSlugCache = [];

    protected static function booted(): void
    {
        static::created(function (self $notification): void {
            Cache::forget("notifications_api_{$notification->user_id}");
        });
    }

    public function getActionUrlAttribute(): ?string
    {
        $data = $this->data ?? [];

        if (!empty($data['action_url'])) {
            return $data['action_url'];
        }

        if (!empty($data['review_url'])) {
            return $data['review_url'];
        }

        if (!empty($data['profile_url'])) {
            return $data['profile_url'];
        }

        if (!empty($data['url'])) {
            return $data['url'];
        }

        if (!empty($data['booking_id'])) {
            $bookingRoute = $this->resolveRoute('bookings.show', ['booking' => $data['booking_id']]);
            if ($bookingRoute) {
                return $bookingRoute;
            }
        }

        $workshopUrl = $this->resolveWorkshopUrl($data);
        if ($workshopUrl) {
            return $workshopUrl;
        }

        return match ($this->type) {
            'workshop_booking',
            'workshop_confirmed',
            'workshop_cancelled' => $this->resolveRoute('bookings.index'),
            default => $this->resolveRoute('profile') ?? url('/'),
        };
    }

    protected function resolveRoute(string $routeName, array $parameters = []): ?string
    {
        return Route::has($routeName)
            ? route($routeName, $parameters)
            : null;
    }

    protected function resolveWorkshopUrl(array $data): ?string
    {
        $slug = $data['workshop_slug'] ?? null;

        if (!$slug && !empty($data['workshop_id'])) {
            $workshopId = (int) $data['workshop_id'];

            if (isset(static::$workshopSlugCache[$workshopId])) {
                $slug = static::$workshopSlugCache[$workshopId];
            } else {
                $slug = Workshop::query()
                    ->whereKey($workshopId)
                    ->value('slug');

                if ($slug) {
                    static::$workshopSlugCache[$workshopId] = $slug;
                }
            }
        }

        return $slug
            ? $this->resolveRoute('workshop.show', ['workshop' => $slug])
            : null;
    }

    // العلاقة مع المستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // تحديد الإشعار كمقروء
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    // تحديد الإشعار كغير مقروء
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    // إنشاء إشعار جديد
    public static function createNotification($userId, $type, $title, $message, $data = null): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }
}
