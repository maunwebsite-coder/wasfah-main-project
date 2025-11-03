<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Recipe;
use App\Models\ChefLinkPage;
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
        'country_code',
        'phone_country_code',
        'provider',
        'provider_id',
        'provider_token',
        'avatar',
        'is_admin',
        'role',
        'chef_status',
        'chef_approved_at',
        'instagram_url',
        'instagram_followers',
        'youtube_url',
        'youtube_followers',
        'chef_specialty_area',
        'chef_specialty_description',
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
            'instagram_followers' => 'integer',
            'youtube_followers' => 'integer',
            'chef_approved_at' => 'datetime',
        ];
    }

    /**
     * Constant roles.
     */
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_CHEF = 'chef';
    public const ROLE_ADMIN = 'admin';

    /**
     * Chef status constants.
     */
    public const CHEF_STATUS_NEEDS_PROFILE = 'needs_profile';
    public const CHEF_STATUS_PENDING = 'pending';
    public const CHEF_STATUS_APPROVED = 'approved';
    public const CHEF_STATUS_REJECTED = 'rejected';

    /**
     * Determine if the user is a chef.
     */
    public function isChef(): bool
    {
        return $this->role === self::ROLE_CHEF && $this->chef_status === self::CHEF_STATUS_APPROVED;
    }

    /**
     * Determine if user needs to complete onboarding.
     */
    public function needsChefProfile(): bool
    {
        return $this->role === self::ROLE_CHEF
            && $this->chef_status !== self::CHEF_STATUS_APPROVED;
    }

    /**
     * Determine whether all chef profile requirements are completed.
     */
    public function hasCompletedChefProfile(): bool
    {
        $hasPhone = filled($this->phone) && filled($this->phone_country_code) && filled($this->country_code);
        $hasSocialPresence = filled($this->instagram_url) || filled($this->youtube_url);

        return $hasPhone && $hasSocialPresence;
    }

    /**
     * Determine if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin || $this->role === self::ROLE_ADMIN;
    }

    /**
     * علاقة مع الوصفات الخاصة بالشيف.
     */
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'user_id', 'id');
    }

    /**
     * صفحة روابط Wasfah الخاصة بالشيف.
     */
    public function linkPage()
    {
        return $this->hasOne(ChefLinkPage::class);
    }

    /**
     * التأكد من إنشاء صفحة الروابط وتعبئتها بقيم افتراضية عند الحاجة.
     */
    public function ensureLinkPage(): ChefLinkPage
    {
        $existing = $this->linkPage()->with('items')->first();

        if ($existing) {
            return $existing;
        }

        $page = $this->linkPage()->create([
            'headline' => 'روابط Wasfah الخاصة بـ ' . ($this->name ?? 'الشيف'),
            'subheadline' => 'كل الروابط الهامة في مكان واحد.',
            'bio' => null,
            'cta_label' => 'تصفح وصفاتي',
            'cta_url' => route('chefs.show', ['chef' => $this->id]),
            'accent_color' => '#f97316',
        ]);

        $defaultLinks = collect([
            [
                'title' => 'صفحتي على Wasfah',
                'subtitle' => 'اكتشف كل وصفاتي المنشورة',
                'url' => route('chefs.show', ['chef' => $this->id]),
                'icon' => 'fas fa-utensils',
            ],
            $this->instagram_url ? [
                'title' => 'حسابي على إنستغرام',
                'subtitle' => null,
                'url' => $this->instagram_url,
                'icon' => 'fab fa-instagram',
            ] : null,
            $this->youtube_url ? [
                'title' => 'قناتي على يوتيوب',
                'subtitle' => null,
                'url' => $this->youtube_url,
                'icon' => 'fab fa-youtube',
            ] : null,
        ])->filter();

        foreach ($defaultLinks as $index => $link) {
            $page->items()->create(array_merge($link, [
                'position' => $index + 1,
            ]));
        }

        return $page->fresh('items');
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
