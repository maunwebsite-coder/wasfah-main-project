@extends('layouts.app')

@section('title', 'إدارة الوصفات - موقع وصفة')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@push('styles')
<style>
    .admin-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #f3f4f6;
    }
    .recipe-card {
        transition: all 0.2s ease;
    }
    .recipe-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .btn-primary {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
    }
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="admin-card p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة الوصفات</h1>
                    <p class="text-gray-600">إدارة وإضافة وتعديل الوصفات</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('admin.recipes.create') }}" class="btn-primary">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة وصفة جديدة
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle ml-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Recipes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($recipes as $recipe)
                <div class="recipe-card admin-card overflow-hidden">
                    <!-- Recipe Image -->
                    <div class="relative h-48">
                        <img src="{{ $recipe->image ? Storage::disk('public')->url($recipe->image) : $recipe->image_url }}" alt="{{ $recipe->title }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                        <div class="absolute top-4 right-4">
                            <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                {{ ucfirst($recipe->difficulty) }}
                            </span>
                        </div>
                    </div>

                    <!-- Recipe Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $recipe->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $recipe->description }}</p>
                        
                        <!-- Recipe Info -->
                        <div class="space-y-2 text-sm text-gray-500 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-user w-4 text-orange-500 ml-2"></i>
                                <span>{{ $recipe->author }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock w-4 text-orange-500 ml-2"></i>
                                <span>{{ (int)$recipe->prep_time + (int)$recipe->cook_time }} دقيقة</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users w-4 text-orange-500 ml-2"></i>
                                <span>{{ (int)$recipe->servings }} حصة</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-tag w-4 text-orange-500 ml-2"></i>
                                <span>{{ $recipe->category->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <a href="{{ route('admin.recipes.show', $recipe) }}" 
                               class="btn-secondary flex-1 text-center">
                                <i class="fas fa-eye ml-1"></i>
                                عرض
                            </a>
                            <a href="{{ route('admin.recipes.edit', $recipe) }}" 
                               class="btn-primary flex-1 text-center">
                                <i class="fas fa-edit ml-1"></i>
                                تعديل
                            </a>
                            <form action="{{ route('admin.recipes.destroy', $recipe) }}" 
                                  method="POST" class="flex-1" 
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الوصفة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full">
                                    <i class="fas fa-trash ml-1"></i>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">لا توجد وصفات</h3>
                    <p class="text-gray-500 mb-6">ابدأ بإضافة وصفة جديدة</p>
                    <a href="{{ route('admin.recipes.create') }}" class="btn-primary">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة وصفة جديدة
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($recipes->hasPages())
            <div class="mt-8">
                {{ $recipes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
