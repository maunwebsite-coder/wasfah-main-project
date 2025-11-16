<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'gallery_images',
        'amazon_url',
        'affiliate_url',
        'price',
        'category',
        'rating',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'amazon_url' => 'string',
        'affiliate_url' => 'string',
        'gallery_images' => 'array'
    ];

    protected $attributes = [
        'rating' => 0,
        'is_active' => true,
        'sort_order' => 0
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (is_null($model->rating)) {
                $model->rating = 0;
            }
        });
        
        static::updating(function ($model) {
            if (is_null($model->rating)) {
                $model->rating = 0;
            }
        });
    }

    /**
     * Get the full URL for the tool's image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        if (!empty($this->gallery_images) && is_array($this->gallery_images)) {
            $firstImage = collect($this->gallery_images)
                ->first(function ($path) {
                    return !empty($path);
                });

            if ($firstImage) {
                return Str::startsWith($firstImage, ['http://', 'https://'])
                    ? $firstImage
                    : asset('storage/' . ltrim($firstImage, '/'));
            }
        }
        
        return asset('image/logo.webp');
    }

    /**
     * Get full URLs for gallery images
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        if (empty($this->gallery_images) || !is_array($this->gallery_images)) {
            return [];
        }

        return collect($this->gallery_images)
            ->filter()
            ->map(function ($path) {
                return Str::startsWith($path, ['http://', 'https://'])
                    ? $path
                    : asset('storage/' . ltrim($path, '/'));
            })
            ->values()
            ->all();
    }

    /**
     * Scope for active tools
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
}

