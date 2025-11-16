<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactMessage extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_NOTIFIED = 'notified';

    /**
     * Supported subject keys that users may choose from.
     *
     * @var array<int, string>
     */
    public const SUBJECT_KEYS = [
        'general',
        'partnership',
        'recipe',
        'workshop',
        'technical',
        'suggestion',
        'other',
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
     * Resolve a human-readable, localized label for the stored subject key.
     */
    public function getSubjectLabelAttribute(): string
    {
        return self::subjectLabel($this->subject);
    }

    /**
     * Full name accessor used by mail templates.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Return the list of allowed subject keys.
     *
     * @return array<int, string>
     */
    public static function subjectKeys(): array
    {
        return self::SUBJECT_KEYS;
    }

    /**
     * Build a keyed list of localized labels for the subject dropdown.
     *
     * @return array<string, string>
     */
    public static function subjectLabelOptions(): array
    {
        $options = [];

        foreach (self::subjectKeys() as $key) {
            $options[$key] = self::subjectLabel($key);
        }

        return $options;
    }

    /**
     * Fetch a translated label for a given subject key with a sensible fallback.
     */
    public static function subjectLabel(string $key): string
    {
        $translationKey = "contact.form.subjects.{$key}";
        $translated = __($translationKey);

        if ($translated === $translationKey) {
            return Str::headline($key);
        }

        return $translated;
    }
}
