<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRevenueShare extends Model
{
    use HasFactory;

    public const TYPE_CHEF = 'chef';
    public const TYPE_PARTNER = 'partner';
    public const TYPE_ADMIN = 'admin';

    public const STATUS_PENDING = 'pending';
    public const STATUS_DISTRIBUTED = 'distributed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'workshop_booking_id',
        'recipient_type',
        'recipient_id',
        'percentage',
        'amount',
        'currency',
        'status',
        'distributed_at',
        'cancelled_at',
        'meta',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'distributed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'meta' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(WorkshopBooking::class, 'workshop_booking_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('recipient_type', $type);
    }

    public function scopeDistributed($query)
    {
        return $query->where('status', self::STATUS_DISTRIBUTED);
    }
}
