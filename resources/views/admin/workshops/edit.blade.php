﻿@extends('layouts.app')

@section('title', 'تعديل الورشة - ' . $workshop->title)

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 40%, #fff7ed 100%);
        min-height: 100vh;
    }

    .notification {
        position: fixed;
        top: 24px;
        right: 24px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.85rem 1.35rem;
        border-radius: 0.9rem;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 18px 30px -18px rgba(15, 23, 42, 0.25);
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 1200;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .notification.error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .notification.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .image-upload-area.has-image {
        border-color: rgba(16, 185, 129, 0.45);
        background-color: #ecfdf5;
    }

    .recipe-item.selected {
        border-color: rgba(249, 115, 22, 0.65);
        background-color: #fff7ed;
        box-shadow: 0 20px 35px -25px rgba(249, 115, 22, 0.6);
    }
</style>
@endpush

@section('content')
<div class="py-10 md:py-16">
    <div class="container mx-auto px-4 max-w-6xl space-y-10">
        @php
            $currencyLabels = [
                'JOD' => 'دينار أردني',
                'AED' => 'درهم إماراتي',
            ];
            $priceFormatted = number_format($workshop->price, 2);
        @endphp

        <div class="relative overflow-hidden rounded-3xl border border-orange-100 bg-gradient-to-br from-white via-orange-50/70 to-sky-50/70 shadow-2xl p-8 md:p-10">
            <div class="absolute -top-24 -left-12 h-48 w-48 rounded-full bg-sky-200/40 blur-3xl"></div>
            <div class="absolute -bottom-32 -right-20 h-56 w-56 rounded-full bg-orange-200/50 blur-3xl"></div>

            <div class="relative z-10 flex flex-col lg:flex-row gap-10">
                <div class="flex-1 space-y-6">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/70 text-orange-600 font-semibold text-xs tracking-widest shadow-sm uppercase">
                        <i class="fas fa-chalkboard-teacher"></i>
                        لوحة تعديل الورشة
                    </span>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-900">
                            تعديل ورشة: {{ $workshop->title }}
                        </h1>
                        <p class="mt-4 text-slate-600 leading-relaxed max-w-3xl">
                            تصميم جديد منظم يضع جميع الحقول في أقسام واضحة، مع لمسات مرئية خفيفة تساعدك على متابعة حالة الورشة وتحديث تفاصيلها بثقة.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-emerald-600 shadow-sm border border-emerald-100">
                            <i class="fas {{ $workshop->is_active ? 'fa-bolt text-emerald-500' : 'fa-pause text-red-400' }}"></i>
                            {{ $workshop->is_active ? 'ورشة نشطة' : 'ورشة غير مفعّلة' }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-sm font-semibold text-sky-600 shadow-sm border border-sky-100">
                            <i class="fas {{ $workshop->is_online ? 'fa-video text-sky-500' : 'fa-map-marker-alt text-orange-500' }}"></i>
                            {{ $workshop->is_online ? 'أونلاين' : 'حضورية' }}
                        </span>
                        @if($workshop->is_featured)
                            <span class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-4 py-2 text-sm font-semibold text-amber-700 shadow-sm border border-amber-200">
                                <i class="fas fa-crown text-amber-500"></i>
                                الورشة المميزة الحالية
                            </span>
                        @endif
                    </div>
                </div>

                <div class="w-full lg:w-72 space-y-4">
                    <div class="rounded-2xl border border-white/70 bg-white/80 backdrop-blur shadow-md p-5">
                        <div class="text-xs font-semibold tracking-widest text-slate-400 uppercase">آخر تحديث</div>
                        <div class="mt-2 text-lg font-bold text-slate-900">
                            {{ optional($workshop->updated_at)->diffForHumans() ?? 'غير متاح' }}
                        </div>
                        <div class="mt-2 text-sm text-slate-500">
                            تم إنشاء الورشة في {{ optional($workshop->created_at)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 backdrop-blur shadow-md p-5">
                        <div class="text-xs font-semibold tracking-widest text-slate-400 uppercase">المقاعد المتاحة</div>
                        <div class="mt-2 text-2xl font-black text-orange-500">
                            {{ $workshop->max_participants }}
                        </div>
                        <div class="mt-2 text-sm text-slate-500">
                            الحد الأقصى لعدد المشاركين في هذه الورشة
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl bg-white/85 border border-white/70 shadow-md p-5">
                    <div class="text-xs font-semibold tracking-widest text-slate-400 uppercase">تاريخ البداية</div>
                    <div class="mt-2 text-sm font-bold text-slate-800">
                        {{ optional($workshop->start_date)->format('d M Y، H:i') ?? 'غير محدد' }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">بداية الجلسة الأولى</div>
                </div>
                <div class="rounded-2xl bg-white/85 border border-white/70 shadow-md p-5">
                    <div class="text-xs font-semibold tracking-widest text-slate-400 uppercase">تاريخ النهاية</div>
                    <div class="mt-2 text-sm font-bold text-slate-800">
                        {{ optional($workshop->end_date)->format('d M Y، H:i') ?? 'غير محدد' }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">الختام المتوقع</div>
                </div>
                <div class="rounded-2xl bg-white/85 border border-white/70 shadow-md p-5">
                    <div class="text-xs font-semibold tracking-widest text-slate-400 uppercase">التكلفة</div>
                    <div class="mt-2 text-sm font-bold text-slate-800">
                        {{ $priceFormatted }} {{ $currencyLabels[$workshop->currency] ?? $workshop->currency }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">يشمل رسوم المشاركة بالكامل</div>
                </div>
                <div class="rounded-2xl bg-white/85 border border-white/70 shadow-md p-5">
                    <div class="text-xs font-semibold tracking-widest text-slate-400 uppercase">مدة الورشة</div>
                    <div class="mt-2 text-sm font-bold text-slate-800">
                        {{ $workshop->duration }} دقيقة
                    </div>
                    <div class="mt-1 text-xs text-slate-500">الوقت الإجمالي المتوقع</div>
                </div>
            </div>
        </div>
        <div class="rounded-[34px] border border-slate-100 bg-white/90 shadow-2xl backdrop-blur p-6 md:p-10 space-y-12">
            <form id="workshop-form" action="{{ route('admin.workshops.update', $workshop->id) }}" method="POST" enctype="multipart/form-data" class="space-y-12">
                @csrf
                @method('PUT')

                <section class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500 to-amber-400 text-white shadow-md">
                            <i class="fas fa-pen-nib"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">البيانات الأساسية</h2>
                            <p class="text-sm text-slate-500 mt-2">
                                حدّث عنوان الورشة، المدرب والفئة ليبقى المحتوى واضحاً ومطابقاً لهوية الورشة.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">عنوان الورشة *</label>
                            <input id="title" name="title" type="text" value="{{ old('title', $workshop->title) }}" required
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-orange-400 focus:ring-4 focus:ring-orange-200 @error('title') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                   placeholder="مثال: تعلم صنع الكيك الفرنسي">
                            @error('title')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="instructor" class="block text-sm font-semibold text-slate-700 mb-2">اسم المدرب *</label>
                            <input id="instructor" name="instructor" type="text" value="{{ old('instructor', $workshop->instructor) }}" required
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-orange-400 focus:ring-4 focus:ring-orange-200 @error('instructor') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                   placeholder="مثال: الشيف أحمد محمد">
                            @error('instructor')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-semibold text-slate-700 mb-2">فئة الورشة *</label>
                            <select id="category" name="category" required
                                    class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-orange-400 focus:ring-4 focus:ring-orange-200 @error('category') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                <option value="">اختر الفئة</option>
                                <option value="cooking" {{ old('category', $workshop->category) == 'cooking' ? 'selected' : '' }}>طبخ</option>
                                <option value="baking" {{ old('category', $workshop->category) == 'baking' ? 'selected' : '' }}>خبز</option>
                                <option value="desserts" {{ old('category', $workshop->category) == 'desserts' ? 'selected' : '' }}>حلويات</option>
                                <option value="beverages" {{ old('category', $workshop->category) == 'beverages' ? 'selected' : '' }}>مشروبات</option>
                                <option value="other" {{ old('category', $workshop->category) == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('category')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="level" class="block text-sm font-semibold text-slate-700 mb-2">مستوى الورشة *</label>
                            <select id="level" name="level" required
                                    class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-orange-400 focus:ring-4 focus:ring-orange-200 @error('level') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                <option value="">اختر المستوى</option>
                                <option value="beginner" {{ old('level', $workshop->level) == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                <option value="intermediate" {{ old('level', $workshop->level) == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                <option value="advanced" {{ old('level', $workshop->level) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                            </select>
                            @error('level')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-500 text-white shadow-md">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">الجدول والتسعير</h2>
                            <p class="text-sm text-slate-500 mt-2">
                                اضبط المواعيد، عدد المقاعد والسعر لضمان تجربة حجز واضحة وسلسة للمشاركين.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البداية *</label>
                            <input id="start_date" name="start_date" type="datetime-local" value="{{ old('start_date', optional($workshop->start_date)->format('Y-m-d\TH:i')) }}" required
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-sky-400 focus:ring-4 focus:ring-sky-200 @error('start_date') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                            @error('start_date')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-slate-700 mb-2">تاريخ النهاية *</label>
                            <input id="end_date" name="end_date" type="datetime-local" value="{{ old('end_date', optional($workshop->end_date)->format('Y-m-d\TH:i')) }}" required
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-sky-400 focus:ring-4 focus:ring-sky-200 @error('end_date') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                            @error('end_date')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-semibold text-slate-700 mb-2">مدة الورشة (بالدقائق) *</label>
                            <input id="duration" name="duration" type="number" min="1" value="{{ old('duration', $workshop->duration) }}" required
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-sky-400 focus:ring-4 focus:ring-sky-200 @error('duration') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                   placeholder="120">
                            @error('duration')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_participants" class="block text-sm font-semibold text-slate-700 mb-2">العدد الأقصى للمشاركين *</label>
                            <input id="max_participants" name="max_participants" type="number" min="1" value="{{ old('max_participants', $workshop->max_participants) }}" required
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-sky-400 focus:ring-4 focus:ring-sky-200 @error('max_participants') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                   placeholder="20">
                            @error('max_participants')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-semibold text-slate-700 mb-2">السعر *</label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $workshop->price) }}" required
                                       class="flex-1 rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-sky-400 focus:ring-4 focus:ring-sky-200 @error('price') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                       placeholder="0.00">
                                <select name="currency"
                                        class="rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-sky-400 focus:ring-4 focus:ring-sky-200 @error('currency') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                    <option value="JOD" {{ old('currency', $workshop->currency) == 'JOD' ? 'selected' : '' }}>دينار أردني</option>
                                    <option value="AED" {{ old('currency', $workshop->currency) == 'AED' ? 'selected' : '' }}>درهم إماراتي</option>
                                </select>
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                            @error('currency')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_deadline" class="block text-sm font-semibold text-slate-700 mb-2">آخر موعد للتسجيل</label>
                            <input id="registration_deadline" name="registration_deadline" type="datetime-local"
                                   value="{{ old('registration_deadline', optional($workshop->registration_deadline)->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-sky-400 focus:ring-4 focus:ring-sky-200 @error('registration_deadline') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                            @error('registration_deadline')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-slate-500">
                                يساعد المشاركين على معرفة إمكانية التسجيل قبل الموعد النهائي.
                            </p>
                        </div>
                    </div>
                </section>
                <section class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-500 to-orange-400 text-white shadow-md">
                            <i class="fas fa-map-marked-alt"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">الموقع وطرق الوصول</h2>
                            <p class="text-sm text-slate-500 mt-2">
                                وضّح تفاصيل المكان أو رابط الاجتماع لضمان وصول المشاركين بسهولة إلى الورشة.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                        <label for="location" class="block text-sm font-semibold text-slate-700 mb-2">الموقع (للورش الحضورية)</label>
                        <input id="location" name="location" type="text" value="{{ old('location', $workshop->location) }}" required
                               class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-rose-400 focus:ring-4 focus:ring-rose-200 @error('location') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                               placeholder="مثال: عمان - شارع الملكة رانيا">
                        <p id="location-help" class="mt-2 text-xs text-slate-500">يجب تحديد الموقع عند اختيار ورشة حضورية.</p>
                        @error('location')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                        @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">العنوان التفصيلي</label>
                            <input id="address" name="address" type="text" value="{{ old('address', $workshop->address) }}"
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-rose-400 focus:ring-4 focus:ring-rose-200 @error('address') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                   placeholder="تفاصيل إضافية للموقع (إن وجدت)">
                            @error('address')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="meeting_link" class="block text-sm font-semibold text-slate-700 mb-2">رابط الاجتماع (للورش الأونلاين)</label>
                            <input id="meeting_link" name="meeting_link" type="url" value="{{ old('meeting_link', $workshop->meeting_link) }}"
                                   class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-rose-400 focus:ring-4 focus:ring-rose-200 @error('meeting_link') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                   placeholder="https://zoom.us/your-meeting-link">
                            <p id="meeting-link-help" class="mt-2 text-xs text-slate-500">سيظهر الرابط للمشاركين بعد تأكيد حجزهم في الورشة الأونلاين.</p>
                            @error('meeting_link')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-slate-500">
                                سيتم إرسال الرابط للمسجلين مباشرة عند إتمام الدفع إذا كانت الورشة أونلاين.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-400 text-white shadow-md">
                            <i class="fas fa-align-left"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">المحتوى والتجربة التعليمية</h2>
                            <p class="text-sm text-slate-500 mt-2">
                                شارك قصة الورشة، تفاصيل المحتوى، وما سيتعلمه المشاركون لتوضيح القيمة التي سيحصلون عليها.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6">
                        <div>
                            <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">وصف الورشة *</label>
                            <textarea id="description" name="description" rows="4" required
                                      class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-teal-400 focus:ring-4 focus:ring-teal-200 @error('description') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                      placeholder="اكتب وصفاً مختصراً جذاباً للورشة...">{{ old('description', $workshop->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-semibold text-slate-700 mb-2">محتوى الورشة التفصيلي</label>
                            <textarea id="content" name="content" rows="7"
                                      class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-teal-400 focus:ring-4 focus:ring-teal-200 @error('content') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                      placeholder="اكتب محتوى مفصلاً عن الورشة...">{{ old('content', $workshop->content) }}</textarea>
                            @error('content')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="what_you_will_learn" class="block text-sm font-semibold text-slate-700 mb-2">ما سيتعلمه المشاركون</label>
                                <textarea id="what_you_will_learn" name="what_you_will_learn" rows="4"
                                          class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-teal-400 focus:ring-4 focus:ring-teal-200 @error('what_you_will_learn') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                          placeholder="أبرز نقاط التعلم الرئيسية...">{{ old('what_you_will_learn', $workshop->what_you_will_learn) }}</textarea>
                                @error('what_you_will_learn')
                                    <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="requirements" class="block text-sm font-semibold text-slate-700 mb-2">متطلبات الورشة</label>
                                <textarea id="requirements" name="requirements" rows="4"
                                          class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-teal-400 focus:ring-4 focus:ring-teal-200 @error('requirements') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                          placeholder="أدخل المتطلبات المسبقة...">{{ old('requirements', $workshop->requirements) }}</textarea>
                                @error('requirements')
                                    <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="materials_needed" class="block text-sm font-semibold text-slate-700 mb-2">المواد المطلوبة</label>
                                <textarea id="materials_needed" name="materials_needed" rows="4"
                                          class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-teal-400 focus:ring-4 focus:ring-teal-200 @error('materials_needed') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                          placeholder="اذكر الأدوات أو المكونات المطلوبة...">{{ old('materials_needed', $workshop->materials_needed) }}</textarea>
                                @error('materials_needed')
                                    <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="instructor_bio" class="block text-sm font-semibold text-slate-700 mb-2">نبذة عن المدرب</label>
                                <textarea id="instructor_bio" name="instructor_bio" rows="4"
                                          class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-teal-400 focus:ring-4 focus:ring-teal-200 @error('instructor_bio') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                          placeholder="أضف ملخصاً عن خبرات المدرب...">{{ old('instructor_bio', $workshop->instructor_bio) }}</textarea>
                                @error('instructor_bio')
                                    <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="featured_description" class="block text-sm font-semibold text-slate-700 mb-2">وصف الورشة المميزة</label>
                            <textarea id="featured_description" name="featured_description" rows="3"
                                      class="w-full rounded-2xl border-2 border-slate-200 bg-white/80 px-4 py-3 text-slate-800 shadow-sm transition focus:border-teal-400 focus:ring-4 focus:ring-teal-200 @error('featured_description') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                                      placeholder="وصف مختصر يظهر في بطاقة الورشة المميزة (اختياري)">{{ old('featured_description', $workshop->featured_description) }}</textarea>
                            <p class="mt-2 text-xs text-slate-500">
                                يساعد على إبراز الورشة في الصفحة الرئيسية عند تمييزها.
                            </p>
                            @error('featured_description')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>
                <section class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 text-white shadow-md">
                            <i class="fas fa-image"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">الصورة الرئيسية</h2>
                            <p class="text-sm text-slate-500 mt-2">
                                استخدم صورة جذابة تمثل أجواء الورشة. يدعم النظام صورًا حتى 5 ميجابايت مع ضغط تلقائي للحفاظ على الجودة.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @if($workshop->image)
                            <div class="flex flex-col sm:flex-row items-start gap-4 rounded-3xl border border-slate-200 bg-white/70 p-4 sm:p-6 shadow-sm current-image-block">
                                <img id="current-image-preview"
                                     src="{{ asset('storage/' . $workshop->image) }}?v={{ time() }}"
                                     alt="{{ $workshop->title }}"
                                     class="h-28 w-28 rounded-2xl object-cover border border-slate-200"
                                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                <div class="space-y-2">
                                    <p class="text-sm text-slate-600">
                                        هذه هي الصورة الحالية المعروضة للزوار. يمكنك استبدالها أو حذفها تماماً.
                                    </p>
                                    <button type="button" id="remove-current-image"
                                            class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:-translate-y-0.5 hover:bg-red-100">
                                        <i class="fas fa-trash-alt"></i>
                                        حذف الصورة الحالية
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div id="image-upload-area"
                             class="image-upload-area rounded-3xl border-2 border-dashed border-slate-300 bg-white/70 px-6 py-10 text-center transition hover:-translate-y-1 hover:border-purple-400 hover:bg-purple-50 cursor-pointer">
                            <div class="flex flex-col items-center gap-3 text-slate-600">
                                <i class="fas fa-cloud-upload-alt text-3xl text-purple-400"></i>
                                <h3 class="text-lg font-semibold text-slate-800">اسحب وأفلت الصورة هنا</h3>
                                <p class="text-sm text-slate-500">أو انقر للاختيار من جهازك (JPEG، PNG، GIF، WebP حتى 5MB)</p>
                                <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-rose-500 px-4 py-2 text-sm font-semibold text-white shadow-md">
                                    <i class="fas fa-folder-open"></i>
                                    اختر صورة
                                </span>
                            </div>
                        </div>

                        <input id="image" name="image" type="file" accept="image/*" class="hidden" onchange="handleImageUpload(this)">

                        <div id="image-preview" class="hidden">
                            <div class="relative inline-block">
                                <img id="preview-img" src="#" alt="معاينة الصورة الجديدة" class="h-36 w-36 rounded-2xl border-2 border-purple-200 object-cover shadow-lg">
                                <button type="button" id="remove-preview"
                                        class="absolute -top-3 -left-3 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-lg"
                                        onclick="removeImagePreview()">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                            <p id="image-info" class="mt-2 text-xs text-slate-500"></p>
                        </div>

                        @error('image')
                            <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                <section class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-md">
                            <i class="fas fa-utensils"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">الوصفات المرتبطة</h2>
                            <p class="text-sm text-slate-500 mt-2">
                                اختر الوصفات التي ستتم تغطيتها داخل الورشة لتظهر تلقائياً في صفحة تفاصيل الورشة وتلهم المشاركين.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-3xl border border-orange-100 bg-white/80 p-6 md:p-8 shadow-inner">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/80 px-4 py-2 text-slate-600 w-full md:w-80">
                                <i class="fas fa-search text-slate-400"></i>
                                <input id="recipe-search" type="text" placeholder="البحث في الوصفات..."
                                       class="w-full bg-transparent text-sm focus:outline-none">
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <button type="button" id="select-all-recipes"
                                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5">
                                    <i class="fas fa-check-double"></i>
                                    اختيار الكل
                                </button>
                                <button type="button" id="clear-selection"
                                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:-translate-y-0.5">
                                    <i class="fas fa-eraser"></i>
                                    إلغاء الكل
                                </button>
                            </div>
                        </div>

                        <div id="recipes-container" class="grid max-h-[28rem] gap-4 overflow-y-auto pr-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($recipes as $recipe)
                                @php
                                    $isSelected = in_array($recipe->recipe_id, old('recipe_ids', $workshop->recipes->pluck('recipe_id')->toArray()));
                                @endphp
                                <div class="recipe-item {{ $isSelected ? 'selected' : '' }} rounded-2xl border border-slate-200 bg-white/80 p-4 transition hover:-translate-y-1 hover:border-orange-300"
                                     data-recipe-id="{{ $recipe->recipe_id }}"
                                     data-recipe-title="{{ strtolower($recipe->title) }}">
                                    <label class="flex cursor-pointer items-start gap-3">
                                        <input type="checkbox"
                                               name="recipe_ids[]"
                                               value="{{ $recipe->recipe_id }}"
                                               {{ $isSelected ? 'checked' : '' }}
                                               class="recipe-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                                        <div class="flex-1 space-y-3">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $recipe->image_url ?: 'https://placehold.co/60x60/f87171/FFFFFF?text=وصفة' }}"
                                                     alt="{{ $recipe->title }}"
                                                     class="h-14 w-14 rounded-xl border border-slate-200 object-cover"
                                                     onerror="this.src='{{ asset('image/logo.png') }}';">
                                                <div>
                                                    <h4 class="text-sm font-semibold text-slate-900 leading-snug">{{ $recipe->title }}</h4>
                                                    <p class="text-xs text-slate-500">{{ $recipe->author }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between text-xs text-slate-500">
                                                <span><i class="fas fa-clock ml-1 text-slate-400"></i>{{ ($recipe->prep_time ?? 0) + ($recipe->cook_time ?? 0) }} دقيقة</span>
                                                <span><i class="fas fa-users ml-1 text-slate-400"></i>{{ $recipe->servings ?? 0 }} حصة</span>
                                                <span class="rounded-full bg-orange-100 px-2 py-1 text-orange-600">
                                                    {{ $recipe->difficulty === 'easy' ? 'سهل' : ($recipe->difficulty === 'medium' ? 'متوسط' : 'صعب') }}
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @if($recipes->count() === 0)
                            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white/70 py-12 text-slate-500">
                                <i class="fas fa-utensils text-3xl mb-3 text-slate-400"></i>
                                <p>لا توجد وصفات متاحة حالياً.</p>
                            </div>
                        @endif
                    </div>
                </section>
                <section class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-600 to-slate-800 text-white shadow-md">
                            <i class="fas fa-toggle-on"></i>
                        </span>
                        <div>
                            <h2 class="text-xl font-black text-slate-900">إعدادات الحالة والتحكم</h2>
                            <p class="text-sm text-slate-500 mt-2">
                                تحكم في تفعيل الورشة، كونها أونلاين، أو تمييزها كأقرب ورشة قادمة على الصفحة الرئيسية.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="relative block rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <input type="checkbox" name="is_online" value="1" {{ old('is_online', $workshop->is_online) ? 'checked' : '' }} class="peer sr-only">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-indigo-500 text-white shadow-md peer-checked:from-sky-600 peer-checked:to-indigo-600">
                                    <i class="fas fa-wifi"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">ورشة أونلاين</p>
                                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                        فعّل هذا الخيار إذا كانت الجلسات تعقد عبر الإنترنت وسيُرسل الرابط للمشاركين تلقائياً.
                                    </p>
                                </div>
                            </div>
                            <div class="absolute inset-0 rounded-2xl ring-2 ring-transparent peer-checked:ring-sky-300"></div>
                        </label>

                        <label class="relative block rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $workshop->is_active) ? 'checked' : '' }} class="peer sr-only">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white shadow-md peer-checked:from-emerald-600 peer-checked:to-teal-600">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">تفعيل الورشة</p>
                                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                        عند تفعيل الورشة ستظهر في صفحة الورش ويمكن للمستخدمين الحجز فوراً.
                                    </p>
                                </div>
                            </div>
                            <div class="absolute inset-0 rounded-2xl ring-2 ring-transparent peer-checked:ring-emerald-300"></div>
                        </label>

                        <label class="relative block rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $workshop->is_featured) ? 'checked' : '' }} class="peer sr-only">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-md peer-checked:from-amber-600 peer-checked:to-orange-600">
                                    <i class="fas fa-crown"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">ورشة مميزة</p>
                                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                        تظهر الورشة في مساحة متقدمة على الصفحة الرئيسية. اختيارها سيُلغي تمييز أي ورشة أخرى حالية.
                                    </p>
                                </div>
                            </div>
                            <div class="absolute inset-0 rounded-2xl ring-2 ring-transparent peer-checked:ring-amber-300"></div>
                        </label>
                    </div>
                </section>

                <div class="flex flex-col gap-4 border-t border-slate-100 pt-6 md:flex-row md:justify-end">
                    <a href="{{ route('admin.workshops.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <i class="fas fa-arrow-right"></i>
                        إلغاء
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 via-amber-500 to-rose-500 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-orange-200">
                        <i class="fas fa-save"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function showNotification(message, type = 'success') {
    document.querySelectorAll('.notification').forEach(el => el.remove());

    const icon = type === 'error' ? 'fa-exclamation-triangle' : (type === 'warning' ? 'fa-info-circle' : 'fa-check');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;

    document.body.appendChild(notification);
    requestAnimationFrame(() => notification.classList.add('show'));

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3200);
}

function handleImageUpload(input) {
    const file = input.files[0];
    if (!file) {
        return;
    }

    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('نوع الملف غير مدعوم. يرجى اختيار صورة JPG أو PNG أو GIF أو WebP', 'error');
        input.value = '';
        return;
    }

    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        showNotification('حجم الصورة يجب أن يكون أقل من 5 ميجابايت', 'error');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = event => {
        const previewImg = document.getElementById('preview-img');
        const imagePreview = document.getElementById('image-preview');
        const imageInfo = document.getElementById('image-info');
        const imageUploadArea = document.getElementById('image-upload-area');

        previewImg.src = event.target.result;
        imagePreview.classList.remove('hidden');
        imageUploadArea.classList.add('has-image');

        const currentImageBlock = document.querySelector('.current-image-block');
        if (currentImageBlock) {
            currentImageBlock.style.display = 'none';
        }

        const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
        imageInfo.textContent = `الملف: ${file.name} (${fileSizeMB} MB)`;

        showNotification('تم تحميل الصورة بنجاح', 'success');
    };

    reader.readAsDataURL(file);
}

function removeImagePreview() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const imageUploadArea = document.getElementById('image-upload-area');
    const currentImageBlock = document.querySelector('.current-image-block');

    imageInput.value = '';
    imagePreview.classList.add('hidden');
    imageUploadArea.classList.remove('has-image');

    if (currentImageBlock) {
        currentImageBlock.style.display = '';
    }

    showNotification('تم حذف الصورة المحددة', 'success');
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('workshop-form');
    if (!form) {
        return;
    }

    const featuredCheckbox = form.querySelector('input[name="is_featured"]');
    const imageInput = document.getElementById('image');
    const imageUploadArea = document.getElementById('image-upload-area');
    const removeCurrentImageBtn = document.getElementById('remove-current-image');
    const isOnlineCheckbox = form.querySelector('input[name="is_online"]');
    const locationInput = document.getElementById('location');
    const meetingLinkInput = document.getElementById('meeting_link');
    const locationHelp = document.getElementById('location-help');
    const meetingLinkHelp = document.getElementById('meeting-link-help');

    if (imageUploadArea) {
        ['dragenter', 'dragover'].forEach(evt => {
            imageUploadArea.addEventListener(evt, event => {
                event.preventDefault();
                imageUploadArea.classList.add('has-image');
            });
        });

        ['dragleave', 'dragend', 'drop'].forEach(evt => {
            imageUploadArea.addEventListener(evt, event => {
                event.preventDefault();
                imageUploadArea.classList.remove('has-image');
            });
        });

        imageUploadArea.addEventListener('drop', event => {
            const files = event.dataTransfer.files;
            if (files && files.length > 0) {
                imageInput.files = files;
                handleImageUpload(imageInput);
            }
        });

        imageUploadArea.addEventListener('click', () => {
            imageInput.click();
        });
    }

    if (removeCurrentImageBtn) {
        removeCurrentImageBtn.addEventListener('click', () => {
            if (confirm('هل أنت متأكد من حذف الصورة الحالية؟')) {
                const currentImageBlock = document.querySelector('.current-image-block');
                if (currentImageBlock) {
                    currentImageBlock.style.display = 'none';
                }

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'remove_image';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);

                showNotification('تم حذف الصورة الحالية', 'success');
            }
        });
    }

    function toggleOnlineFields() {
        if (!isOnlineCheckbox) {
            return;
        }

        const isOnline = isOnlineCheckbox.checked;

        if (locationInput) {
            locationInput.required = !isOnline;
            locationInput.placeholder = isOnline ? 'مثال: أونلاين عبر Zoom' : 'مثال: عمان - شارع الملكة رانيا';
            locationInput.classList.toggle('border-emerald-400', isOnline);
            if (locationHelp) {
                locationHelp.textContent = isOnline
                    ? 'الحقل اختياري للورش الأونلاين، ويمكنك إضافة وصف عام للمكان إذا رغبت.'
                    : 'يجب تحديد الموقع عند اختيار ورشة حضورية.';
            }
        }

        if (meetingLinkInput) {
            meetingLinkInput.required = isOnline;
            meetingLinkInput.placeholder = isOnline ? 'https://zoom.us/j/123456789' : 'يمكن ترك الحقل فارغاً للورش الحضورية';
            meetingLinkInput.classList.toggle('ring-2', isOnline);
            meetingLinkInput.classList.toggle('ring-amber-400', isOnline);
            if (meetingLinkHelp) {
                meetingLinkHelp.textContent = isOnline
                    ? 'يجب إضافة رابط الاجتماع، وسيظهر للمشاركين بعد تأكيد الحجز.'
                    : 'هذا الحقل اختياري للورش الحضورية.';
            }
        }
    }

    if (isOnlineCheckbox) {
        toggleOnlineFields();
        isOnlineCheckbox.addEventListener('change', toggleOnlineFields);
    }

    const recipeSearch = document.getElementById('recipe-search');
    const selectAllBtn = document.getElementById('select-all-recipes');
    const clearSelectionBtn = document.getElementById('clear-selection');
    const recipeCheckboxes = document.querySelectorAll('.recipe-checkbox');
    const recipeItems = document.querySelectorAll('.recipe-item');

    if (recipeSearch) {
        recipeSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            recipeItems.forEach(item => {
                const title = item.getAttribute('data-recipe-title');
                item.style.display = title.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            recipeCheckboxes.forEach(checkbox => {
                const item = checkbox.closest('.recipe-item');
                if (item && item.style.display !== 'none') {
                    checkbox.checked = true;
                    item.classList.add('selected');
                }
            });
        });
    }

    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', () => {
            recipeCheckboxes.forEach(checkbox => checkbox.checked = false);
            recipeItems.forEach(item => item.classList.remove('selected'));
        });
    }

    recipeCheckboxes.forEach(checkbox => {
        const recipeItem = checkbox.closest('.recipe-item');

        if (checkbox.checked && recipeItem) {
            recipeItem.classList.add('selected');
        }

        checkbox.addEventListener('change', () => {
            if (!recipeItem) {
                return;
            }

            if (checkbox.checked) {
                recipeItem.classList.add('selected');
            } else {
                recipeItem.classList.remove('selected');
            }
        });
    });

    if (featuredCheckbox) {
        featuredCheckbox.addEventListener('change', function() {
            if (this.checked) {
                fetch(`/admin/workshops/check-featured?exclude={{ $workshop->id }}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.hasFeatured) {
                            const confirmed = confirm('يوجد ورشة مميزة حالياً. هل تريد جعل هذه الورشة هي الورشة المميزة الجديدة؟ سيتم إلغاء تمييز الورشة السابقة.');
                            if (!confirmed) {
                                featuredCheckbox.checked = false;
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    }

    form.addEventListener('submit', async event => {
        if (form.dataset.submitting === 'true') {
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const showLoading = () => {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>جاري الحفظ...</span>';
            }
        };

        if (featuredCheckbox && featuredCheckbox.checked) {
            event.preventDefault();
            try {
                const response = await fetch(`/admin/workshops/check-featured?exclude={{ $workshop->id }}`);
                const data = await response.json();
                if (data.hasFeatured) {
                    const confirmed = confirm('يوجد ورشة مميزة حالياً. هل تريد جعل هذه الورشة هي الورشة المميزة الجديدة؟ سيتم إلغاء تمييز الورشة السابقة.');
                    if (!confirmed) {
                        return;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }

            showLoading();
            form.dataset.submitting = 'true';
            form.submit();
            return;
        }

        showLoading();
        form.dataset.submitting = 'true';
    });
});
</script>
@endpush
