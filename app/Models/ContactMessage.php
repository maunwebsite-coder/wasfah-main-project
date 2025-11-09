<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_NOTIFIED = 'notified';
    public const STATUS_REVIEWED = 'reviewed';

    /**
     * Status labels used inside the admin area.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'بانتظار المراجعة',
        self::STATUS_NOTIFIED => 'تم إشعار الفريق',
        self::STATUS_REVIEWED => 'تمت المراجعة',
    ];

    /**
     * Allowed statuses for validation.
     *
     * @var string[]
     */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_NOTIFIED,
        self::STATUS_REVIEWED,
    ];

    /**
     * Mapped labels for subject dropdown selections.
     *
     * @var array<string, string>
     */
    public const SUBJECT_LABELS = [
        'general' => 'استفسار عام',
        'partnership' => 'طلب شراكة أو تعاون',
        'recipe' => 'مشكلة في وصفة',
        'workshop' => 'استفسار عن ورشة عمل',
        'technical' => 'مشكلة تقنية',
        'suggestion' => 'اقتراح',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'subject',
        'subject_label',
        'message',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Resolve a human-readable label for the stored subject key.
     */
    public function getSubjectLabelAttribute(): string
    {
        return self::SUBJECT_LABELS[$this->subject] ?? $this->subject;
    }

    /**
     * Full name accessor used by mail templates.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Resolve a human readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * Badge classes for admin UI pills.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'panel__badge panel__badge--pending',
            self::STATUS_REVIEWED => 'panel__badge panel__badge--approved',
            default => 'panel__badge panel__badge--default',
        };
    }
}
