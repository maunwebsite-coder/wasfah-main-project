<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;

class Workshop extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'content',
        'instructor',
        'instructor_avatar',
        'instructor_bio',
        'category',
        'level',
        'duration',
        'max_participants',
        'price',
        'currency',
        'image',
        'images',
        'location',
        'address',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'registration_deadline',
        'is_online',
        'meeting_link',
        'meeting_provider',
        'jitsi_room',
        'jitsi_passcode',
        'meeting_started_at',
        'meeting_started_by',
        'meeting_locked_at',
        'host_first_joined_at',
        'host_join_device_token',
        'host_join_device_fingerprint',
        'host_join_device_ip',
        'host_join_device_user_agent',
        'requirements',
        'what_you_will_learn',
        'materials_needed',
        'is_active',
        'is_featured',
        'views_count',
        'bookings_count',
        'rating',
        'reviews_count',
    ];

    protected $casts = [
        'images' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'meeting_started_at' => 'datetime',
        'meeting_locked_at' => 'datetime',
        'host_first_joined_at' => 'datetime',
        'is_online' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected $hidden = [
        'meeting_link_cipher',
    ];

    protected static bool $descriptionFullTextChecked = false;
    protected static bool $descriptionFullTextExists = false;

    protected function meetingLink(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                $cipher = $attributes['meeting_link_cipher'] ?? null;

                if (is_string($cipher) && $cipher !== '') {
                    try {
                        return Crypt::decryptString($cipher);
                    } catch (\Throwable $exception) {
                        Log::warning('Failed to decrypt workshop meeting link.', [
                            'workshop_id' => $attributes['id'] ?? null,
                            'exception_message' => $exception->getMessage(),
                        ]);
                    }
                }

                if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                return null;
            },
            set: function ($value) {
                if (blank($value)) {
                    return [
                        'meeting_link' => null,
                        'meeting_link_cipher' => null,
                    ];
                }

                $normalized = trim((string) $value);

                if ($normalized === '') {
                    return [
                        'meeting_link' => null,
                        'meeting_link_cipher' => null,
                    ];
                }

                try {
                    $encrypted = Crypt::encryptString($normalized);
                } catch (\Throwable $exception) {
                    Log::error('Failed to encrypt workshop meeting link.', [
                        'exception_message' => $exception->getMessage(),
                    ]);

                    return [
                        'meeting_link' => $normalized,
                        'meeting_link_cipher' => null,
                    ];
                }

                $hashKey = config('app.key', 'wasfah-workshop');
                $hashedIndicator = hash_hmac('sha256', $normalized, (string) $hashKey);

                return [
                    'meeting_link' => $hashedIndicator,
                    'meeting_link_cipher' => $encrypted,
                ];
            }
        );
    }

    // العلاقات
    public function bookings()
    {
        return $this->hasMany(WorkshopBooking::class);
    }

    public function reviews()
    {
        return $this->hasMany(WorkshopReview::class);
    }

    public function views()
    {
        return $this->hasMany(WorkshopView::class);
    }

    /**
     * الشيف المسؤول عن الورشة (إن وجد).
     */
    public function chef()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function meetingStarter()
    {
        return $this->belongsTo(User::class, 'meeting_started_by');
    }

    // العلاقة مع الوصفات (many-to-many)
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'workshop_recipes', 'workshop_id', 'recipe_id')
                    ->withPivot('order')
                    ->orderBy('workshop_recipes.order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        $currencyCode = $this->currency ?: 'JOD';

        return number_format($this->price, 2) . ' ' . $currencyCode;
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return $hours . ' ساعة و ' . $minutes . ' دقيقة';
        } elseif ($hours > 0) {
            return $hours . ' ساعة';
        } else {
            return $minutes . ' دقيقة';
        }
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_date > now();
    }

    public function getIsRegistrationOpenAttribute()
    {
        if (!$this->registration_deadline) {
            return $this->is_upcoming;
        }
        
        return $this->is_upcoming && now() < $this->registration_deadline;
    }

    public function getAvailableSpotsAttribute()
    {
        return $this->max_participants - $this->bookings_count;
    }

    public function getIsFullyBookedAttribute()
    {
        return $this->available_spots <= 0;
    }

    public function getIsCompletedAttribute()
    {
        return $this->end_date && $this->end_date < now();
    }

    /**
     * الحصول على عدد المشاهدات الفريدة (من IPs مختلفة)
     */
    public function getUniqueViewsCountAttribute()
    {
        return $this->views()->count();
    }

    /**
     * Validation rules للورشات
     */
    public static function validationRules($workshopId = null)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructor' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'duration' => 'required|integer|min:30',
            'max_participants' => 'required|integer|min:1|max:1000',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:JOD',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => ['required_unless:is_online,1', 'nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'is_online' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meeting_link' => [
                'nullable',
                'url',
                'max:255',
                Rule::requiredIf(function () {
                    $request = request();
                    return $request->boolean('is_online') && !$request->boolean('auto_generate_meeting');
                }),
            ],
            'meeting_provider' => ['nullable', 'string', 'max:50'],
            'jitsi_room' => ['nullable', 'string', 'max:255'],
            'jitsi_passcode' => ['nullable', 'string', 'max:20'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        // إضافة validation خاص للورشة المميزة
        if ($workshopId) {
            $rules['is_featured'] = [
                'boolean',
                function ($attribute, $value, $fail) use ($workshopId) {
                    if ($value === true) {
                        $existingFeatured = static::where('is_featured', true)
                            ->where('id', '!=', $workshopId)
                            ->exists();
                        
                        if ($existingFeatured) {
                            $fail('يمكن أن تكون ورشة واحدة فقط مميزة في نفس الوقت.');
                        }
                    }
                },
            ];
        } else {
            $rules['is_featured'] = [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value === true) {
                        $existingFeatured = static::where('is_featured', true)->exists();
                        
                        if ($existingFeatured) {
                            $fail('يمكن أن تكون ورشة واحدة فقط مميزة في نفس الوقت.');
                        }
                    }
                },
            ];
        }

        return $rules;
    }

    /**
     * جعل هذه الورشة مميزة (مع إلغاء تمييز الورشات الأخرى)
     */
    public function makeFeatured()
    {
        // إلغاء تمييز جميع الورشات الأخرى
        static::where('is_featured', true)->update(['is_featured' => false]);
        
        // جعل هذه الورشة مميزة
        $this->is_featured = true;
        $this->save();
        
        return $this;
    }

    /**
     * إلغاء تمييز هذه الورشة
     */
    public function removeFeatured()
    {
        $this->is_featured = false;
        $this->save();
        
        return $this;
    }

    /**
     * التحقق من وجود ورشة مميزة أخرى
     */
    public function hasOtherFeaturedWorkshop()
    {
        return static::where('is_featured', true)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    /**
     * Generate slug from title
     */
    public function generateSlug()
    {
        $slug = \Str::slug($this->title, '-', 'ar');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($workshop) {
            if (empty($workshop->slug)) {
                $workshop->slug = $workshop->generateSlug();
            }
        });
        
        static::updating(function ($workshop) {
            if ($workshop->isDirty('title') && empty($workshop->slug)) {
                $workshop->slug = $workshop->generateSlug();
            }
        });
    }

    /**
     * Get route key name for model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Determine if the workshops table has the description full-text index.
     */
    public static function hasDescriptionFullTextIndex(): bool
    {
        if (! static::$descriptionFullTextChecked) {
            static::$descriptionFullTextExists = static::fullTextIndexExists('workshops_description_fulltext');
            static::$descriptionFullTextChecked = true;
        }

        return static::$descriptionFullTextExists;
    }

    /**
     * Inspect information_schema to check for a full-text index.
     */
    protected static function fullTextIndexExists(string $indexName): bool
    {
        try {
            $instance = new static();
            $connection = $instance->getConnection();
            $database = $connection->getDatabaseName();
            $table = $connection->getTablePrefix().$instance->getTable();

            $count = $connection->table('information_schema.statistics')
                ->where('table_schema', $database)
                ->where('table_name', $table)
                ->where('index_name', $indexName)
                ->count();

            return $count > 0;
        } catch (\Throwable $exception) {
            Log::warning('Failed to detect workshops full-text index.', [
                'index' => $indexName,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    #[SearchUsingFullText(['description'])]
    #[SearchUsingPrefix(['slug'])]
    public function toSearchableArray(): array
    {
        return [
            'title' => (string) $this->title,
            'slug' => (string) $this->slug,
            'description' => (string) $this->description,
            'instructor' => (string) $this->instructor,
            'category' => (string) $this->category,
            'location' => (string) $this->location,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_active;
    }
}
