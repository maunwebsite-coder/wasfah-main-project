<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceInvoice extends Model
{
    use HasFactory;

    public const TYPE_BOOKING = 'booking';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_PAID = 'paid';
    public const STATUS_VOID = 'void';

    protected $fillable = [
        'invoice_number',
        'workshop_booking_id',
        'created_by',
        'type',
        'status',
        'currency',
        'subtotal',
        'tax_amount',
        'total',
        'line_items',
        'notes',
        'issued_at',
        'due_at',
        'paid_at',
        'voided_at',
        'meta',
    ];

    protected $casts = [
        'line_items' => 'array',
        'meta' => 'array',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'voided_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(WorkshopBooking::class, 'workshop_booking_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getIsIssuedAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_ISSUED, self::STATUS_PAID], true);
    }

    public function getIsVoidAttribute(): bool
    {
        return $this->status === self::STATUS_VOID;
    }
}

