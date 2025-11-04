<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{
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

        $intent = $request->input('intent', 'customer');
        session(['auth_login_intent' => $intent]);
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();
            $intent = session('auth_login_intent', User::ROLE_CUSTOMER);
            session()->forget('auth_login_intent');

            if (!in_array($intent, [User::ROLE_CUSTOMER, User::ROLE_CHEF], true)) {
                $intent = User::ROLE_CUSTOMER;
            }

            // Check if user exists by email first
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            $isNewUser = false;
            
            if ($existingUser) {
                // User exists, update their social login info
                $updates = [
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                ];

                if (
                    $intent === User::ROLE_CHEF
                    && !$existingUser->isAdmin()
                    && $existingUser->role !== User::ROLE_CHEF
                ) {
                    $updates['role'] = User::ROLE_CHEF;
                    $updates['chef_status'] = $existingUser->chef_status ?? User::CHEF_STATUS_NEEDS_PROFILE;
                }

                $existingUser->update($updates);
                $user = $existingUser;
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                    'password' => Hash::make(uniqid()), // Random password for social login users
                    'role' => $intent === User::ROLE_CHEF ? User::ROLE_CHEF : User::ROLE_CUSTOMER,
                    'chef_status' => $intent === User::ROLE_CHEF
                        ? User::CHEF_STATUS_NEEDS_PROFILE
                        : null,
                ]);
                $isNewUser = true;
                
                // إنشاء إشعارات ترحيبية للمستخدم الجديد
                $this->createWelcomeNotifications($user);
            }

            // Log the user in
            Auth::login($user);

            // Redirect to onboarding if profile incomplete
            if ($this->shouldRedirectToOnboarding($user)) {
                return redirect()
                    ->route('onboarding.show')
                    ->with('success', 'مرحباً بك! نحتاج لبعض التفاصيل الإضافية لاعتمادك كشيف في وصفة.');
            }

            // التحقق من وجود معرف ورشة محفوظ في session
            $pendingWorkshopId = session('pending_workshop_booking');
            if ($pendingWorkshopId) {
                // مسح معرف الورشة من session
                session()->forget('pending_workshop_booking');

                $workshop = \App\Models\Workshop::find($pendingWorkshopId);
                if (!$workshop) {
                    return redirect('/')
                        ->with('info', 'تم تسجيل الدخول بنجاح، لكن لم يتم العثور على الورشة المطلوبة. يمكنك تصفح الورشات المتاحة الآن.');
                }

                $successMessage = $isNewUser
                    ? 'تم إنشاء حساب جديد بنجاح! مرحباً بك في وصفة 🎉 يمكنك الآن حجز الورشة.'
                    : 'تم تسجيل الدخول بنجاح! مرحباً بك مرة أخرى في وصفة 👋 يمكنك الآن حجز الورشة.';

                return redirect()
                    ->route('workshop.show', $workshop->slug)
                    ->with('success', $successMessage);
            }

            // Redirect with appropriate message based on whether it's a new user or existing user
            if ($isNewUser) {
                return redirect('/')->with('success', 'تم إنشاء حساب جديد بنجاح! مرحباً بك في وصفة 🎉');
            } else {
                return redirect('/')->with('success', 'تم تسجيل الدخول بنجاح! مرحباً بك مرة أخرى في وصفة 👋');
            }

        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            
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
            'مرحباً بك في موقع وصفة! 🎉',
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
}


