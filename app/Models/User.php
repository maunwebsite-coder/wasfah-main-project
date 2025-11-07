<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Recipe;
use App\Models\ChefLinkPage;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
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
        'is_referral_partner',
        'referral_code',
        'referrer_id',
        'referral_commission_rate',
        'referral_commission_currency',
        'referral_partner_since_at',
        'referral_admin_notes',
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
            'is_referral_partner' => 'boolean',
            'referral_partner_since_at' => 'datetime',
            'referral_commission_rate' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (blank($user->referral_code)) {
                $user->referral_code = static::generateUniqueReferralCode();
            }
        });

        static::saving(function (User $user) {
            if ($user->is_referral_partner && blank($user->referral_code)) {
                $user->referral_code = static::generateUniqueReferralCode();
            }

            if ($user->isDirty('is_referral_partner') && $user->is_referral_partner && blank($user->referral_partner_since_at)) {
                $user->referral_partner_since_at = now();
            }
        });
    }

    public static function generateUniqueReferralCode(int $length = 10): string
    {
        $length = max(6, $length);

        do {
            $code = Str::upper(Str::random($length));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    public function ensureReferralCode(bool $persist = true): string
    {
        if (filled($this->referral_code)) {
            return $this->referral_code;
        }

        $this->referral_code = static::generateUniqueReferralCode();

        if ($persist && $this->exists) {
            $this->saveQuietly();
        }

        return $this->referral_code;
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
     * Determine if the user can share referral links.
     */
    public function isReferralPartner(): bool
    {
        return (bool) $this->is_referral_partner;
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

    public function getReferralCurrencyCodeAttribute(): string
    {
        return $this->referral_commission_currency ?: (string) config('referrals.default_currency', 'JOD');
    }

    public function getReferralCurrencySymbolAttribute(): string
    {
        $currency = $this->referral_currency_code;

        return data_get(config('referrals.currencies', []), "{$currency}.symbol", $currency);
    }

    public function getReferralCurrencyLabelAttribute(): string
    {
        $currency = $this->referral_currency_code;

        return data_get(config('referrals.currencies', []), "{$currency}.label", $currency);
    }

    /**
     * الرابط القابل للمشاركة لطلبات الدعوة.
     */
    public function getReferralLinkAttribute(): ?string
    {
        if (!$this->isReferralPartner()) {
            return null;
        }

        $code = $this->ensureReferralCode();

        return route('register', ['ref' => $code]);
    }

    /**
     * العلاقات
     */
    
    // المستخدم الذي قام بدعوة هذا الحساب
    public function referralPartner()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    // المستخدمون الذين تم تسجيلهم عبر هذا الحساب
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    // العمولات المكتسبة كشريك إحالة
    public function referralCommissions()
    {
        return $this->hasMany(ReferralCommission::class, 'referral_partner_id');
    }

    // العمولات الناتجة عن نشاط هذا المستخدم (كمُحال)
    public function generatedReferralCommissions()
    {
        return $this->hasMany(ReferralCommission::class, 'referred_user_id');
    }
    
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

    /**
     * الورش التي ينشئها الشيف.
     */
    public function workshops()
    {
        return $this->hasMany(Workshop::class);
    }

    /**
     * Retrieve the next upcoming (or most recent active) workshop for this chef.
     */
    public function nextUpcomingWorkshop(): ?Workshop
    {
        $baseQuery = $this->workshops()
            ->active()
            ->withCount('bookings');

        $upcoming = (clone $baseQuery)
            ->whereNotNull('start_date')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->first();

        if ($upcoming) {
            return $upcoming;
        }

        return (clone $baseQuery)
            ->orderBy('start_date')
            ->orderByDesc('created_at')
            ->first();
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
