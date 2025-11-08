@extends('layouts.app')

@section('title', 'شراكات وصفة')

@section('content')
<div class="bg-gradient-to-b from-orange-50 via-white to-white py-12">
    <div class="container mx-auto px-4">
        <div class="space-y-6 text-center md:text-right">
            <span class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white px-4 py-1.5 text-sm font-semibold text-orange-600 shadow-sm">
                <i class="fas fa-handshake-angle"></i>
                <span>برنامج الشراكة في نص وصفة</span>
            </span>
            <h1 class="text-4xl font-black leading-tight text-slate-900 md:text-5xl">
                شاركنا جمهور وصفة، واحصل على نسبة من كل شخص يأتي من طرفك ويشارك في الورشات
            </h1>
            <p class="text-lg text-slate-600 md:text-xl">
                نص وصفة عبارة عن شبكة شيفات ومنتجين تنشر وصفات موثوقة وتبث ورش مباشرة. كل شريك يحصل على رابط تتبع مخصص، وعند حضور المشتركين القادمين من طرفه لورشة مدفوعة أو الاشتراك في باقة وصفات، يتم تحويل النسبة المتفق عليها بشكل دوري.
            </p>
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full bg-orange-500 px-8 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-orange-600">
                    اطلب مكالمة تعريفية
                    <i class="fas fa-arrow-left ml-2"></i>
                </a>
                <a href="#partner-steps" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-8 py-3 text-base font-semibold text-slate-700 transition hover:border-orange-200 hover:text-orange-600">
                    تعرّف على طريقة الانضمام
                    <i class="fas fa-circle-info ml-2 text-orange-500"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="bg-white py-16">
    <div class="container mx-auto px-4">
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-6 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-orange-500">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-900">نسبة من كل إحالة</h3>
                <p class="mt-2 text-slate-600">عمولة تبدأ من 15% على كل مشترك يحجز ورشة أو باقة وصفات عبر رابط الشريك، مع لوحة تقارير لمتابعة الأرقام.</p>
            </div>
            <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-6 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-orange-500">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-900">ورش مشتركة</h3>
                <p class="mt-2 text-slate-600">ننتج ورش متخصصة تحمل هوية الشريك، ويمكن استضافة شيف من فريقه أو من شبكة وصفة مع توفير غرفة بث جاهزة.</p>
            </div>
            <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-6 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-orange-500">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-900">انتشار للوصفات</h3>
                <p class="mt-2 text-slate-600">كل وصفة يتم نشرها عبر وصفة تظهر في صفحات الوصفات، النشرات البريدية، وروابط الشيف، ما يرفع مشاهدات المنتج أو الخدمة.</p>
            </div>
        </div>
    </div>
</div>

<div id="partner-steps" class="bg-gray-50 py-16">
    <div class="container mx-auto px-4 space-y-10">
        <div class="text-center md:text-right">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">كيف تصبح شريكاً</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900 md:text-4xl">ثلاث خطوات واضحة للانضمام</h2>
            <p class="mt-2 text-slate-600">نركز على السرعة والوضوح، لذلك جميع المتطلبات موحدة ويمكن متابعتها من خلال لوحة خاصة.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @php
                $steps = [
                    ['title' => 'طلب الانضمام', 'desc' => 'تعبئة نموذج مختصر حول نوع النشاط والجمهور المستهدف، وسيصلك رابط لوحة المتابعة.'],
                    ['title' => 'تحديد النسبة والرابط', 'desc' => 'نحدد نسبة العمولة، وننشئ روابط تتبع فورية لتجربة الوصفات أو الورش.'],
                    ['title' => 'تشغيل الحملة', 'desc' => 'إطلاق المحتوى أو الورش المشتركة، واستلام تقارير العائد أسبوعياً.'],
                ];
            @endphp
            @foreach ($steps as $index => $step)
                <div class="rounded-3xl border border-white bg-white p-6 shadow-lg shadow-orange-100/40">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-500 text-xl font-bold text-white">{{ $index + 1 }}</span>
                    <h3 class="mt-4 text-xl font-semibold text-slate-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-slate-600">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white py-16">
    <div class="container mx-auto px-4 grid gap-10 lg:grid-cols-2">
        <div class="space-y-6">
            <p class="text-sm font-semibold text-orange-500">نظام الشيف وروابطه</p>
            <h2 class="text-3xl font-bold text-slate-900">كل شيف يستطيع نشر وصفاته وصناعة صفحة روابط خاصة</h2>
            <p class="text-slate-600">نمنح الشيفات والشركاء لوحة إعدادات تتيح نشر الوصفات مع صور وفيديوهات، ثم ربطها في صفحة روابط ديناميكية مشابهة لصفحات "link in bio". يمكن مشاركة الصفحة في إنستغرام أو واتساب، وتعرض أحدث الورش، أدوات الشيف، وكوبونات الشريك.</p>
            <ul class="space-y-3 text-slate-700">
                <li class="flex items-start gap-3">
                    <span class="mt-1 flex h-7 w-7 items-center justify-center rounded-full bg-orange-100 text-orange-600"><i class="fas fa-check"></i></span>
                    <span>إمكانية إنشاء صفحة روابط متعددة لكل شيف أو فرع مع تحكم كامل بالترتيب.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 flex h-7 w-7 items-center justify-center rounded-full bg-orange-100 text-orange-600"><i class="fas fa-check"></i></span>
                    <span>نشر الوصفات مباشرة على المنصة مع خيار جعلها عامة أو خاصة لجمهور الشريك.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 flex h-7 w-7 items-center justify-center rounded-full bg-orange-100 text-orange-600"><i class="fas fa-check"></i></span>
                    <span>تتبع زيارات كل رابط لمعرفة عدد المهتمين والورشة التي جذبتهم.</span>
                </li>
            </ul>
        </div>
        <div class="rounded-3xl border border-orange-100 bg-orange-50/60 p-8 shadow-xl">
            <p class="text-sm font-semibold text-orange-500">نماذج تعاون سريعة</p>
            <div class="mt-6 space-y-5">
                <div class="rounded-2xl border border-white bg-white p-5 shadow">
                    <h3 class="text-xl font-semibold text-slate-900">برنامج الإحالة</h3>
                    <p class="mt-2 text-slate-600">اربط متجرك أو منصتك برابط وصفة، واحصل على عمولة عن كل تسجيل أو حجز ورشة يأتي من طرفك.</p>
                </div>
                <div class="rounded-2xl border border-white bg-white p-5 shadow">
                    <h3 class="text-xl font-semibold text-slate-900">ورش مشتركة</h3>
                    <p class="mt-2 text-slate-600">نقدّم ورش Live أو مصوّرة باسم الشريك، وتُضاف تلقائياً إلى صفحات الروابط الخاصة بالشيفات للترويج السريع.</p>
                </div>
                <div class="rounded-2xl border border-white bg-white p-5 shadow">
                    <h3 class="text-xl font-semibold text-slate-900">حزم وصفات موجهة</h3>
                    <p class="mt-2 text-slate-600">إطلاق سلسلة وصفات تستخدم منتجات الشريك مع وجود كوبون خاص، ما يزيد الطلب ويمنح الجمهور دليلاً عملياً.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="rounded-3xl border border-orange-100 bg-white p-8 text-center shadow-lg">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-500">جاهز للتجربة؟</p>
            <h2 class="mt-3 text-3xl font-extrabold text-slate-900">سنرسل لك خطة شراكة أولية خلال 3 أيام عمل</h2>
            <p class="mt-3 text-slate-600">شاركنا تفاصيل نشاطك عبر نموذج التواصل، وسنتواصل معك بجدول ورش مقترح ونسبة العمولة المناسبة.</p>
            <a href="{{ route('contact') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-orange-500 px-8 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-orange-600">
                انتقل إلى نموذج التواصل
                <i class="fas fa-arrow-left ml-2"></i>
            </a>
        </div>
    </div>
</div>
@endsection
