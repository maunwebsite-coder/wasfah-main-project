<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_NOTIFIED = 'notified';

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
}
