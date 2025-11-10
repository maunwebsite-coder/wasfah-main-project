@extends('layouts.app')

@section('title', 'شروط الخدمة – وصفة')

@section('content')
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-4xl space-y-10">
        <div class="text-center space-y-3">
            <p class="text-orange-500 font-semibold uppercase tracking-widest">Terms of Service</p>
            <h1 class="text-3xl font-extrabold text-gray-900">شروط الخدمة – Wasfah</h1>
            <p class="text-gray-600 leading-relaxed">
                باستخدامك لموقع wasfah.ae وتطبيقاته، فإنك توافق على الشروط التالية التي تنظّم علاقتك معنا.
            </p>
        </div>

        <div class="space-y-8 text-gray-800 leading-8">
            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">1. الحسابات والتسجيل</h2>
                <p>
                    يجب أن تكون المعلومات التي تزودنا بها دقيقة ومكتملة. يحق لـ Wasfah رفض أو إيقاف أي حساب يخالف السياسات أو يقدّم بيانات مضللة.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">2. المحتوى وحقوق الملكية</h2>
                <p>
                    تبقى حقوق الملكية للوصفات، الصور، والورشات بموجب القوانين المعمول بها. يمنحنا رفعك للمحتوى ترخيصاً غير حصري لعرضه داخل المنصة.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">3. المدفوعات والحجوزات</h2>
                <p>
                    تخضع عمليات شراء الورشات والأدوات لسياسات الدفع والاسترداد الخاصة بنا. قد يتم خصم رسوم إلغاء في حال التراجع بعد الموعد المحدد.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">4. الاستخدام المقبول</h2>
                <p>
                    يمنع إساءة استخدام المنصة أو محاولة الوصول غير المصرّح به إلى أنظمتنا. يحق لنا اتخاذ إجراءات فورية عند رصد نشاط مخالف.
                </p>
            </article>

            <article class="space-y-3">
                <h2 class="text-xl font-bold text-gray-900">5. التعديلات على الشروط</h2>
                <p>
                    قد نقوم بتحديث هذه الشروط حسب الحاجة. يُعد استمرارك في استخدام Wasfah بعد نشر أي تحديث بمثابة موافقة عليه.
                </p>
            </article>
        </div>

        <div class="rounded-2xl bg-orange-50 border border-orange-200 p-6 text-sm text-gray-700 space-y-2">
            <p>آخر تحديث: {{ now()->format('Y-m-d') }}</p>
            <p>للاستفسارات القانونية تواصل معنا عبر <a href="mailto:legal@wasfah.ae" class="text-orange-600 font-semibold">legal@wasfah.ae</a>.</p>
        </div>
    </div>
</section>
@endsection

