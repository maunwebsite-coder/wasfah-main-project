@extends('layouts.app')

@section('title', 'حجوزاتي - موقع وصفة')

@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

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

    $cardBackgroundClasses = [
        'pending' => 'border-amber-100 bg-gradient-to-br from-amber-50 via-white to-white shadow-amber-100/50',
        'confirmed' => 'border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-white shadow-emerald-100/50',
        'cancelled' => 'border-rose-100 bg-gradient-to-br from-rose-50 via-white to-white shadow-rose-100/50',
    ];

    $accentGradientClasses = [
        'pending' => 'from-amber-400/80 via-amber-300/70 to-transparent',
        'confirmed' => 'from-emerald-400/80 via-emerald-300/70 to-transparent',
        'cancelled' => 'from-rose-400/80 via-rose-300/70 to-transparent',
    ];
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="container mx-auto px-4">
            <div class="rounded-3xl border border-orange-100 bg-gradient-to-br from-orange-50 via-white to-white p-6 md:p-8 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-32 h-32 bg-orange-200/25 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-orange-100/30 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>

                <div class="relative flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800">حجوزاتي</h1>
                        <p class="mt-2 text-sm md:text-base text-gray-600">
                            كل ورش العمل التي حجزتها عبر وصفة في مكان واحد. يمكنك متابعة حالة الحجز والدخول إلى غرفة الورشة عند التأكيد.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('workshops') }}" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-orange-600">
                            <i class="fas fa-search text-xs"></i>
                            استكشاف ورش جديدة
                        </a>
                        <a href="{{ route('profile') }}" class="inline-flex items-center gap-2 rounded-xl border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700">
                            <i class="fas fa-user-circle text-xs"></i>
                            العودة للملف الشخصي
                        </a>
                    </div>
                </div>
            </div>

            <section class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">إجمالي الحجوزات</p>
                    <p class="mt-3 text-3xl font-extrabold text-gray-800">{{ number_format($bookings->total()) }}</p>
                </article>
                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">حجوزات مؤكدة</p>
                    <p class="mt-3 text-3xl font-extrabold text-gray-800">
                        {{ number_format($bookings->getCollection()->where('status', 'confirmed')->count()) }}
                    </p>
                </article>
                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">قيد المراجعة</p>
                    <p class="mt-3 text-3xl font-extrabold text-gray-800">
                        {{ number_format($bookings->getCollection()->where('status', 'pending')->count()) }}
                    </p>
                </article>
                <article class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">آخر تحديث</p>
                    <p class="mt-3 text-xl font-semibold text-gray-800">
                        {{ Carbon::now()->locale('ar')->translatedFormat('d F Y • h:i a') }}
                    </p>
                </article>
            </section>

            <section class="mt-8 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">سجل الحجوزات</h2>
                        <p class="text-sm text-gray-500">
                            يتم ترتيب الحجوزات من الأحدث إلى الأقدم. استخدم زر التفاصيل لمعرفة كل المعلومات أو إدارة الحجز.
                        </p>
                    </div>
                    <div class="text-sm text-gray-500">
                        العرض الحالي {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} من إجمالي {{ $bookings->total() }}
                    </div>
                </div>

                @if ($bookings->count())
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($bookings as $booking)
                            @php
                                $workshop = $booking->workshop;
                                $start = optional($workshop?->start_date);
                                $status = $booking->status;
                                $cardBackground = $cardBackgroundClasses[$status] ?? 'border-gray-100 bg-white';
                                $accentGradient = $accentGradientClasses[$status] ?? 'from-gray-200 via-gray-100 to-transparent';
                            @endphp
                            <article class="group relative flex h-full flex-col rounded-2xl border {{ $cardBackground }} p-5 text-right shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">الورشة</p>
                                        <h3 class="mt-1 text-lg font-bold text-gray-900">
                                            {{ $workshop?->title ?? 'ورشة بدون عنوان' }}
                                        </h3>
                                        <p class="mt-1 text-xs text-gray-500">
                                            رقم الحجز: {{ strtoupper(Str::slug($booking->public_code ?? $booking->id)) }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-600' }}">
                                        <i class="fas fa-circle text-[6px]"></i>
                                        {{ $statusLabels[$status] ?? $status }}
                                    </span>
                                </div>

                                <dl class="mt-4 grid grid-cols-1 gap-4 text-sm text-gray-600 sm:grid-cols-2">
                                    <div class="rounded-xl border border-white/60 bg-white/40 px-4 py-3">
                                        <dt class="text-xs font-semibold text-gray-400">التاريخ</dt>
                                        <dd class="mt-1 font-semibold text-gray-800">
                                            {{ $start ? $start->locale('ar')->translatedFormat('d F Y • h:i a') : '—' }}
                                        </dd>
                                    </div>
                                    <div class="rounded-xl border border-white/60 bg-white/40 px-4 py-3">
                                        <dt class="text-xs font-semibold text-gray-400">طريقة الحضور</dt>
                                        <dd class="mt-1 font-semibold text-gray-800">
                                            {{ $workshop?->is_online ? 'أونلاين' : 'حضوري' }}
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-5 flex flex-wrap gap-3 text-sm font-semibold">
                                    <a href="{{ route('bookings.show', $booking) }}" class="inline-flex items-center gap-2 rounded-xl border border-orange-200 bg-white px-4 py-2 text-orange-600 transition hover:border-orange-300 hover:bg-orange-50">
                                        <i class="fas fa-info-circle text-xs"></i>
                                        التفاصيل
                                    </a>
                                    @if ($status === 'confirmed' && $workshop?->is_online)
                                        <a href="{{ $booking->secure_join_url }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2 text-white shadow-sm transition hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1 focus:ring-offset-white">
                                            <i class="fas fa-door-open text-xs"></i>
                                            دخول الورشة
                                        </a>
                                    @endif
                                </div>

                                <div class="pointer-events-none absolute inset-x-4 bottom-2 h-1 rounded-full bg-gradient-to-r {{ $accentGradient }} opacity-80"></div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $bookings->links() }}
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-gray-500">
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
        </div>
    </div>
@endsection
