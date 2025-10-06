<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkshopView extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'ip_address',
        'user_agent',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * العلاقة مع الورشة
     */
    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }


    /**
     * Scope للحصول على المشاهدات الحديثة
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope للحصول على المشاهدات من IP محدد
     */
    public function scopeFromIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }
}
