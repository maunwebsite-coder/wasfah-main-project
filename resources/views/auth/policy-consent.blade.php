@extends('layouts.auth')

@section('title', __('تأكيد البيانات'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-2xl border border-orange-100 p-10 space-y-8">
            <div class="space-y-3 text-center">
                <p class="text-sm uppercase font-semibold tracking-[0.3em] text-orange-500">Your name</p>
                <h1 class="text-3xl font-extrabold text-gray-900">Let us know what we should call you</h1>
                <p class="text-gray-600 leading-relaxed">
                    We'll use this name on your Wasfah dashboard, in emails, and whenever you interact with our chefs.
                </p>
            </div>

            <form method="POST" action="{{ route('policy-consent.store') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-gray-900">Full name</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        dir="auto"
                        value="{{ old('name', $user->name) }}"
                        required
                        maxlength="255"
                        class="w-full rounded-2xl border border-gray-200 bg-white/70 focus:bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                        placeholder="اكتب اسمك الكامل هنا"
                    >
                    @error('name')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start gap-3 rounded-2xl border border-gray-200 bg-orange-50/40 px-4 py-4">
                    <input
                        type="checkbox"
                        name="accept_terms"
                        id="accept_terms"
                        value="1"
                        class="mt-1 h-5 w-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                        {{ old('accept_terms') ? 'checked' : '' }}
                        required
                    >
                    <label for="accept_terms" class="text-sm text-gray-800 leading-6">
                        I agree to the
                        <a href="{{ $termsUrl }}" target="_blank" rel="noopener" class="text-orange-600 font-semibold hover:underline">Terms of Service</a>
                        and
                        <a href="{{ $privacyUrl }}" target="_blank" rel="noopener" class="text-orange-600 font-semibold hover:underline">Privacy Policy</a>.
                        <span class="block text-xs text-gray-500 mt-1">
                            أوافق على شروط الخدمة وسياسة الخصوصية لديك.
                        </span>
                    </label>
                </div>
                @error('accept_terms')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="space-y-4">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-500 text-white font-semibold py-3 shadow-lg shadow-orange-200 hover:bg-orange-600 transition"
                    >
                        Create an account
                        <span class="text-xs uppercase tracking-widest">Continue</span>
                    </button>

                    <div class="text-center">
                        <a href="{{ url()->previous() ?: route('home') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900">
                            Go back
                        </a>
                    </div>
                </div>
            </form>

            <div class="text-center text-xs text-gray-400">
                Policy version {{ $policyVersion }}
            </div>
        </div>
    </div>
</div>
@endsection

