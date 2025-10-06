<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'tool_id',
        'quantity',
        'price',
        'amazon_url',
        'affiliate_url'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    /**
     * Get the user that owns the cart item
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tool that belongs to the cart item
     */
    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    /**
     * Get total price for this cart item
     */
    public function getTotalPriceAttribute()
    {
        return round($this->price * $this->quantity, 2);
    }

    /**
     * Scope for user's cart items
     */
    public function scopeForUser($query, $userId = null, $sessionId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        } elseif ($sessionId) {
            return $query->where('session_id', $sessionId);
        }
        
        return $query;
    }
}
