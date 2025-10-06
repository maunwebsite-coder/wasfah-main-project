<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedTool extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tool_id'
    ];

    /**
     * Get the tool that owns the saved item
     */
    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    /**
     * Scope for authenticated user only
     */
    public function scopeForUser($query, $userId)
    {
        // فقط للمستخدمين المسجلين
        return $query->where('user_id', $userId);
    }
}
