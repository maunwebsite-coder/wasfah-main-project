@extends('layouts.app')

@section('title', 'ورش الشيف الأونلاين')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
<div class="min-h-screen bg-gradient-to-b from-orange-50/50 to-white py-10">
    <div class="container mx-auto px-4">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">منطقة الشيف</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-900">ورش العمل الخاصة بي</h1>
                <p class="mt-2 text-sm text-slate-600">أنشئ جلسات أونلاين بسهولة، وشارك رابط Jitsi مع المشاركين بعد الحجز.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('chef.workshops.create') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-white shadow hover:from-orange-600 hover:to-orange-700">
                    <i class="fas fa-plus"></i>
                    إضافة ورشة جديدة
                </a>
                <a href="{{ route('chef.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-slate-600 shadow-sm hover:border-slate-300 hover:text-slate-800">
                    <i class="fas fa-arrow-right"></i>
                    العودة للوصفات
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-800 shadow">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mb-8 grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">إجمالي الورش</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">ورش مفعلة</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $stats['active'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">ورش أونلاين</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $stats['online'] }}</p>
            </div>
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">مسودات</p>
                <p class="mt-2 text-3xl font-bold text-orange-600">{{ $stats['drafts'] }}</p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white shadow-sm">
            @forelse ($workshops as $workshop)
                @php
                    $coverImage = $workshop->image
                        ? (Str::startsWith($workshop->image, ['http://', 'https://'])
                            ? $workshop->image
                            : Storage::disk('public')->url($workshop->image))
                        : null;
                @endphp
                <div class="border-b border-slate-100 p-5 last:border-b-0">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex flex-1 items-start gap-4">
                            <div class="h-16 w-20 overflow-hidden rounded-2xl bg-slate-100 shadow-inner">
                                @if ($coverImage)
                                    <img src="{{ $coverImage }}"
                                         alt="{{ $workshop->title }}"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-slate-400">
                                        <i class="fas fa-camera text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-orange-500">{{ $workshop->category }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $workshop->is_online ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-600' }}">
                                        <i class="fas {{ $workshop->is_online ? 'fa-video' : 'fa-map-marker-alt' }}"></i>
                                        {{ $workshop->is_online ? 'أونلاين' : 'حضوري' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $workshop->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                        <i class="fas {{ $workshop->is_active ? 'fa-circle-check' : 'fa-pause' }}"></i>
                                        {{ $workshop->is_active ? 'منشورة' : 'مسودة' }}
                                    </span>
                                </div>
                                <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $workshop->title }}</h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ optional($workshop->start_date)->locale('ar')->translatedFormat('d F Y • h:i a') }}
                                    • لمدة {{ $workshop->duration }} دقيقة
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-slate-500">
                                    <span><i class="fas fa-users text-slate-400"></i> {{ $workshop->confirmed_bookings ?? 0 }} / {{ $workshop->max_participants }} مشارك</span>
                                    @if ($workshop->price)
                                        <span><i class="fas fa-tag text-slate-400"></i> {{ number_format($workshop->price, 2) }} {{ $workshop->currency }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-start gap-3 md:w-64">
                            @if ($workshop->is_online)
                                <div class="w-full rounded-2xl bg-slate-50 p-3 text-xs text-slate-600">
                                    <p class="font-semibold text-slate-800">رابط اللقاء:</p>
                                    @if ($workshop->meeting_link)
                                        <div class="mt-1 flex items-center gap-2">
                                            <code class="truncate rounded-xl bg-white px-2 py-1">{{ $workshop->meeting_link }}</code>
                                            <button type="button" class="copy-link-btn text-slate-500 hover:text-slate-800"
                                                    data-link="{{ $workshop->meeting_link }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    @else
                                        <p class="mt-1 text-orange-500">سيتم توليد رابط بعد الحفظ.</p>
                                    @endif
                                    @if ($workshop->jitsi_passcode)
                                        <p class="mt-1 text-xs text-slate-500">رمز الدخول: {{ $workshop->jitsi_passcode }}</p>
                                    @endif
                                </div>
                            @endif
                            <div class="flex w-full flex-wrap items-center gap-2">
                                <a href="{{ route('chef.workshops.edit', $workshop) }}"
                                   class="flex-1 rounded-2xl border border-slate-200 px-3 py-2 text-center text-sm font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-900">
                                    تعديل
                                </a>
                                <form action="{{ route('chef.workshops.destroy', $workshop) }}" method="POST" class="flex-1"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الورشة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full rounded-2xl border border-red-200 px-3 py-2 text-center text-sm font-semibold text-red-600 hover:bg-red-50">
                                        حذف
                                    </button>
                                </form>
                            </div>
                            @if ($workshop->is_active)
                                <a href="{{ route('workshop.show', $workshop) }}" target="_blank"
                                   class="w-full rounded-2xl bg-slate-900 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                    عرض صفحة الورشة
                                </a>
                            @else
                                <div class="w-full rounded-2xl border border-dashed border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-400">
                                    سيتم عرض الرابط بعد تفعيل الورشة
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <p class="text-lg font-semibold text-slate-800">لا توجد ورش بعد</p>
                    <p class="mt-2 text-sm text-slate-500">ابدأ أول ورشة لك الآن، وسنقوم بتوليد رابط Jitsi فوراً.</p>
                    <a href="{{ route('chef.workshops.create') }}"
                       class="mt-4 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-white shadow hover:from-orange-600 hover:to-orange-700">
                        <i class="fas fa-plus"></i>
                        إنشاء ورشة
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $workshops->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('click', async (event) => {
        const target = event.target.closest('.copy-link-btn');
        if (!target) return;
        event.preventDefault();
        const link = target.dataset.link;
        if (!link) return;

        try {
            await navigator.clipboard.writeText(link);
            target.classList.add('text-emerald-600');
            target.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                target.classList.remove('text-emerald-600');
                target.innerHTML = '<i class="fas fa-copy"></i>';
            }, 1500);
        } catch (error) {
            alert('تعذر نسخ الرابط، حاول مرة أخرى.');
        }
    });
</script>
@endpush
