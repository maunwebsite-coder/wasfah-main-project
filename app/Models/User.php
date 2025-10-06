<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ✅ تم إضافة هذا السطر

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens; // ✅ تم إضافة HasApiTokens هنا

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'provider',
        'provider_id',
        'provider_token',
        'avatar',
        'is_admin',
        'last_login_at',
        'last_login_ip',
        'remember_me',
        'remember_me_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'remember_me' => 'boolean',
            'remember_me_expires_at' => 'datetime',
        ];
    }

    /**
     * العلاقات
     */
    
    // علاقة مع حجوزات الورشات
    public function workshopBookings()
    {
        return $this->hasMany(WorkshopBooking::class);
    }

    // علاقة مع تقييمات الورشات
    public function workshopReviews()
    {
        return $this->hasMany(WorkshopReview::class);
    }

    // علاقة مع التفاعلات مع الوصفات
    public function interactions()
    {
        return $this->hasMany(UserInteraction::class);
    }

    // الورشات المحجوزة والمؤكدة
    public function confirmedWorkshops()
    {
        return $this->belongsToMany(Workshop::class, 'workshop_bookings')
                    ->wherePivot('status', 'confirmed');
    }

    // الإشعارات
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // الإشعارات غير المقروءة
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    // عدد الإشعارات غير المقروءة
    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }
}
