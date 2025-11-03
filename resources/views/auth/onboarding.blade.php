@extends('layouts.app')

@section('title', 'إكمال بيانات الشيف')

@push('styles')
<style>
    .onboarding-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(249, 115, 22, 0.08);
    }
    .onboarding-header {
        background: linear-gradient(135deg, #f97316 0%, #fb923c 40%, #fcd34d 100%);
        color: white;
        border-radius: 1.5rem 1.5rem 0 0;
        padding: 2.5rem 2rem;
        position: relative;
        overflow: hidden;
    }
    .onboarding-header::after {
        content: "";
        position: absolute;
        bottom: -40px;
        right: -40px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.18);
        border-radius: 9999px;
    }
    .onboarding-content {
        padding: 2.5rem;
    }
    .badge-tip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(34, 197, 94, 0.1);
        color: #047857;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .info-card {
        background: rgba(249, 115, 22, 0.08);
        border: 1px solid rgba(249, 115, 22, 0.2);
        border-radius: 1rem;
        padding: 1.25rem;
        color: #7c2d12;
    }
    .input-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        display: block;
    }
    .input-control {
        border-radius: 0.85rem;
        border: 1px solid #e2e8f0;
        width: 100%;
        padding: 0.85rem 1rem;
        transition: all 0.2s ease;
    }
    .phone-prefix {
        min-width: 4.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.85rem 1rem;
        border-radius: 0.85rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-weight: 600;
        color: #1f2937;
    }
    .input-control:focus {
        outline: none;
        border-color: #fb923c;
        box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.25);
    }
    .submit-btn {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        padding: 0.95rem 1.5rem;
        border-radius: 0.85rem;
        border: none;
        font-weight: 700;
        font-size: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 35px rgba(249, 115, 22, 0.35);
    }
    .submit-btn:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.28);
    }
</style>
@endpush

@section('content')
@php
    $availableCountries = $countries ?? [];
    $defaultCountryCode = 'SA';
    $selectedCountryCode = old('country_code', $user->country_code ?? $defaultCountryCode);
    if (!array_key_exists($selectedCountryCode, $availableCountries)) {
        $selectedCountryCode = $defaultCountryCode;
    }
    $dialCodeFromCountry = $availableCountries[$selectedCountryCode]['dial_code'] ?? '+966';
    $storedDialCode = old('phone_country_code', $user->phone_country_code ?? $dialCodeFromCountry);
    $currentDialCode = str_starts_with($storedDialCode, '+') ? $storedDialCode : '+' . ltrim($storedDialCode ?? '', '+');
    $phoneInputValue = old('phone');
    if ($phoneInputValue === null) {
        $fullPhone = $user->phone ?? '';
        if ($fullPhone && str_starts_with($fullPhone, $currentDialCode)) {
            $phoneInputValue = ltrim(substr($fullPhone, strlen($currentDialCode)), ' ');
        } else {
            $phoneInputValue = $fullPhone;
        }
    }
@endphp
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-100 py-10">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="onboarding-card">
            <div class="onboarding-header">
                <h1 class="text-3xl font-bold mb-3">مرحباً بك في مجتمع شيف وصفة</h1>
                <p class="text-base md:text-lg text-orange-50/90 max-w-2xl leading-relaxed">
                    نحتاج بعض المعلومات للتأكد من أنك شيف متخصص في عالم الطعام، ولديك حضور قوي على وسائل التواصل. هذه الخطوة تساعدنا في تقديم أفضل تجربة للمتابعين!
                </p>
            </div>
            <div class="onboarding-content">
                @if (session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                    <div class="badge-tip">
                        <i class="fas fa-check-circle"></i>
                        يجب أن تمتلك 500 متابع على الأقل على إحدى المنصات
                    </div>
                    <div class="text-sm text-gray-500">
                        تم تسجيل الدخول بواسطة {{ $user->email }}
                    </div>
                </div>

                <div class="info-card mb-8">
                    <h2 class="text-lg font-semibold mb-2 text-orange-800">لماذا نطلب هذه البيانات؟</h2>
                    <ul class="list-disc list-inside text-sm space-y-1 text-orange-700/90">
                        <li>نضمن أن الشيف يمتلك تأثيراً حقيقياً على منصات التواصل.</li>
                        <li>نؤكد أن المحتوى الذي يقدمه متخصص في عالم الغذاء والطبخ.</li>
                        <li>نساعد الشيف على إبراز قنواته الاجتماعية لزوار وصفة.</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-6">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="input-label">الدولة *</label>
                            <select name="country_code" id="country-code" class="input-control">
                                <option value="">اختر دولتك</option>
                                @foreach($availableCountries as $code => $country)
                                    <option value="{{ $code }}"
                                            data-dial-code="{{ $country['dial_code'] }}"
                                            {{ $code === $selectedCountryCode ? 'selected' : '' }}>
                                        {{ $country['name'] }} ({{ $country['dial_code'] }})
                                    </option>
                                @endforeach
                            </select>
                            @error('country_code')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="input-label">رقم الجوال *</label>
                            <div class="flex items-center gap-3">
                                <span class="phone-prefix" id="dial-code-display">{{ $currentDialCode }}</span>
                                <div class="flex-1">
                                    <input type="text"
                                           name="phone"
                                           value="{{ $phoneInputValue }}"
                                           class="input-control"
                                           placeholder="مثال: 5XXXXXXXX">
                                </div>
                            </div>
                            <input type="hidden" name="phone_country_code" id="phone_country_code" value="{{ $currentDialCode }}">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <div class="bg-orange-50 border border-orange-100 rounded-lg px-4 py-3 text-sm text-orange-700">
                                يرجى توفير حساب إنستغرام أو قناة يوتيوب واحدة على الأقل حتى نتمكن من التحقق من خبرتك ونشاطك.
                            </div>
                        </div>
                        <div>
                            <label class="input-label">رابط حساب إنستغرام (اختياري)</label>
                            <input type="url"
                                   name="instagram_url"
                                   value="{{ old('instagram_url', $user->instagram_url) }}"
                                   class="input-control"
                                   placeholder="https://www.instagram.com/username">
                            @error('instagram_url')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="input-label">عدد المتابعين في إنستغرام (اختياري)</label>
                            <input type="number"
                                   name="instagram_followers"
                                   value="{{ old('instagram_followers', $user->instagram_followers) }}"
                                   min="0"
                                   class="input-control"
                                   placeholder="مثال: 1200">
                            @error('instagram_followers')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="input-label">رابط قناة يوتيوب (اختياري)</label>
                            <input type="url"
                                   name="youtube_url"
                                   value="{{ old('youtube_url', $user->youtube_url) }}"
                                   class="input-control"
                                   placeholder="https://www.youtube.com/channel/...">
                            @error('youtube_url')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="input-label">عدد المتابعين في يوتيوب (اختياري)</label>
                            <input type="number"
                                   name="youtube_followers"
                                   value="{{ old('youtube_followers', $user->youtube_followers) }}"
                                   min="0"
                                   class="input-control"
                                   placeholder="مثال: 800">
                            @error('youtube_followers')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @php
                            $instagramOldValue = old('instagram_url', $user->instagram_url);
                            $youtubeOldValue = old('youtube_url', $user->youtube_url);
                            $showSocialRequiredError = ($errors->has('instagram_url') || $errors->has('youtube_url')) && empty($instagramOldValue) && empty($youtubeOldValue);
                        @endphp
                        @if($showSocialRequiredError)
                            <div class="md:col-span-2">
                                <p class="mt-1 text-sm text-red-500">
                                    يرجى إدخال حساب إنستغرام أو قناة يوتيوب واحدة على الأقل.
                                </p>
                            </div>
                        @endif
                        <div>
                            <label class="input-label">مجال التخصص *</label>
                            <select name="chef_specialty_area" class="input-control">
                                <option value="">اختر تخصصك الرئيسي</option>
                                <option value="food" {{ old('chef_specialty_area', $user->chef_specialty_area) === 'food' ? 'selected' : '' }}>الطعام والطبخ</option>
                            </select>
                            @error('chef_specialty_area')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <p class="text-xs text-gray-500">يكفي إضافة منصة واحدة (إنستغرام أو يوتيوب) مع عدد المتابعين الخاص بها.</p>

                    <div>
                        <label class="input-label">صف لنا خبرتك في عالم الطعام *</label>
                        <textarea name="chef_specialty_description"
                                  rows="5"
                                  class="input-control"
                                  placeholder="حدّثنا عن نوع المحتوى الذي تقدمه، إنجازاتك، والسبب الذي يجعل متابعيك يثقون بذوقك.">{{ old('chef_specialty_description', $user->chef_specialty_description) }}</textarea>
                        @error('chef_specialty_description')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        سيتم مراجعة طلبك من قبل فريق وصفة، وسنقوم بإشعارك فور اعتمادك كـ شيف.
                    </div>

                    <div class="text-center pt-4">
                        <button type="submit" class="submit-btn">
                            إرسال الطلب للمراجعة
                        </button>
                    </div>
                </form>
</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countrySelect = document.getElementById('country-code');
        const dialCodeDisplay = document.getElementById('dial-code-display');
        const phoneCountryCodeInput = document.getElementById('phone_country_code');

        const updateDialCode = () => {
            if (!countrySelect) {
                return;
            }

            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const dialCode = selectedOption ? selectedOption.getAttribute('data-dial-code') : '';

            if (dialCodeDisplay) {
                dialCodeDisplay.textContent = dialCode || '+';
            }

            if (phoneCountryCodeInput) {
                phoneCountryCodeInput.value = dialCode || '';
            }
        };

        if (countrySelect) {
            countrySelect.addEventListener('change', updateDialCode);
            updateDialCode();
        }
    });
</script>
@endpush
