@extends('layouts.app')

@section('title', 'ملف الشيف ' . ($chef->name ?? ''))

@section('content')
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 space-y-8">
            <div class="bg-white rounded-3xl shadow-lg border border-orange-100 p-8 flex flex-col md:flex-row items-center md:items-start gap-6">
                <div class="w-32 h-32 rounded-full border-4 border-orange-100 overflow-hidden shadow-md flex items-center justify-center bg-orange-50 text-orange-600 text-3xl font-bold">
                    @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="صورة الشيف {{ $chef->name }}" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($chef->name ?? 'شيف', 0, 1) }}
                    @endif
                </div>
                <div class="flex-1 space-y-4 text-center md:text-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $chef->name }}</h1>
                        <p class="text-gray-600 mt-1">
                            {{ $chef->chef_specialty_description ?: 'شيف مبدع يشارك وصفاته مع مجتمع وصفه.' }}
                        </p>
                    </div>

                    @if ($socialLinks->isNotEmpty())
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                            @foreach ($socialLinks as $link)
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full border border-orange-200 px-4 py-2 text-sm text-orange-600 transition hover:bg-orange-50 hover:text-orange-700">
                                    <i class="{{ $link['icon'] }}"></i>
                                    <span>{{ $link['label'] }}</span>
                                    @if (!empty($link['followers']))
                                        <span class="text-orange-500">({{ number_format($link['followers']) }})</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="rounded-2xl bg-orange-50 px-4 py-3 text-center">
                            <p class="text-xs text-orange-500">عدد الوصفات</p>
                            <p class="text-2xl font-semibold text-orange-600">{{ number_format($stats['recipes_count']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3 text-center">
                            <p class="text-xs text-gray-500">تم الحفظ</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ number_format($stats['total_saves']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3 text-center">
                            <p class="text-xs text-gray-500">تم التجربة</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ number_format($stats['total_made']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 px-4 py-3 text-center">
                            <p class="text-xs text-gray-500">متوسط التقييم</p>
                            <p class="text-2xl font-semibold text-gray-800">
                                {{ $stats['average_rating'] ? number_format($stats['average_rating'], 1) : '—' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-900">وصفات عامة</h2>
                    <span class="text-sm text-gray-500">{{ $publicRecipes->count() }} وصفة</span>
                </div>

                @if ($publicRecipes->isEmpty())
                    <div class="rounded-2xl bg-white border border-dashed border-orange-200 p-8 text-center text-gray-500">
                        لا توجد وصفات عامة متاحة حاليًا.
                    </div>
                @else
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($publicRecipes as $recipe)
                            <a href="{{ route('recipe.show', ['recipe' => $recipe->slug ?? $recipe->recipe_id]) }}" class="group rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden transition hover:-translate-y-1 hover:shadow-lg">
                                @if (!empty($recipe->image_url))
                                    <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}" class="w-full h-48 object-cover">
                                @endif
                                <div class="p-5 space-y-2">
                                    <h3 class="font-semibold text-lg text-gray-900 group-hover:text-orange-600 transition">{{ $recipe->title }}</h3>
                                    @if (!empty($recipe->category?->name))
                                        <p class="text-sm text-gray-500">{{ $recipe->category->name }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span><i class="fas fa-bookmark text-orange-500 ms-1"></i>{{ number_format($recipe->saved_count ?? 0) }}</span>
                                        <span><i class="fas fa-utensils text-orange-500 ms-1"></i>{{ number_format($recipe->made_count ?? 0) }}</span>
                                        <span><i class="fas fa-star text-orange-500 ms-1"></i>{{ number_format((float) ($recipe->interactions_avg_rating ?? 0), 1) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($canViewExclusive && $exclusiveRecipes->isNotEmpty())
                <div class="space-y-6">
                    <h2 class="text-2xl font-semibold text-gray-900">وصفات خاصة</h2>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($exclusiveRecipes as $recipe)
                            <div class="rounded-2xl bg-orange-900 text-white shadow-lg overflow-hidden">
                                @if (!empty($recipe->image_url))
                                    <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}" class="w-full h-44 object-cover opacity-80">
                                @endif
                                <div class="p-5 space-y-2">
                                    <h3 class="font-semibold text-lg">{{ $recipe->title }}</h3>
                                    <p class="text-sm text-orange-100 line-clamp-2">
                                        {{ $recipe->excerpt ?? 'وصفة حصرية لأعضاء مجتمع الشيف.' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($popularRecipes->isNotEmpty())
                <div class="space-y-6">
                    <h2 class="text-2xl font-semibold text-gray-900">أبرز الوصفات</h2>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($popularRecipes as $recipe)
                            <a href="{{ route('recipe.show', ['recipe' => $recipe->slug ?? $recipe->recipe_id]) }}" class="rounded-2xl bg-white border border-orange-100 shadow-sm p-5 flex flex-col gap-3 transition hover:border-orange-300 hover:shadow-md">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-lg text-gray-900">{{ $recipe->title }}</h3>
                                    <span class="inline-flex items-center gap-1 text-sm text-orange-600 font-medium">
                                        <i class="fas fa-fire"></i>
                                        شائع
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span><i class="fas fa-bookmark text-orange-500 ms-1"></i>{{ number_format($recipe->saved_count ?? 0) }}</span>
                                    <span><i class="fas fa-utensils text-orange-500 ms-1"></i>{{ number_format($recipe->made_count ?? 0) }}</span>
                                    <span><i class="fas fa-star text-orange-500 ms-1"></i>{{ number_format((float) ($recipe->interactions_avg_rating ?? 0), 1) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
