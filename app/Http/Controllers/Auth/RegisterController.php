<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationVerificationCodeMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    private const MAX_VERIFICATION_ATTEMPTS = 5;

    /**
     * Handle registration with email/password.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['nullable', Rule::in([User::ROLE_CUSTOMER, User::ROLE_CHEF])],
        ], [
            'name.required' => 'يرجى إدخال الاسم الكامل.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'هذا البريد الإلكتروني مسجل بالفعل.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'role.in' => 'نوع الحساب غير صحيح.',
        ]);

        $pendingWorkshopId = $request->input('pending_workshop_booking');
        if ($pendingWorkshopId) {
            session(['pending_workshop_booking' => $pendingWorkshopId]);
        }

        $role = $data['role'] ?? User::ROLE_CUSTOMER;

        EmailVerificationCode::where('email', $data['email'])->delete();

        $verificationCode = (string) random_int(100000, 999999);

        $verification = EmailVerificationCode::create([
            'token' => (string) Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'role' => $role,
            'verification_code' => Hash::make($verificationCode),
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($verification->email)->send(
            new RegistrationVerificationCodeMail($verification->name, $verificationCode)
        );

        session([
            'register_verification_token' => $verification->token,
            'register_pending_email' => $verification->email,
        ]);

        return redirect()
            ->route('register.verify.show')
            ->with('status', 'تم إرسال رمز التحقق إلى بريدك الإلكتروني. يرجى إدخال الرمز خلال 15 دقيقة لإكمال التسجيل.');
    }

    /**
     * Display the form to enter verification code.
     */
    public function showVerificationForm(Request $request)
    {
        $token = session('register_verification_token') ?? $request->query('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'يرجى إدخال بيانات التسجيل أولاً.');
        }

        $verification = EmailVerificationCode::where('token', $token)->first();

        if (!$verification) {
            session()->forget(['register_verification_token', 'register_pending_email']);

            return redirect()->route('login')->with('error', 'انتهت صلاحية طلب التسجيل. يرجى المحاولة مرة أخرى.');
        }

        session(['register_verification_token' => $verification->token]);

        return view('auth.register-verify', [
            'email' => $verification->email,
            'expiresAt' => $verification->expires_at,
        ]);
    }

    /**
     * Verify the code and create the user account.
     */
    public function verifyCode(Request $request)
    {
        $token = session('register_verification_token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'يرجى إدخال بيانات التسجيل أولاً.');
        }

        $verification = EmailVerificationCode::where('token', $token)->first();

        if (!$verification) {
            session()->forget(['register_verification_token', 'register_pending_email']);

            return redirect()->route('login')->with('error', 'انتهت صلاحية طلب التسجيل. يرجى المحاولة مرة أخرى.');
        }

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'يرجى إدخال رمز التحقق.',
            'code.digits' => 'رمز التحقق يجب أن يتكون من ستة أرقام.',
        ]);

        if ($verification->isExpired()) {
            $verification->delete();
            session()->forget(['register_verification_token', 'register_pending_email']);

            return redirect()->route('register')->with('error', 'انتهت صلاحية رمز التحقق. يرجى التسجيل مرة أخرى.');
        }

        if ($verification->attempts >= self::MAX_VERIFICATION_ATTEMPTS) {
            $verification->delete();
            session()->forget(['register_verification_token', 'register_pending_email']);

            return redirect()->route('register')->with('error', 'تم تجاوز الحد المسموح لمحاولات التحقق. يرجى البدء من جديد.');
        }

        if (!Hash::check($data['code'], $verification->verification_code)) {
            $verification->increment('attempts');

            $remaining = max(self::MAX_VERIFICATION_ATTEMPTS - $verification->attempts, 0);

            return back()
                ->withErrors([
                    'code' => $remaining > 0
                        ? "رمز التحقق غير صحيح. تبقى لديك {$remaining} محاولة."
                        : 'رمز التحقق غير صحيح. يرجى طلب رمز جديد.',
                ])
                ->withInput();
        }

        $existingUser = User::where('email', $verification->email)->first();
        if ($existingUser) {
            $verification->delete();
            session()->forget(['register_verification_token', 'register_pending_email']);

            return $this->completeRegistration($request, $existingUser)
                ->with('info', 'الحساب موجود بالفعل، تم تسجيل الدخول بنجاح.');
        }

        $user = User::create([
            'name' => $verification->name,
            'email' => $verification->email,
            'password' => $verification->password_hash,
            'provider' => null,
            'provider_id' => null,
            'provider_token' => null,
            'role' => $verification->role,
            'chef_status' => $verification->role === User::ROLE_CHEF
                ? User::CHEF_STATUS_NEEDS_PROFILE
                : null,
        ]);

        $verification->delete();
        session()->forget(['register_verification_token', 'register_pending_email']);

        return $this->completeRegistration($request, $user)
            ->with('success', 'تم التحقق من البريد الإلكتروني وإنشاء الحساب بنجاح!');
    }

    /**
     * Resend verification code.
     */
    public function resendCode()
    {
        $token = session('register_verification_token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'يرجى إدخال بيانات التسجيل أولاً.');
        }

        $verification = EmailVerificationCode::where('token', $token)->first();

        if (!$verification) {
            session()->forget(['register_verification_token', 'register_pending_email']);

            return redirect()->route('register')->with('error', 'انتهت صلاحية طلب التسجيل. يرجى المحاولة مرة أخرى.');
        }

        if ($verification->updated_at->diffInSeconds(now()) < 60) {
            return back()->with('error', 'يرجى الانتظار دقيقة قبل طلب رمز جديد.');
        }

        $verificationCode = (string) random_int(100000, 999999);

        $verification->update([
            'verification_code' => Hash::make($verificationCode),
            'expires_at' => now()->addMinutes(15),
            'attempts' => 0,
        ]);

        Mail::to($verification->email)->send(
            new RegistrationVerificationCodeMail($verification->name, $verificationCode)
        );

        return back()->with('status', 'تم إرسال رمز جديد إلى بريدك الإلكتروني.');
    }

    /**
     * Determine whether the user must complete onboarding flow.
     */
    private function shouldRedirectToOnboarding(User $user): bool
    {
        if ($user->isAdmin()) {
            return false;
        }

        if ($user->role === User::ROLE_CHEF) {
            if (!$user->hasCompletedChefProfile()) {
                return true;
            }

            return in_array($user->chef_status, [
                User::CHEF_STATUS_NEEDS_PROFILE,
                User::CHEF_STATUS_REJECTED,
            ], true);
        }

        return false;
    }

    /**
     * Finalize registration flow after successful verification.
     */
    private function completeRegistration(Request $request, User $user): RedirectResponse
    {
        Auth::login($user);
        $request->session()->regenerate();

        if ($this->shouldRedirectToOnboarding($user)) {
            return redirect()->route('onboarding.show')
                ->with('success', 'تم إنشاء الحساب بنجاح! يرجى استكمال البيانات لإتمام اعتمادك كـ شيف.');
        }

        if ($pendingWorkshopId = session('pending_workshop_booking')) {
            session()->forget('pending_workshop_booking');

            $workshop = Workshop::find($pendingWorkshopId);
            if ($workshop) {
                return redirect()
                    ->route('workshop.show', $workshop->slug)
                    ->with('success', 'تم إنشاء الحساب بنجاح! يمكنك الآن إكمال حجز الورشة.');
            }
        }

        return redirect()->intended('/')
            ->with('success', 'تم إنشاء الحساب بنجاح! مرحباً بك في وصفة 🎉');
    }
}
