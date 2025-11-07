<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Services\ReferralProgramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{
    private const ALLOWED_FLOWS = [
        'login',
        'register_customer',
        'register_chef',
    ];

    public function __construct(
        protected ReferralProgramService $referrals,
    ) {
    }

    /**
     * Redirect the user to Google's authentication page.
     */
    public function redirect(Request $request)
    {
        // تخزين معرف الورشة في session إذا كان موجوداً
        $pendingWorkshopId = $request->input('pending_workshop_booking');
        if ($pendingWorkshopId) {
            session(['pending_workshop_booking' => $pendingWorkshopId]);
        }

        $flow = $request->input('flow', 'login');
        if (!in_array($flow, self::ALLOWED_FLOWS, true)) {
            $flow = 'login';
        }

        $intent = $request->input('intent', User::ROLE_CUSTOMER);
        if (!in_array($intent, [User::ROLE_CUSTOMER, User::ROLE_CHEF], true)) {
            $intent = User::ROLE_CUSTOMER;
        }

        if ($flow === 'register_customer') {
            $intent = User::ROLE_CUSTOMER;
        } elseif ($flow === 'register_chef') {
            $intent = User::ROLE_CHEF;
        }

        session([
            'auth_login_intent' => $intent,
            'auth_login_flow' => $flow,
        ]);
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback(Request $request)
    {
        $stage = 'start';
        $socialUser = null;

        try {
            $stage = 'fetch-social-user';
            $socialUser = Socialite::driver('google')->user();

            $stage = 'resolve-flow-and-intent';
            $flow = session('auth_login_flow', 'login');
            session()->forget('auth_login_flow');
            if (!in_array($flow, self::ALLOWED_FLOWS, true)) {
                $flow = 'login';
            }

            $intent = session('auth_login_intent', User::ROLE_CUSTOMER);
            session()->forget('auth_login_intent');

            if (!in_array($intent, [User::ROLE_CUSTOMER, User::ROLE_CHEF], true)) {
                $intent = User::ROLE_CUSTOMER;
            }

            if ($flow === 'register_customer') {
                $intent = User::ROLE_CUSTOMER;
            } elseif ($flow === 'register_chef') {
                $intent = User::ROLE_CHEF;
            }

            // Check if user exists by email first
            $stage = 'find-existing-user';
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            $isNewUser = false;
            
            if ($existingUser) {
                // User exists, update their social login info
                $stage = 'update-existing-user';
                $updates = [
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                ];

                if (
                    $flow === 'register_chef'
                    && !$existingUser->isAdmin()
                    && $existingUser->role !== User::ROLE_CHEF
                ) {
                    $updates['role'] = User::ROLE_CHEF;
                    $updates['chef_status'] = $existingUser->chef_status ?? User::CHEF_STATUS_NEEDS_PROFILE;
                }

                $existingUser->update($updates);
                $user = $existingUser;
            } else {
                if ($flow === 'login') {
                    $stage = 'no-user-found-login-flow';
                    return redirect('/login')
                        ->with('error', 'لم نعثر على حساب مرتبط ببريدك الإلكتروني في وصفة. يرجى اختيار خيار إنشاء حساب جديد.');
                }

                // Create new user
                $stage = 'prepare-new-user-data:' . $flow;
                $newUserData = [
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                    'password' => Hash::make(uniqid()), // Random password for social login users
                    'role' => $intent === User::ROLE_CHEF ? User::ROLE_CHEF : User::ROLE_CUSTOMER,
                ];

                if ($intent === User::ROLE_CHEF) {
                    $newUserData['chef_status'] = User::CHEF_STATUS_NEEDS_PROFILE;
                }

                $stage = 'create-new-user:' . $flow;
                $user = User::create($newUserData);
                $isNewUser = true;

                $this->assignReferralPartner($request, $user);

                // إنشاء إشعارات ترحيبية للمستخدم الجديد بدون تعطيل عملية تسجيل الدخول في حال الفشل
                try {
                    $stage = 'create-welcome-notifications';
                    $this->createWelcomeNotifications($user);
                } catch (\Throwable $notificationException) {
                    \Log::warning('Failed to create welcome notifications', [
                        'user_id' => $user->id,
                        'error' => $notificationException->getMessage(),
                    ]);
                }
            }

            // Log the user in
            $stage = 'login-user:' . $flow;
            Auth::login($user);

            // Redirect to onboarding if profile incomplete
            $stage = 'redirect-onboarding-check:' . $flow;
            if ($this->shouldRedirectToOnboarding($user)) {
                return redirect()
                    ->route('onboarding.show')
                    ->with('success', 'مرحباً بك! نحتاج لبعض التفاصيل الإضافية لاعتمادك كشيف في وصفة.');
            }

            // التحقق من وجود معرف ورشة محفوظ في session
            $stage = 'pending-workshop-check:' . $flow;
            $successMessage = $this->successMessageFor($flow, $isNewUser);
            $pendingWorkshopId = session('pending_workshop_booking');
            if ($pendingWorkshopId) {
                // مسح معرف الورشة من session
                session()->forget('pending_workshop_booking');

                $workshop = \App\Models\Workshop::find($pendingWorkshopId);
                if (!$workshop) {
                    return redirect('/')
                        ->with('info', 'تم تسجيل الدخول بنجاح، لكن لم يتم العثور على الورشة المطلوبة. يمكنك تصفح الورشات المتاحة الآن.');
                }

                $workshopMessage = $isNewUser
                    ? ($flow === 'register_chef'
                        ? 'تم إنشاء حساب شيف جديد بنجاح! يمكنك الآن متابعة الورشة المختارة.'
                        : 'تم إنشاء حساب جديد بنجاح! يمكنك الآن حجز الورشة.')
                    : ($flow === 'register_chef'
                        ? 'تم تسجيل الدخول بنجاح كشيف! يمكنك الآن متابعة الورشة المختارة.'
                        : 'تم تسجيل الدخول بنجاح! يمكنك الآن حجز الورشة.');

                return redirect()
                    ->route('workshop.show', $workshop->slug)
                    ->with('success', $workshopMessage);
            }

            // Redirect with appropriate message based on whether it's a new user or existing user
            return redirect('/')->with('success', $successMessage);

        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error('Google OAuth Error', [
                'stage' => $stage,
                'message' => $e->getMessage(),
                'email' => $socialUser ? $socialUser->getEmail() : null,
                'exception' => $e,
            ]);
            
            // Handle exceptions, e.g., redirect to login with an error message
            return redirect('/login')->with('error', 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * إنشاء إشعارات ترحيبية للمستخدم الجديد
     */
    private function createWelcomeNotifications(User $user)
    {
        // إشعار ترحيبي أساسي
        Notification::createNotification(
            $user->id,
            'general',
            'مرحباً بك في موقع وصفة!',
            "أهلاً وسهلاً بك {$user->name}! نحن سعداء لانضمامك إلى مجتمع وصفة. استكشف ورشات الطبخ المتنوعة واكتشف وصفات جديدة.",
            [
                'welcome' => true,
                'user_name' => $user->name
            ]
        );
        
        // إشعار عن الميزات المتاحة
        Notification::createNotification(
            $user->id,
            'general',
            'اكتشف ميزات موقع وصفة',
            "يمكنك الآن حجز الورشات، حفظ الوصفات المفضلة، وتتبع أدوات الطبخ. ابدأ رحلتك في عالم الطبخ معنا!",
            [
                'features' => true,
                'user_name' => $user->name
            ]
        );
        
        // إشعار عن الورشات المتاحة
        Notification::createNotification(
            $user->id,
            'general',
            'ورشات طبخ رائعة في انتظارك',
            "لدينا مجموعة متنوعة من ورشات الطبخ التي تناسب جميع المستويات. تصفح الورشات المتاحة واحجز مكانك اليوم!",
            [
                'workshops' => true,
                'user_name' => $user->name
            ]
        );
        
        // إشعار نصائح للبداية
        Notification::createNotification(
            $user->id,
            'general',
            'نصائح للبداية في موقع وصفة',
            "للاستفادة القصوى من موقع وصفة، تأكد من تحديث ملفك الشخصي واستكشاف جميع الأقسام المتاحة.",
            [
                'tips' => true,
                'user_name' => $user->name
            ]
        );
    }

    /**
     * Determine the appropriate success message based on flow context.
     */
    private function successMessageFor(string $flow, bool $isNewUser): string
    {
        if ($isNewUser) {
            return match ($flow) {
                'register_chef' => 'تم إنشاء حساب شيف جديد بنجاح! مرحباً بك في وصفة.',
                'register_customer' => 'تم إنشاء حساب مستخدم جديد بنجاح! مرحباً بك في وصفة.',
                default => 'تم إنشاء حساب جديد بنجاح! مرحباً بك في وصفة.',
            };
        }

        return match ($flow) {
            'register_chef' => 'تم تسجيل الدخول بنجاح! تم تحديث حسابك كشيف في وصفة.',
            'register_customer' => 'تم تسجيل الدخول بنجاح! حسابك في وصفة جاهز للاستخدام.',
            default => 'تم تسجيل الدخول بنجاح! مرحباً بك مرة أخرى في وصفة.',
        };
    }

    /**
     * Determine if user should complete onboarding.
     */
    private function shouldRedirectToOnboarding(User $user): bool
    {
        if ($user->isAdmin()) {
            return false;
        }

        if (is_null($user->chef_status)) {
            return false;
        }

        if (!$user->hasCompletedChefProfile()) {
            return true;
        }

        return in_array($user->chef_status, [
            User::CHEF_STATUS_NEEDS_PROFILE,
            User::CHEF_STATUS_REJECTED,
        ], true);
    }

    private function assignReferralPartner(Request $request, User $user): void
    {
        if ($user->referrer_id) {
            return;
        }

        $referrer = $this->referrals->rememberedPartner($request);

        if ($referrer) {
            $this->referrals->assignReferrerIfNeeded($user, $referrer);
        }
    }
}


