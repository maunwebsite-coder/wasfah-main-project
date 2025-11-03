<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle traditional email/password login.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
        ]);

        $remember = $request->boolean('remember');

        if ($request->filled('pending_workshop_booking')) {
            session(['pending_workshop_booking' => $request->input('pending_workshop_booking')]);
        }

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة. يرجى التحقق من البريد الإلكتروني أو كلمة المرور.',
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        if ($this->shouldRedirectToOnboarding($user)) {
            return redirect()->route('onboarding.show');
        }

        if ($pendingWorkshop = session('pending_workshop_booking')) {
            session()->forget('pending_workshop_booking');

            $workshop = Workshop::find($pendingWorkshop);
            if ($workshop) {
                return redirect()
                    ->route('workshop.show', $workshop->slug)
                    ->with('success', 'تم تسجيل الدخول بنجاح! يمكنك الآن استكمال حجز الورشة.');
            }
        }

        return redirect()->intended('/')
            ->with('success', 'تم تسجيل الدخول بنجاح! مرحباً بك مرة أخرى في وصفة 👋');
    }

    /**
     * Determine whether the logged-in user must finish onboarding.
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
