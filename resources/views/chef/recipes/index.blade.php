@extends('layouts.app')

@section('title', 'منطقة الشيف - إدارة وصفاتي')

@section('content')
@php
    use App\Models\Recipe;
    use Illuminate\Support\Facades\Storage;

    $statusMeta = [
        Recipe::STATUS_DRAFT => ['label' => 'مسودة', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
        Recipe::STATUS_PENDING => ['label' => 'قيد المراجعة', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
        Recipe::STATUS_APPROVED => ['label' => 'معتمدة', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
        Recipe::STATUS_REJECTED => ['label' => 'مرفوضة', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
    ];

    $visibilityMeta = [
        Recipe::VISIBILITY_PUBLIC => [
            'label' => 'عام',
            'bg' => 'bg-emerald-50',
            'text' => 'text-emerald-700',
            'icon' => 'fa-earth-americas',
            'hint' => 'مرئية لكل الزوار بعد الموافقة',
        ],
        Recipe::VISIBILITY_PRIVATE => [
            'label' => 'خاص',
            'bg' => 'bg-slate-100',
            'text' => 'text-slate-700',
            'icon' => 'fa-lock',
            'hint' => 'مخفية عن الزوار حتى وإن كانت معتمدة',
        ],
    ];

    $publicProfileUrl = auth()->check()
        ? route('chefs.show', ['chef' => auth()->id()])
        : null;
@endphp

<div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-wider text-orange-500 font-semibold mb-2">منطقة الشيف</p>
                <h1 class="text-3xl font-bold text-gray-900">لوحة التحكم بالوصفات</h1>
                <p class="text-gray-600 mt-1">أضف وصفات جديدة، تابع حالة المراجعة، وحدث محتواك بسهولة.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch justify-end gap-3">
                @if ($publicProfileUrl)
                    <a href="{{ $publicProfileUrl }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-orange-200 bg-white px-5 py-3 text-orange-600 font-semibold shadow-sm hover:bg-orange-50 hover:border-orange-300 transition">
                        <i class="fas fa-eye"></i>
                        عرض صفحتي العامة
                    </a>
                @endif
                <a href="{{ route('chef.workshops.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-white px-5 py-3 text-indigo-600 font-semibold shadow-sm hover:border-indigo-300 hover:bg-indigo-50 transition">
                    <i class="fas fa-video"></i>
                    ورش العمل
                </a>
                <a href="{{ route('chef.recipes.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-white font-semibold shadow hover:from-orange-600 hover:to-orange-700 transition">
                    <i class="fas fa-plus"></i>
                    إضافة وصفة جديدة
                </a>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-4 mb-8">
            @foreach ($statusMeta as $status => $meta)
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500">{{ $meta['label'] }}</p>
                        <span class="{{ $meta['bg'] }} {{ $meta['text'] }} inline-flex h-10 w-10 items-center justify-center rounded-2xl text-lg font-semibold">
                            {{ $statusCounts[$status] ?? 0 }}
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-gray-500">
                        @switch($status)
                            @case(Recipe::STATUS_DRAFT)
                                وصفاتك المحفوظة قبل الإرسال للمراجعة.
                                @break
                            @case(Recipe::STATUS_PENDING)
                                بانتظار موافقة فريق الإدارة.
                                @break
                            @case(Recipe::STATUS_APPROVED)
                                ظاهرة الآن على المنصة للمستخدمين.
                                @break
                            @case(Recipe::STATUS_REJECTED)
                                تحتاج لتعديلات ثم إعادة الإرسال.
                                @break
                        @endswitch
                    </p>
                </div>
            @endforeach
        </div>

        <section class="mb-10">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-500 via-purple-500 to-orange-500 text-white shadow-lg">
                <div class="absolute -top-24 -left-16 h-48 w-48 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-20 -right-20 h-56 w-56 rounded-full bg-white/20 blur-3xl opacity-60"></div>
                <div class="relative flex flex-col gap-8 p-6 sm:p-8 lg:grid lg:grid-cols-[1.1fr,0.9fr]">
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70">ورش Wasfah</p>
                            <h2 class="mt-2 text-2xl font-bold leading-snug md:text-3xl">اجعل جلساتك التعليمية نابضة بالحياة</h2>
                            <p class="mt-3 text-sm text-white/80">
                                راقب أداء الورش الأونلاين واللقاءات الحضورية في لمحة واحدة، وشجع جمهورك على الحجز عبر بطاقات جذابة وروابط مخصصة.
                                @if ($latestWorkshop)
                                    <span class="mt-2 block text-xs text-white/70">أحدث ورشة أنشأتها: {{ \Illuminate\Support\Str::limit($latestWorkshop->title, 40) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex snap-x snap-mandatory gap-3 overflow-x-auto pb-4 sm:grid sm:gap-3 sm:grid-cols-2 sm:overflow-visible sm:pb-0 sm:snap-none xl:grid-cols-4">
                            <div class="min-w-[220px] flex-shrink-0 snap-center rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm sm:min-w-0">
                                <p class="text-xs font-semibold text-white/70">إجمالي الورش</p>
                                <p class="mt-2 text-3xl font-bold">{{ $workshopStats['total'] ?? 0 }}</p>
                                <p class="text-xs text-white/70">كل ما أنشأته من جلسات.</p>
                            </div>
                            <div class="min-w-[220px] flex-shrink-0 snap-center rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm sm:min-w-0">
                                <p class="text-xs font-semibold text-white/70">ورش منشورة</p>
                                <p class="mt-2 text-3xl font-bold">{{ $workshopStats['active'] ?? 0 }}</p>
                                <p class="text-xs text-white/70">مرئية الآن لجمهورك.</p>
                            </div>
                            <div class="min-w-[220px] flex-shrink-0 snap-center rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm sm:min-w-0">
                                <p class="text-xs font-semibold text-white/70">جلسات أونلاين</p>
                                <p class="mt-2 text-3xl font-bold">{{ $workshopStats['online'] ?? 0 }}</p>
                                <p class="text-xs text-white/70">مجهزة بروابط بث مباشر.</p>
                            </div>
                            <div class="min-w-[220px] flex-shrink-0 snap-center rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm sm:min-w-0">
                                <p class="text-xs font-semibold text-white/70">ورش قادمة</p>
                                <p class="mt-2 text-3xl font-bold">{{ $workshopStats['upcoming'] ?? 0 }}</p>
                                <p class="text-xs text-white/70">استعد لها من الآن.</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('chef.workshops.create') }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-indigo-600 shadow hover:bg-indigo-50 sm:w-auto sm:justify-start">
                                <i class="fas fa-wand-magic-sparkles"></i>
                                أنشئ ورشة جديدة
                            </a>
                            <a href="{{ route('chef.workshops.index') }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-white/30 px-5 py-3 text-sm font-semibold text-white hover:border-white/60 sm:w-auto sm:justify-start">
                                <i class="fas fa-chalkboard-teacher"></i>
                                إدارة الورش الحالية
                            </a>
                        </div>
                    </div>
                    <div class="space-y-4 rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur sm:p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-2 text-sm font-semibold text-white">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                                    <i class="fas fa-calendar-star"></i>
                                </span>
                                أقرب الورش القادمة
                            </div>
                            <span class="text-xs text-white/70 sm:text-right">حتى ٣ ورش</span>
                        </div>
                        @forelse ($upcomingWorkshops as $workshop)
                            @php
                                $confirmed = (int) ($workshop->confirmed_bookings ?? 0);
                                $capacity = (int) ($workshop->max_participants ?? 0);
                                $fillPercent = $capacity > 0 ? (int) min(100, round(($confirmed / $capacity) * 100)) : 0;
                            @endphp
                            <div class="rounded-2xl bg-white/90 p-4 text-slate-800 shadow-sm transition hover:shadow-md">
                                <div class="flex flex-wrap items-center justify-between gap-2 text-xs font-semibold text-slate-500">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 {{ $workshop->is_online ? 'bg-indigo-100 text-indigo-600' : 'bg-orange-100 text-orange-600' }}">
                                        <i class="fas {{ $workshop->is_online ? 'fa-video' : 'fa-map-marker-alt' }}"></i>
                                        {{ $workshop->is_online ? 'أونلاين' : 'حضوري' }}
                                    </span>
                                    <span class="flex items-center gap-1 text-slate-400">
                                        <i class="fas fa-clock"></i>
                                        {{ optional($workshop->start_date)?->locale('ar')->translatedFormat('d F Y • h:i a') ?? 'موعد لاحق' }}
                                    </span>
                                </div>
                                <h3 class="mt-3 text-lg font-semibold text-slate-900">{{ $workshop->title }}</h3>
                                <p class="mt-2 text-sm text-slate-500 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($workshop->description, 110) }}
                                </p>
                                <div class="mt-4 space-y-2">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                        <span><i class="fas fa-users text-slate-400"></i> {{ $confirmed }} مشارك</span>
                                        <span>{{ $capacity > 0 ? $capacity : 'بدون حد' }} مقعد</span>
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-orange-500 to-orange-600 transition-all duration-300" style="width: {{ $fillPercent }}%;"></div>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <a href="{{ route('chef.workshops.edit', $workshop) }}"
                                       class="inline-flex w-full items-center justify-center gap-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 sm:w-auto sm:justify-start">
                                        <i class="fas fa-pen"></i>
                                        تعديل التفاصيل
                                    </a>
                                    @if ($workshop->is_online && $workshop->meeting_link)
                                        <a href="{{ route('chef.workshops.join', $workshop) }}"
                                           class="inline-flex w-full items-center justify-center gap-1 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow hover:from-indigo-600 hover:to-indigo-700 sm:w-auto sm:justify-start">
                                            <i class="fas fa-play"></i>
                                            فتح غرفة البث
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-white/40 px-6 py-10 text-center text-white/80">
                                <i class="fas fa-sparkles text-3xl"></i>
                                <p class="text-sm">لا توجد ورش مجدولة في الأيام القادمة.</p>
                                <a href="{{ route('chef.workshops.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/90 px-4 py-2 text-sm font-semibold text-indigo-600 shadow">
                                    <i class="fas fa-plus"></i>
                                    جهز ورشتك القادمة الآن
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        @php
            $linkPage = auth()->user()?->ensureLinkPage();
            $publicLinkUrl = $linkPage ? route('links.chef', $linkPage) : route('links');
        @endphp

        <div class="mb-10">
            <div class="rounded-3xl border border-orange-100 bg-white shadow-sm overflow-hidden flex flex-col md:flex-row">
                <div class="flex-1 px-6 py-6 md:px-8 md:py-7">
                    <div class="flex items-center gap-3 mb-4 text-orange-500">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-100">
                            <i class="fas fa-link text-lg"></i>
                        </span>
                        <h2 class="text-xl font-semibold text-gray-900">صفحة روابط Wasfah الموحدة</h2>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        صمم صفحة روابط احترافية وشاركها مع متابعيك في البايو. أضف روابط الورشات، المحتوى الاجتماعي، وأبرز أعمالك مع إمكانية التحكم الكامل بالتصميم.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                        <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-orange-600 font-medium">
                            <i class="fas fa-palette"></i>
                            تخصيص كامل
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-orange-600 font-medium">
                            <i class="fas fa-bolt"></i>
                            تحديثات فورية
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-orange-600 font-medium">
                            <i class="fas fa-share-alt"></i>
                            رابط جاهز للمشاركة
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-start gap-3 bg-gradient-to-bl from-orange-50 to-orange-100 px-6 py-6 md:px-8 md:py-7">
                    <a href="{{ route('chef.links.edit') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-white font-semibold shadow hover:from-orange-600 hover:to-orange-700 transition">
                        <i class="fas fa-pen"></i>
                        إدارة الصفحة
                    </a>
                    <a href="{{ $publicLinkUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl border border-orange-200 bg-white px-5 py-3 text-orange-600 font-semibold hover:bg-orange-50 transition">
                        <i class="fas fa-external-link-alt"></i>
                        عرض الرابط
                    </a>
                    <div class="text-xs text-gray-500 break-all max-w-sm ltr:text-left rtl:text-right">
                        {{ $publicLinkUrl }}
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            @if ($recipes->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <div class="mb-4 flex justify-center">
                        <div class="h-20 w-20 rounded-full bg-orange-50 flex items-center justify-center text-orange-400">
                            <i class="fas fa-utensils text-2xl"></i>
                        </div>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">لم تقم بإضافة أي وصفة بعد</h2>
                    <p class="text-sm text-gray-500 mb-6">ابدأ الآن بمشاركة وصفاتك مع مجتمع وصفة.</p>
                    <a href="{{ route('chef.recipes.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-white font-semibold hover:from-orange-600 hover:to-orange-700 transition">
                        <i class="fas fa-plus"></i>
                        إضافة أول وصفة
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">الوصفة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">الظهور</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">آخر تحديث</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">التصنيف</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 w-56">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($recipes as $recipe)
                                <tr class="hover:bg-orange-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="h-14 w-14 overflow-hidden rounded-xl border border-gray-100 bg-gray-100">
                                                @if ($recipe->image)
                                                    <img src="{{ Storage::disk('public')->url($recipe->image) }}" alt="{{ $recipe->title }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center text-gray-300">
                                                        <i class="fas fa-utensils"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $recipe->title }}</p>
                                                <p class="text-sm text-gray-500 line-clamp-2">{{ \Illuminate\Support\Str::limit($recipe->description, 80) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $meta = $statusMeta[$recipe->status] ?? $statusMeta[Recipe::STATUS_DRAFT];
                                        @endphp
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium {{ $meta['bg'] }} {{ $meta['text'] }}">
                                            <span class="h-2 w-2 rounded-full bg-current"></span>
                                            {{ $meta['label'] }}
                                        </span>
                                        @if ($recipe->status === Recipe::STATUS_APPROVED && $recipe->approved_at)
                                            <p class="mt-1 text-xs text-emerald-600">تاريخ الموافقة: {{ $recipe->approved_at->locale('ar')->translatedFormat('d F Y') }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $visibilityInfo = $visibilityMeta[$recipe->visibility] ?? $visibilityMeta[Recipe::VISIBILITY_PUBLIC];
                                        @endphp
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium {{ $visibilityInfo['bg'] }} {{ $visibilityInfo['text'] }}">
                                            <i class="fas {{ $visibilityInfo['icon'] }}"></i>
                                            {{ $visibilityInfo['label'] }}
                                        </span>
                                        <p class="mt-1 text-xs text-gray-500">{{ $visibilityInfo['hint'] }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $recipe->updated_at?->locale('ar')->translatedFormat('d F Y - h:i a') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $recipe->category->name ?? 'غير محدد' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('chef.recipes.edit', $recipe) }}" class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50">
                                                <i class="fas fa-pen"></i>
                                                تعديل
                                            </a>

                                            @if (in_array($recipe->status, [Recipe::STATUS_DRAFT, Recipe::STATUS_REJECTED], true))
                                                <form method="POST" action="{{ route('chef.recipes.submit', $recipe) }}">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-orange-200 px-3 py-1.5 text-sm text-orange-600 hover:bg-orange-50">
                                                        <i class="fas fa-paper-plane"></i>
                                                        إرسال للمراجعة
                                                    </button>
                                                </form>
                                            @endif

                                            @if (in_array($recipe->status, [Recipe::STATUS_DRAFT, Recipe::STATUS_REJECTED], true))
                                                <form method="POST" action="{{ route('chef.recipes.destroy', $recipe) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الوصفة؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-red-200 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">
                                                        <i class="fas fa-trash"></i>
                                                        حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                    {{ $recipes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
