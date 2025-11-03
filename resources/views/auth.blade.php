@extends('layouts.auth')

@php
    $authMode = old('auth_mode', request('mode', 'login'));
    if (!in_array($authMode, ['login', 'register'], true)) {
        $authMode = 'login';
    }
@endphp

@section('title', 'تسجيل الدخول أو إنشاء حساب - وصفة')

@push('styles')
<style>
    .auth-shell {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.75rem 1rem;
        background: #fff7ed;
    }
    .auth-card {
        width: min(100%, 760px);
        background: #ffffff;
        border-radius: 1.75rem;
        padding: clamp(2.25rem, 3vw, 3rem);
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(15, 23, 42, 0.05);
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }
    .auth-header {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .auth-header h1 {
        font-size: clamp(1.9rem, 2.4vw + 1rem, 2.6rem);
        color: #1f2937;
        line-height: 1.25;
    }
    .auth-header p {
        color: #6b7280;
        font-size: 1rem;
        max-width: 34rem;
        margin: 0 auto;
        line-height: 1.6;
    }
    .auth-badge {
        align-self: center;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1.15rem;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.12);
        color: #c2410c;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .auth-tabs {
        align-self: center;
        display: inline-flex;
        gap: 0.35rem;
        padding: 0.3rem;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.08);
        border: 1px solid rgba(249, 115, 22, 0.15);
    }
    .auth-tab {
        border: none;
        background: transparent;
        color: #ea580c;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 0.5rem 1.2rem;
        border-radius: 999px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .auth-tab.active {
        background: #ffffff;
        color: #c2410c;
        box-shadow: 0 10px 24px rgba(249, 115, 22, 0.16);
    }
    .auth-panel {
        display: none;
        flex-direction: column;
        gap: 1.5rem;
    }
    .auth-panel.active {
        display: flex;
    }
    .auth-form-card {
        display: grid;
        gap: 1rem;
        padding: 1.6rem;
        border-radius: 1.35rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: #fdfbf7;
    }
    .auth-input {
        width: 100%;
        border-radius: 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: #ffffff;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        color: #1f2937;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .auth-input:focus {
        outline: none;
        border-color: rgba(249, 115, 22, 0.55);
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.18);
    }
    .auth-checkbox {
        width: 1.05rem;
        height: 1.05rem;
        border-radius: 0.35rem;
        border: 1px solid rgba(148, 163, 184, 0.6);
        accent-color: #f97316;
    }
    .auth-primary-btn {
        width: 100%;
        border: none;
        border-radius: 0.95rem;
        padding: 0.9rem 1rem;
        font-size: 1rem;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(135deg, #f97316, #fb923c);
        box-shadow: 0 18px 32px rgba(249, 115, 22, 0.24);
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .auth-primary-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 40px rgba(249, 115, 22, 0.28);
    }
    .auth-primary-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
    }
    .auth-form-note {
        font-size: 0.85rem;
        color: #94a3b8;
        text-align: center;
        line-height: 1.6;
    }
    .divider {
        position: relative;
        text-align: center;
        margin: 0.75rem 0;
        color: #9ca3af;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .divider::before,
    .divider::after {
        content: "";
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: linear-gradient(90deg, rgba(156, 163, 175, 0), rgba(156, 163, 175, 0.35));
    }
    .divider::before {
        left: 0;
    }
    .divider::after {
        right: 0;
    }
    .google-btn {
        border: 1px solid rgba(148, 163, 184, 0.28);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        border-radius: 0.95rem;
        padding: 0.85rem 1rem;
        font-size: 0.96rem;
        font-weight: 600;
        background: #ffffff;
        color: #1f2937;
        transition: all 0.2s ease;
        width: 100%;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        cursor: pointer;
    }
    .google-btn:hover {
        transform: translateY(-2px);
        border-color: rgba(249, 115, 22, 0.4);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.12);
        color: #c2410c;
    }
    .google-btn.google-chef {
        background: linear-gradient(135deg, #f97316, #fb923c);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 20px 36px rgba(249, 115, 22, 0.3);
    }
    .google-btn.google-chef:hover {
        transform: translateY(-2px);
        box-shadow: 0 22px 40px rgba(249, 115, 22, 0.34);
    }
    @media (max-width: 640px) {
        .auth-card {
            border-radius: 1.5rem;
            padding: 2rem 1.5rem;
            gap: 1.5rem;
        }
        .auth-tabs {
            width: 100%;
            justify-content: center;
        }
        .auth-tab {
            flex: 1;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-badge">
                <i class="fas fa-hat-chef"></i>
                وصفة لكل عشاق المذاق
            </span>
            <h1>ابدأ رحلتك في مشاركة وصفاتك بسهولة</h1>
            <p>تسجيل أخف، واجهة منظمة، ووصول سريع إلى مجتمع وصفة.</p>
            <p class="text-sm text-[#9a3412] leading-relaxed">
                خطوتان فقط عبر Google أو البريد الإلكتروني، ويمكنك إكمال بيانات الشيف لاحقاً.
            </p>
        </div>
            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('info'))
                <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-600">
                    {{ session('info') }}
                </div>
            @endif
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-600">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    <ul class="list-disc space-y-1 pe-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="auth-tabs">
                <button type="button" class="auth-tab {{ $authMode === 'login' ? 'active' : '' }}" data-auth-tab="login">
                    تسجيل الدخول
                </button>
                <button type="button" class="auth-tab {{ $authMode === 'register' ? 'active' : '' }}" data-auth-tab="register">
                    إنشاء حساب جديد
                </button>
            </div>

            <div data-auth-panel="login" class="auth-panel {{ $authMode === 'login' ? 'active' : '' }}">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">سجّل الدخول إلى وصفة</h2>
                    <p class="text-gray-600 leading-relaxed">
                        اختر الطريقة الأنسب لك للدخول، بريد إلكتروني بكلمة مرور أو تسجيل سريع عبر Google.
                    </p>
                </div>

                <form method="POST" action="{{ route('login.password') }}" class="auth-form-card">
                    @csrf
                    <input type="hidden" name="auth_mode" value="login">
                    <input type="hidden" name="pending_workshop_booking" value="{{ old('pending_workshop_booking', request('pending_workshop_booking')) }}">
                    <div>
                        <label for="login-email" class="mb-1 block text-sm font-semibold text-gray-700">البريد الإلكتروني</label>
                        <input
                            id="login-email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                            class="auth-input"
                            placeholder="example@email.com">
                    </div>
                    <div>
                        <label for="login-password" class="mb-1 block text-sm font-semibold text-gray-700">كلمة المرور</label>
                        <input
                            id="login-password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            required
                            class="auth-input"
                            placeholder="••••••••">
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                            class="auth-checkbox">
                        تذكرني في هذا الجهاز
                    </label>
                    <button type="submit" class="auth-primary-btn">
                        تسجيل الدخول باستخدام البريد الإلكتروني
                    </button>
                    <p class="auth-form-note">
                        في حال احتجت لإعادة تعيين كلمة المرور تواصل مع فريق وصفة لمساعدتك.
                    </p>
                </form>

                <div class="divider">أو أكمل عبر Google</div>

                <div class="grid gap-3">
                    <button type="button" class="google-btn" data-google-intent="customer">
                        <img src="https://img.icons8.com/color/36/google-logo.png" alt="Google" class="inline-block">
                        المتابعة كمستخدم
                    </button>
                    <button type="button" class="google-btn google-chef" data-google-intent="chef">
                        <img src="https://img.icons8.com/color/36/google-logo.png" alt="Google" class="inline-block">
                        التقديم كشيف محترف
                    </button>
                </div>

                <p class="auth-form-note">
                    للانضمام كشيف يكفي إضافة بياناتك الاحترافية بعد تسجيل الدخول.
                </p>

                <p class="auth-form-note">
                    بالمتابعة تؤكد موافقتك على شروط الاستخدام وسياسة الخصوصية في وصفة.
                </p>
            </div>

            <div data-auth-panel="register" class="auth-panel {{ $authMode === 'register' ? 'active' : '' }}">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">إنشاء حساب جديد في وصفة</h2>
                    <p class="text-gray-600 leading-relaxed">
                        استخدم بريدك أو Google لإنشاء حسابك ثم عد لإكمال بيانات الشيف وقتما تشاء.
                    </p>
                </div>

                <form method="POST" action="{{ route('register.password') }}" class="auth-form-card">
                    @csrf
                    <input type="hidden" name="auth_mode" value="register">
                    <input type="hidden" name="pending_workshop_booking" value="{{ old('pending_workshop_booking', request('pending_workshop_booking')) }}">
                    <div>
                        <label for="register-name" class="mb-1 block text-sm font-semibold text-gray-700">الاسم الكامل</label>
                        <input
                            id="register-name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                            class="auth-input"
                            placeholder="الاسم كما سيظهر للآخرين">
                    </div>
                    <div>
                        <label for="register-email" class="mb-1 block text-sm font-semibold text-gray-700">البريد الإلكتروني</label>
                        <input
                            id="register-email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                            class="auth-input"
                            placeholder="example@email.com">
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="register-password" class="mb-1 block text-sm font-semibold text-gray-700">كلمة المرور</label>
                            <input
                                id="register-password"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                required
                                class="auth-input"
                                placeholder="••••••••">
                        </div>
                        <div>
                            <label for="register-password-confirmation" class="mb-1 block text-sm font-semibold text-gray-700">تأكيد كلمة المرور</label>
                            <input
                                id="register-password-confirmation"
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                required
                                class="auth-input"
                                placeholder="••••••••">
                        </div>
                    </div>
                    <div>
                        <label for="register-role" class="mb-1 block text-sm font-semibold text-gray-700">نوع الحساب</label>
                        <select
                            id="register-role"
                            name="role"
                            class="auth-input">
                            <option value="customer" {{ old('role', 'customer') === 'customer' ? 'selected' : '' }}>مستخدم عادي</option>
                            <option value="chef" {{ old('role') === 'chef' ? 'selected' : '' }}>شيف محترف</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                            حسابات الإدارة ينشئها فريق وصفة. اختيارك لحساب الشيف يعني أنك مستعد لإكمال بيانات الاعتماد لاحقاً.
                        </p>
                    </div>
                    <button type="submit" class="auth-primary-btn">
                        إنشاء حساب بالبريد الإلكتروني
                    </button>
                </form>

                <div class="divider">أو استخدم Google</div>

                <div class="grid gap-3">
                    <button type="button" class="google-btn" data-google-intent="customer">
                        <img src="https://img.icons8.com/color/36/google-logo.png" alt="Google" class="inline-block">
                        إنشاء حساب كمستخدم عبر Google
                    </button>
                    <button type="button" class="google-btn google-chef" data-google-intent="chef">
                        <img src="https://img.icons8.com/color/36/google-logo.png" alt="Google" class="inline-block">
                        التسجيل كشيف محترف عبر Google
                    </button>
                </div>

                <p class="auth-form-note">
                    بتسجيلك توافق على شروط خدمة وصفة وسياسة الخصوصية.
                </p>
            </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const pendingWorkshop = params.get('pending_workshop_booking');
    const tabs = document.querySelectorAll('[data-auth-tab]');
    const panels = document.querySelectorAll('[data-auth-panel]');
    const modeInputs = document.querySelectorAll('input[name="auth_mode"]');
    const pendingInputs = document.querySelectorAll('input[name="pending_workshop_booking"]');
    const initialMode = '{{ $authMode }}';

    const activateMode = (mode) => {
        const normalized = ['login', 'register'].includes(mode) ? mode : 'login';
        tabs.forEach((tab) => {
            tab.classList.toggle('active', tab.dataset.authTab === normalized);
        });
        panels.forEach((panel) => {
            panel.classList.toggle('active', panel.dataset.authPanel === normalized);
        });
        modeInputs.forEach((input) => {
            input.value = normalized;
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            activateMode(tab.dataset.authTab || 'login');
        });
    });

    activateMode(initialMode);

    const redirectToGoogleAuth = (intent) => {
        const url = new URL('{{ route('google.redirect') }}', window.location.origin);
        if (pendingWorkshop) {
            url.searchParams.set('pending_workshop_booking', pendingWorkshop);
        }
        if (intent && intent !== 'customer') {
            url.searchParams.set('intent', intent);
        }
        window.location.href = url.toString();
    };

    document.querySelectorAll('[data-google-intent]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = button.closest('[data-auth-panel]');
            if (panel) {
                activateMode(panel.dataset.authPanel || initialMode);
            }
            redirectToGoogleAuth(button.dataset.googleIntent || 'customer');
        });
    });

    if (pendingWorkshop) {
        pendingInputs.forEach((input) => {
            input.value = pendingWorkshop;
        });
    }
});
</script>
@endpush


