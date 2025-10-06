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
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();

            // Check if user exists by email first
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            $isNewUser = false;
            
            if ($existingUser) {
                // User exists, update their social login info
                $existingUser->update([
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                    'provider_token' => $socialUser->token,
                ]);
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
                ]);
                $isNewUser = true;
                
                // إنشاء إشعارات ترحيبية للمستخدم الجديد
                $this->createWelcomeNotifications($user);
            }

            // Log the user in
            Auth::login($user);

            // التحقق من وجود معرف ورشة محفوظ في session
            $pendingWorkshopId = session('pending_workshop_booking');
            if ($pendingWorkshopId) {
                // مسح معرف الورشة من session
                session()->forget('pending_workshop_booking');
                
                // توجيه المستخدم إلى صفحة الورشة
                if ($isNewUser) {
                    $workshop = \App\Models\Workshop::find($pendingWorkshopId);
                    return redirect()->route('workshop.show', $workshop->slug)->with('success', 'تم إنشاء حساب جديد بنجاح! مرحباً بك في وصفة 🎉 يمكنك الآن حجز الورشة.');
                } else {
                    $workshop = \App\Models\Workshop::find($pendingWorkshopId);
                    return redirect()->route('workshop.show', $workshop->slug)->with('success', 'تم تسجيل الدخول بنجاح! مرحباً بك مرة أخرى في وصفة 👋 يمكنك الآن حجز الورشة.');
                }
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
}