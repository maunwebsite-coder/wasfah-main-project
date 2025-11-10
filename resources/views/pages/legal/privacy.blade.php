@extends('layouts.app')

@section('title', 'سياسة الخصوصية – وصفة')

@section('content')
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 max-w-4xl space-y-10">
        <div class="text-center space-y-3">
            <p class="text-orange-500 font-semibold uppercase tracking-widest">Privacy Policy</p>
            <h1 class="text-3xl font-extrabold text-gray-900">سياسة الخصوصية – Wasfah</h1>
            <p class="text-gray-600 leading-relaxed">
                نحمي بياناتك الشخصية ونوضح أدناه كيفية جمعها، استخدامـها، وحفظها عند استخدام wasfah.ae.
            </p>
        </div>

        <div class="space-y-8 text-gray-800 leading-8">
            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">1. البيانات التي نجمعها</h2>
                <p>
                    نقوم بجمع معلومات التسجيل الأساسية، بيانات الدفع المشفّرة، وتفضيلات الاستخدام لتحسين التجربة وتقديم الخدمات.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">2. طريقة الاستخدام</h2>
                <p>
                    نستخدم بياناتك لإدارة حسابك، معالجة المدفوعات، إرسال التنبيهات، وتحسين محتوى الوصفات والورشات.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">3. مشاركة البيانات</h2>
                <p>
                    لا نشارك بياناتك مع أطراف ثالثة إلا عند الضرورة القانونية أو مع مزودي الدفع والخدمات المرتبطين بالمنصة وفق اتفاقيات حماية البيانات.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">4. ملفات تعريف الارتباط</h2>
                <p>
                    نستخدم ملفات تعريف الارتباط (Cookies) التحليلية والوظيفية لتخصيص التجربة. يمكنك تعديل تفضيلاتك من إعدادات المتصفح.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">5. حقوقك</h2>
                <p>
                    يمكنك طلب تصحيح بياناتك، تنزيل نسخة منها، أو طلب حذفها عبر التواصل مع فريقنا على البريد أدناه.
                </p>
            </article>
        </div>

        <div class="rounded-2xl bg-white shadow-sm border border-gray-200 p-6 text-sm text-gray-700 space-y-2">
            <p>آخر تحديث: {{ now()->format('Y-m-d') }}</p>
            <p>لطلبات الخصوصية: <a href="mailto:privacy@wasfah.ae" class="text-orange-600 font-semibold">privacy@wasfah.ae</a></p>
        </div>
    </div>
</section>
@endsection

