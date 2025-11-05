<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class WorkshopBooking extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->public_code)) {
                $booking->public_code = static::generateUniquePublicCode();
            }
        });

        // عند إنشاء حجز جديد
        static::created(function ($booking) {
            if ($booking->status === 'confirmed') {
                $booking->workshop->increment('bookings_count');
            }
        });

        // عند تحديث حالة الحجز
        static::updated(function ($booking) {
            $originalStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;

            // إذا تغيرت الحالة من غير مؤكد إلى مؤكد
            if ($originalStatus !== 'confirmed' && $newStatus === 'confirmed') {
                $booking->workshop->increment('bookings_count');
            }
            // إذا تغيرت الحالة من مؤكد إلى غير مؤكد
            elseif ($originalStatus === 'confirmed' && $newStatus !== 'confirmed') {
                $booking->workshop->decrement('bookings_count');
            }
        });

        // عند حذف حجز
        static::deleted(function ($booking) {
            if ($booking->status === 'confirmed') {
                $booking->workshop->decrement('bookings_count');
            }
        });
    }

    protected $fillable = [
        'workshop_id',
        'user_id',
        'status',
        'booking_date',
        'payment_status',
        'payment_method',
        'payment_amount',
        'notes',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'public_code',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'payment_amount' => 'decimal:2',
    ];

    // العلاقات
    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Accessors
    public function getIsConfirmedAttribute()
    {
        return $this->status === 'confirmed';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    protected static function generateUniquePublicCode(int $length = 10): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $code = '';

            for ($i = 0; $i < $length; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (static::where('public_code', $code)->exists());

        return $code;
    }
}
