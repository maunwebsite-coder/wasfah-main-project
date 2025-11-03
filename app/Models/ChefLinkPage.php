<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChefLinkPage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'slug',
        'headline',
        'subheadline',
        'bio',
        'cta_label',
        'cta_url',
        'accent_color',
        'hero_image_path',
        'is_published',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * The attributes that should be appended.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(static function (ChefLinkPage $page): void {
            $page->slug = $page->slug ?? static::generateUniqueSlugFor($page->user);
        });

        static::saving(static function (ChefLinkPage $page): void {
            if (!$page->slug) {
                $page->slug = static::generateUniqueSlugFor($page->user);
            }
        });
    }

    /**
     * Generate a unique slug for the given user.
     */
    protected static function generateUniqueSlugFor(?User $user): string
    {
        $base = $user
            ? Str::slug($user->name ?? 'chef', '-', 'ar')
            : Str::uuid()->toString();

        if ($base === '') {
            $base = 'chef';
        }

        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . ++$suffix;
        }

        return $slug;
    }

    /**
     * Relation with the owning user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with link items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ChefLinkItem::class)->orderBy('position');
    }

    /**
     * Scope to only include published pages.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Use slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Derived avatar url for public pages.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->hero_image_path) {
            return \Storage::disk('public')->url($this->hero_image_path);
        }

        $avatar = $this->user?->avatar;

        if (!$avatar) {
            return null;
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        return \Storage::disk('public')->url($avatar);
    }
}
