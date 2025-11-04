@extends('layouts.auth')

@section('title', 'تسجيل الدخول أو إنشاء حساب عبر Google - وصفة')

@push('styles')
<style>
    .auth-shell {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(2rem, 3vw, 4rem);
        background: #fff7ed;
    }
    .auth-card {
        width: min(100%, 860px);
        background: #ffffff;
        border-radius: 2rem;
        padding: clamp(2rem, 4vw, 3.5rem);
        box-shadow: 0 35px 60px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(249, 115, 22, 0.12);
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }
    .auth-header {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .auth-badge {
        align-self: center;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.6rem 1.4rem;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.12);
        color: #c2410c;
        font-size: 0.9rem;
        font-weight: 600;
    }
    .auth-header h1 {
        font-size: clamp(2rem, 2.6vw + 1rem, 2.8rem);
        color: #1f2937;
        line-height: 1.2;
    }
    .auth-header p {
        color: #6b7280;
        font-size: 1rem;
        max-width: 40rem;
        margin: 0 auto;
        line-height: 1.8;
    }
    .persona-grid {
        display: grid;
        gap: 1.5rem;
    }
    .persona-card {
        border-radius: 1.75rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: linear-gradient(145deg, #ffffff, #fff9f1);
        box-shadow: 0 25px 45px rgba(15, 23, 42, 0.08);
        padding: clamp(1.6rem, 2.8vw, 2.4rem);
        display: grid;
        gap: 1.1rem;
    }
    .persona-card h2 {
        font-size: 1.4rem;
        color: #1f2937;
        margin: 0;
    }
    .persona-card p {
        color: #6b7280;
        font-size: 0.98rem;
        line-height: 1.8;
        margin: 0;
    }
    .persona-highlights {
        display: grid;
        gap: 0.75rem;
        font-size: 0.94rem;
        color: #7c2d12;
    }
    .persona-highlights li {
        list-style: none;
        position: relative;
        padding-right: 1.8rem;
        line-height: 1.7;
    }
    [dir="rtl"] .persona-highlights li::before {
        content: '✓';
        position: absolute;
        top: 0;
        right: 0;
        color: #f97316;
        font-weight: 700;
    }
    .google-actions {
        display: grid;
        gap: 0.75rem;
    }
    .google-btn {
        border-radius: 1rem;
        padding: 0.95rem 1.2rem;
        font-size: 1.05rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.85rem;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        width: 100%;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: #ffffff;
        color: #1f2937;
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.08);
    }
    .google-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.12);
        border-color: rgba(249, 115, 22, 0.35);
    }
    .google-btn--outline {
        background: #ffffff;
    }
    .google-btn--accent {
        background: linear-gradient(135deg, #fb923c, #f97316);
        color: #ffffff;
        border: none;
        box-shadow: 0 22px 38px rgba(249, 115, 22, 0.28);
    }
    .google-btn--accent:hover {
        box-shadow: 0 26px 46px rgba(249, 115, 22, 0.36);
    }
    .google-btn--chef {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #ffffff;
        border: none;
        box-shadow: 0 24px 40px rgba(249, 115, 22, 0.32);
    }
    .google-btn--chef:hover {
        box-shadow: 0 28px 48px rgba(249, 115, 22, 0.4);
    }
    .support-note {
        text-align: center;
        font-size: 0.92rem;
        color: #9a3412;
        background: rgba(253, 230, 216, 0.6);
        border: 1px solid rgba(249, 115, 22, 0.25);
        border-radius: 1.2rem;
        padding: 1rem;
        line-height: 1.8;
    }
    .legal-note {
        text-align: center;
        color: #94a3b8;
        font-size: 0.88rem;
        line-height: 1.6;
    }
    @media (min-width: 768px) {
        .persona-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .auth-card {
            border-radius: 1.5rem;
            padding: 2.2rem 1.6rem;
            gap: 2rem;
        }
        .persona-card {
            border-radius: 1.4rem;
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
            <h1>تسجيل الدخول أو إنشاء حساب عبر Google فقط</h1>
            <p>
                وفرنا لك تجربة موحدة بنقرة واحدة. كل الحسابات تُفعّل فوراً عبر Google بدون رسائل تحقق بالبريد.
                اختر ما إذا كنت تريد تسجيل الدخول لحسابك الحالي أو إنشاء حساب جديد (مستخدم أو شيف) وسنقودك للخطوات التالية فوراً.
            </p>
            <p class="text-sm text-[#9a3412] leading-relaxed">
                إذا كان لديك حجز ورشة قيد الإكمال فسنحتفظ به لك بعد تسجيل الدخول.
            </p>
        </div>

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 text-right">
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-600 text-right">
                {{ session('info') }}
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-600 text-right">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 text-right">
                <ul class="list-disc space-y-1 pe-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="persona-grid">
            <div class="persona-card">
                <h2>المتابعة كمستخدم وصفة</h2>
                <p>
                    مناسب للباحثين عن ورشات الطبخ، الوصفات المحفوظة، والتنبيهات الشخصية.
                    سيتم إنشاء أو تسجيل دخول حسابك مباشرةً عبر Google.
                </p>
                <ul class="persona-highlights">
                    <li>احجز الورشات المباشرة واطّلع على مواعيدها فوراً.</li>
                    <li>احفظ الوصفات المفضلة لديك وتابع تحديثاتها بسهولة.</li>
                    <li>استفد من الإشعارات المخصصة لتحسين تجربتك.</li>
                </ul>
                <div class="google-actions">
                    <button
                        type="button"
                        class="google-btn google-btn--outline"
                        data-google-intent="customer"
                        data-google-flow="login"
                    >
                        <img src="https://img.icons8.com/color/32/google-logo.png" alt="Google">
                        تسجيل الدخول عبر Google
                    </button>
                    <button
                        type="button"
                        class="google-btn google-btn--accent"
                        data-google-intent="customer"
                        data-google-flow="register_customer"
                    >
                        <img src="https://img.icons8.com/color/32/google-logo.png" alt="Google">
                        إنشاء حساب مستخدم جديد عبر Google
                    </button>
                </div>
            </div>

            <div class="persona-card">
                <h2>الانضمام كشيف محترف</h2>
                <p>
                    مثالي للشيف المحترف الذي يرغب بنشر ورشاته ودوراته والوصفات الخاصة به مع جمهور وصفة.
                    بعد تسجيل الدخول سنرشدك لإكمال بيانات الاعتماد المهنية.
                </p>
                <ul class="persona-highlights">
                    <li>اعرض ورشاتك ووصفاتك أمام مجتمع وصفة.</li>
                    <li>استقبل طلبات الحجز والتفاعل مع المتابعين بسهولة.</li>
                    <li>إكمال ملفك الاحترافي وخطوات الاعتماد بعد تسجيل الدخول مباشرةً.</li>
                </ul>
                <div class="google-actions">
                    <button
                        type="button"
                        class="google-btn google-btn--outline"
                        data-google-intent="chef"
                        data-google-flow="login"
                    >
                        <img src="https://img.icons8.com/color/32/google-logo.png" alt="Google">
                        تسجيل الدخول كشيف عبر Google
                    </button>
                    <button
                        type="button"
                        class="google-btn google-btn--chef"
                        data-google-intent="chef"
                        data-google-flow="register_chef"
                    >
                        <img src="https://img.icons8.com/color/32/google-logo.png" alt="Google">
                        إنشاء حساب شيف جديد عبر Google
                    </button>
                </div>
            </div>
        </div>

        <div class="support-note">
            في حال واجهت أي مشكلة أثناء تسجيل الدخول عبر Google، تواصل معنا عبر البريد
            <a href="mailto:support@wasfah.com" class="font-semibold text-[#b45309] underline underline-offset-4">support@wasfah.com</a>
            وسنساعدك فوراً.
        </div>

        <p class="legal-note">
            بالمتابعة عبر Google فإنك توافق على شروط الخدمة وسياسة الخصوصية الخاصة بوصفة.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const pendingWorkshop = params.get('pending_workshop_booking');

    const redirectToGoogleAuth = (intent, flow) => {
        const url = new URL('{{ route('google.redirect') }}', window.location.origin);
        if (pendingWorkshop) {
            url.searchParams.set('pending_workshop_booking', pendingWorkshop);
        }
        if (flow) {
            url.searchParams.set('flow', flow);
        }
        if (intent) {
            url.searchParams.set('intent', intent);
        }
        window.location.href = url.toString();
    };

    document.querySelectorAll('[data-google-intent]').forEach((button) => {
        button.addEventListener('click', () => {
            const intent = button.dataset.googleIntent || 'customer';
            const flow = button.dataset.googleFlow || 'login';
            redirectToGoogleAuth(intent, flow);
        });
    });
});
</script>
@endpush
