@extends('layouts.app')

@section('title', 'الملف الشخصي - موقع وصفة')

@php
    use Carbon\Carbon;
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="container mx-auto px-4">
            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                    <i class="fas fa-check-circle ml-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    <i class="fas fa-exclamation-triangle ml-2"></i>
                    حدثت بعض الأخطاء. يرجى مراجعة الحقول أدناه.
                </div>
            @endif

            @include('profile.partials.hero')

            <div class="mt-6">
                @include('profile.partials.nav', ['active' => 'overview'])
            </div>

            <section class="mt-6 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                @php
                    $limitedBookings = $bookedWorkshops->take(3);
                    $totalBookings = $bookedWorkshops->count();
                    $statusLabels = [
                        'pending' => 'بانتظار المراجعة',
                        'confirmed' => 'مؤكد',
                        'cancelled' => 'ملغي',
                    ];
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'confirmed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                @endphp

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">سجل الحجوزات</h2>
                        <p class="text-sm text-gray-500">
                            يتم ترتيب الحجوزات من الأحدث إلى الأقدم. استخدم زر التفاصيل لمعرفة كل المعلومات أو إدارة الحجز.
                        </p>
                    </div>
                    <div class="flex flex-col items-start gap-2 text-sm text-gray-500 md:items-end">
                        @if ($totalBookings > 0)
                            <span class="flex flex-wrap gap-1 whitespace-nowrap">
                                <span>العرض الحالي</span>
                                <span>1 - {{ $limitedBookings->count() }}</span>
                                <span>من إجمالي</span>
                                <span>{{ $totalBookings }}</span>
                            </span>
                        @endif
                        <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-orange-200 bg-orange-50 px-3 py-1 font-semibold text-orange-600 hover:border-orange-300 hover:text-orange-700">
                            <i class="fas fa-arrow-left text-xs"></i>
                            عرض جميع الحجوزات
                        </a>
                    </div>
                </div>

                @if ($totalBookings > 0)
                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">الورشة</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">التاريخ</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">الحالة</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">طريقة الحضور</th>
                                    <th class="px-4 py-2 text-right font-semibold text-gray-600">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($limitedBookings as $booking)
                                    @php
                                        $workshop = $booking->workshop;
                                        $start = optional($workshop?->start_date);
                                        $status = $booking->status;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-gray-800">
                                                {{ $workshop?->title ?? 'ورشة بدون عنوان' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                رقم الحجز: {{ $booking->public_code ?? $booking->id }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-gray-600">
                                            {{ $start ? $start->locale('ar')->translatedFormat('d F Y • h:i a') : '—' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-600' }}">
                                                <i class="fas fa-circle text-[6px]"></i>
                                                {{ $statusLabels[$status] ?? $status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-gray-600">
                                            {{ $workshop?->is_online ? 'أونلاين' : 'حضوري' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3 text-sm">
                                                <a href="{{ route('bookings.show', $booking) }}" class="font-semibold text-orange-600 hover:text-orange-700">
                                                    التفاصيل
                                                </a>
                                                @if ($status === 'confirmed' && $workshop?->is_online)
                                                    <a href="{{ $booking->secure_join_url }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1 focus:ring-offset-white">
                                                        <i class="fas fa-door-open text-xs"></i>
                                                        دخول الورشة
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-gray-500">
                        لم تقم بحجز أي ورشة حتى الآن. استكشف الورش المتاحة واحجز مكانك الأول.
                        <div class="mt-4">
                            <a href="{{ route('workshops') }}" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-orange-600">
                                <i class="fas fa-search text-xs"></i>
                                تصفح الورش الآن
                            </a>
                        </div>
                    </div>
                @endif
            </section>

            <section class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">الوصفات المحفوظة</span>
                        <i class="fas fa-bookmark text-orange-500"></i>
                    </div>
                    <div class="mt-4 text-3xl font-extrabold text-gray-800">
                        {{ number_format($stats['saved_recipes_count']) }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $stats['saved_recipes_count'] > 0 ? 'مكتبتك الخاصة تكبر يوماً بعد يوم.' : 'ابدأ بحفظ الوصفات المفضلة لتصل إليها سريعاً.' }}
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('saved.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                            استعرض الوصفات المحفوظة
                        </a>
                    </div>
                </article>

                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">الوصفات التي جربتها</span>
                        <i class="fas fa-utensils text-orange-500"></i>
                    </div>
                    <div class="mt-4 text-3xl font-extrabold text-gray-800">
                        {{ number_format($stats['made_recipes_count']) }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $stats['made_recipes_count'] > 0 ? 'انطباعاتك عن الوصفات تساعد باقي المجتمع.' : 'شاركنا بأول وصفة تقوم بإعدادها من وصفة.' }}
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('saved.index') }}#made" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                            أرشيف التجارب
                        </a>
                    </div>
                </article>

                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">ورش قادمة</span>
                        <i class="fas fa-calendar-alt text-orange-500"></i>
                    </div>
                    <div class="mt-4 text-3xl font-extrabold text-gray-800">
                        {{ number_format($stats['upcoming_workshops_count']) }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $stats['upcoming_workshops_count'] > 0 ? 'جاهزون لاستقبال جلساتك القادمة.' : 'لا توجد ورش مجدولة، تصفح الورش المتاحة الآن.' }}
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('workshops') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                            استكشف الورش المتاحة
                        </a>
                    </div>
                </article>

                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">نقاط التفاعل</span>
                        <i class="fas fa-star text-orange-500"></i>
                    </div>
                    <div class="mt-4 text-3xl font-extrabold text-gray-800">
                        {{ number_format($stats['engagement_score']) }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        نقاطك ترتفع بالحفظ والتجربة وحضور الورش. استمر في التفاعل لتحصل على مكافآت مميزة قريباً.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('profile.statistics') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                            تفاصيل نقاط التفاعل
                        </a>
                    </div>
                </article>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-3">
                <article class="lg:col-span-2 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-800">أحدث نشاط</h2>
                        <a href="{{ route('profile.activity') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                            عرض كل النشاطات
                        </a>
                    </div>
                    <div class="mt-4 space-y-4">
                        @php
                            $latestActivities = $activityFeed->take(4);
                        @endphp
                        @forelse ($latestActivities as $activity)
                            @php
                                $timestamp = $activity['timestamp'] instanceof Carbon
                                    ? $activity['timestamp']
                                    : Carbon::parse($activity['timestamp']);
                            @endphp
                            <div class="flex flex-col gap-2 rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-orange-600">
                                        @switch($activity['type'])
                                            @case('saved_recipe')
                                                <i class="fas fa-bookmark ml-1"></i>
                                                حفظت وصفة
                                                @break
                                            @case('made_recipe')
                                                <i class="fas fa-check-circle ml-1"></i>
                                                أنهيت وصفة
                                                @break
                                            @case('workshop_booking')
                                                <i class="fas fa-calendar-check ml-1"></i>
                                                حجزت ورشة
                                                @break
                                            @default
                                                نشاط جديد
                                        @endswitch
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $timestamp->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="text-sm font-medium text-gray-800">
                                    {{ $activity['title'] }}
                                </div>
                                @if (!empty($activity['meta']['category']))
                                    <span class="text-xs text-gray-500 flex flex-wrap items-center gap-1">
                                        <span>التصنيف:</span>
                                        <span>{{ $activity['meta']['category'] }}</span>
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-6 text-center text-gray-500">
                                لم يتم تسجيل أي نشاط بعد. عندما تحفظ وصفة أو تحجز ورشة ستظهر هنا.
                            </div>
                        @endforelse
                    </div>
                </article>

                <article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-bold text-gray-800">الورشة القادمة</h2>
                    @if ($nextWorkshop)
                        @php
                            $workshopModel = optional($nextWorkshop)->workshop;
                            $workshopStart = optional($workshopModel?->start_date);
                        @endphp
                        <div class="mt-4 rounded-2xl border border-orange-100 bg-orange-50/50 p-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ $workshopModel?->title ?? 'ورشة بدون عنوان' }}
                            </h3>
                            @if ($workshopStart)
                                <p class="mt-2 text-sm text-gray-600">
                                    <i class="fas fa-clock ml-2 text-orange-500"></i>
                                    {{ $workshopStart->locale('ar')->translatedFormat('d F Y • h:i a') }}
                                </p>
                            @endif
                            <p class="mt-2 text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt ml-2 text-orange-500"></i>
                                {{ $workshopModel?->is_online ? 'أونلاين عبر Google Meet' : ($workshopModel?->location ?? 'سيتم تحديد الموقع لاحقاً') }}
                            </p>
                            <a href="{{ route('bookings.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-orange-600 hover:text-orange-700">
                                تفاصيل الحجز
                                <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    @else
                        <div class="mt-4 rounded-2xl border border-dashed border-gray-200 p-5 text-center text-gray-500">
                            لا توجد ورش قادمة حالياً. تصفح الورش المتاحة واحجز مقعدك القادم.
                            <div class="mt-4">
                                <a href="{{ route('workshops') }}" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-medium text-white shadow hover:bg-orange-600">
                                    <i class="fas fa-search text-xs"></i>
                                    استكشف الورش
                                </a>
                            </div>
                        </div>
                    @endif
                </article>
            </section>

            <section class="mt-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">الوصفات المحفوظة مؤخراً</h2>
                    <a href="{{ route('saved.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">
                        إدارة كل الوصفات المحفوظة
                    </a>
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @php
                        $savedPreview = $savedRecipes->take(3);
                    @endphp
                    @forelse ($savedPreview as $recipe)
                        <article class="rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
                            <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="block">
                                <div class="aspect-w-16 aspect-h-10 overflow-hidden rounded-t-2xl bg-gray-100">
                                    <img
                                        src="{{ $recipe->image_url ?? $recipe->image ?? asset('image/Brownies.png') }}"
                                        alt="{{ $recipe->title }}"
                                        class="h-full w-full object-cover"
                                    >
                                </div>
                                <div class="p-4 space-y-2">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $recipe->title }}</h3>
                                    @if ($recipe->category?->name)
                                        <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-xs font-medium text-orange-600">
                                            {{ $recipe->category->name }}
                                        </span>
                                    @endif
                                        <p class="text-xs text-gray-500 flex flex-wrap items-center gap-1">
                                            <span>تمت الإضافة في</span>
                                            <span>{{ optional($recipe->userInteraction->updated_at ?? $recipe->userInteraction->created_at)->diffForHumans() }}</span>
                                        </p>
                                </div>
                            </a>
                        </article>
                    @empty
                        <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-gray-200 bg-white p-6 text-center text-gray-500">
                            لم تحفظ أي وصفة بعد. تصفح وصفاتنا المتنوعة وابدأ بتجميع قائمتك الخاصة.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="mt-8 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-bold text-gray-800">إنجازاتك</h2>
                <p class="mt-2 text-sm text-gray-500">
                    راقب تقدمك واستمر في التفاعل لتحصل على مستويات أعلى.
                </p>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    @foreach ($achievements as $achievement)
                        @php
                            $progress = $achievement['progress'] ?? 0;
                        @endphp
                        <article class="rounded-2xl border border-orange-100 bg-orange-50/50 p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600">
                                    <i class="fas fa-{{ $achievement['icon'] }} text-sm"></i>
                                    {{ $achievement['title'] }}
                                </span>
                                <span class="text-xs uppercase text-gray-500">
                                    {{ $achievement['current_level'] }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-gray-600 flex flex-wrap gap-1">
                                @if (!empty($achievement['description_prefix']) || !empty($achievement['description_suffix']))
                                    @if (!empty($achievement['description_prefix']))
                                        <span>{{ $achievement['description_prefix'] }}</span>
                                    @endif
                                    <span>{{ number_format($achievement['count']) }}</span>
                                    @if (!empty($achievement['description_suffix']))
                                        <span>{{ $achievement['description_suffix'] }}</span>
                                    @endif
                                @elseif (!empty($achievement['description']))
                                    <span>{{ $achievement['description'] }}</span>
                                @endif
                            </p>
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>التقدم</span>
                                    @if (!empty($achievement['next_goal']))
                                        <span class="flex flex-wrap items-center gap-1">
                                            <span>الهدف التالي:</span>
                                            <span>{{ number_format($achievement['next_goal']) }}</span>
                                        </span>
                                    @else
                                        <span>أعلى مستوى</span>
                                    @endif
                                </div>
                                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-white shadow-inner">
                                    <div class="h-full rounded-full bg-orange-500" style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="mt-2 text-sm font-semibold text-gray-800">
                                    {{ number_format($achievement['count']) }}
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            @if ($user->isChef() && $chefOverview)
                <section class="mt-8 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">نظرة سريعة على مأكولاتك</h2>
                            <p class="mt-1 text-sm text-gray-500">
                                مؤشرات تساعدك على متابعة أداء وصفاتك وورشاتك.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ $chefOverview['dashboard_url'] }}" class="inline-flex items-center gap-2 rounded-xl border border-orange-200 bg-white px-4 py-2 text-sm font-medium text-orange-600 hover:border-orange-300 hover:bg-orange-50">
                                <i class="fas fa-th-large text-xs"></i>
                                لوحة التحكم
                            </a>
                            <a href="{{ $chefOverview['links_url'] }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:border-gray-300 hover:bg-gray-50">
                                <i class="fas fa-link text-xs"></i>
                                روابط Wasfah
                            </a>
                            @if ($chefOverview['public_profile_url'])
                                <a href="{{ $chefOverview['public_profile_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600">
                                    <i class="fas fa-eye text-xs"></i>
                                    عرض الصفحة العامة
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-orange-100 bg-orange-50/40 p-4">
                            <p class="text-sm text-gray-500">عدد الوصفات المنشورة</p>
                            <p class="mt-2 text-3xl font-extrabold text-gray-800">{{ number_format($chefOverview['public_recipes']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-orange-100 bg-orange-50/40 p-4">
                            <p class="text-sm text-gray-500">الوصفات الحصرية</p>
                            <p class="mt-2 text-3xl font-extrabold text-gray-800">{{ number_format($chefOverview['exclusive_recipes']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-orange-100 bg-orange-50/40 p-4">
                            <p class="text-sm text-gray-500">إجمالي مرات الحفظ</p>
                            <p class="mt-2 text-3xl font-extrabold text-gray-800">{{ number_format($chefOverview['total_saves']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-orange-100 bg-orange-50/40 p-4">
                            <p class="text-sm text-gray-500">متوسط التقييم</p>
                            <p class="mt-2 text-3xl font-extrabold text-gray-800">
                                {{ $chefOverview['average_rating'] ? number_format($chefOverview['average_rating'], 1) : '—' }}
                            </p>
                        </div>
                    </div>
                    @if (!empty($chefOverview['popular_recipes']))
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-800">أكثر الوصفات شعبية</h3>
                            <div class="mt-4 grid gap-4 md:grid-cols-3">
                                @foreach ($chefOverview['popular_recipes'] as $recipe)
                                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                                        <h4 class="text-md font-semibold text-gray-800">{{ $recipe->title }}</h4>
                                        <div class="mt-3 flex flex-wrap gap-3 text-sm text-gray-600">
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fas fa-bookmark ml-1 text-orange-500"></i>
                                                <span>{{ number_format($recipe->saved_count) }}</span>
                                                <span>حفظ</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fas fa-utensils ml-1 text-orange-500"></i>
                                                <span>{{ number_format($recipe->made_count) }}</span>
                                                <span>تجربة</span>
                                            </span>
                                            <span><i class="fas fa-star ml-1 text-orange-500"></i> {{ number_format($recipe->interactions_avg_rating ?? 0, 1) }} ({{ number_format($recipe->rating_count ?? 0) }})</span>
                                        </div>
                                        <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-orange-600 hover:text-orange-700">
                                            مشاهدة الوصفة
                                            <i class="fas fa-arrow-left text-xs"></i>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            <section id="profile-settings" class="mt-10 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">تعديل بيانات الملف الشخصي</h2>
                        <p class="text-sm text-gray-500">
                            عدّل معلومات التواصل وصورة الملف بسهولة، وسيتم حفظها فوراً.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 grid gap-6 md:grid-cols-2">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label for="name" class="text-sm font-semibold text-gray-700">الاسم الكامل</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-800 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100"
                            required
                        >
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="text-sm font-semibold text-gray-700">رقم الجوال</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-800 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100"
                            placeholder="+966..."
                        >
                        @error('phone')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label for="avatar" class="text-sm font-semibold text-gray-700">صورة الملف</label>
                        <input
                            type="file"
                            id="avatar"
                            name="avatar"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-gray-700 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-100"
                            accept="image/*"
                            data-max-size="5120"
                            data-max-size-message="لا يمكن رفع صورة أكبر من 5 ميجابايت."
                            data-error-target="#profile_avatar_error"
                        >
                        <p class="text-xs text-gray-500">
                            يدعم الصور حتى 5 ميجابايت. اختر صورة واضحة تمثل أسلوبك.
                        </p>
                        <p id="profile_avatar_error" class="text-xs text-red-600 hidden"></p>
                        @error('avatar')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-orange-600"
                        >
                            <i class="fas fa-save text-xs"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
