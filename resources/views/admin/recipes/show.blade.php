@extends('layouts.app')

@section('title', $recipe->title . ' - إدارة الوصفات')

@push('styles')
<style>
    .admin-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #f3f4f6;
    }
    .btn-primary {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
    }
    .btn-secondary {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-secondary:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
    }
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }
    .info-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-icon {
        width: 1.5rem;
        color: #f97316;
        margin-left: 1rem;
    }
    .step-number {
        background: #f97316;
        color: white;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-left: 1rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Header -->
        <div class="admin-card p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $recipe->title }}</h1>
                    <p class="text-gray-600">عرض تفاصيل الوصفة</p>
                </div>
                <div class="flex space-x-4 rtl:space-x-reverse mt-4 md:mt-0">
                    <a href="{{ route('admin.recipes.edit', $recipe) }}" class="btn-primary">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.recipes.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-right ml-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recipe Image and Basic Info -->
            <div class="lg:col-span-2">
                <div class="admin-card overflow-hidden mb-8">
                    <img src="{{ $recipe->image_url_display ?? $recipe->image_url }}" alt="{{ $recipe->title }}" 
                         class="w-full h-64 md:h-80 object-cover"
                         onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                </div>

                <!-- Description -->
                <div class="admin-card p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">وصف الوصفة</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $recipe->description }}</p>
                </div>

                <!-- Steps -->
                <div class="admin-card p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">خطوات التحضير</h2>
                    <div class="space-y-6">
                        @foreach($recipe->steps as $index => $step)
                            <div class="flex items-start">
                                <div class="step-number">{{ $index + 1 }}</div>
                                <p class="text-gray-700 leading-relaxed flex-1">{{ $step }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Recipe Info -->
                <div class="admin-card p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">معلومات الوصفة</h3>
                    
                    <div class="space-y-0">
                        <div class="info-item">
                            <i class="fas fa-user info-icon"></i>
                            <div>
                                <span class="text-gray-500 text-sm">المؤلف</span>
                                <p class="font-semibold text-gray-900">{{ $recipe->author }}</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-tag info-icon"></i>
                            <div>
                                <span class="text-gray-500 text-sm">الفئة</span>
                                <p class="font-semibold text-gray-900">{{ $recipe->category->name ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-signal info-icon"></i>
                            <div>
                                <span class="text-gray-500 text-sm">مستوى الصعوبة</span>
                                <p class="font-semibold text-gray-900">
                                    @switch($recipe->difficulty)
                                        @case('easy') سهل @break
                                        @case('medium') متوسط @break
                                        @case('hard') صعب @break
                                        @default غير محدد
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-clock info-icon"></i>
                            <div>
                                <span class="text-gray-500 text-sm">وقت التحضير</span>
                                <p class="font-semibold text-gray-900">{{ (int)$recipe->prep_time }} دقيقة</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-fire info-icon"></i>
                            <div>
                                <span class="text-gray-500 text-sm">وقت الطبخ</span>
                                <p class="font-semibold text-gray-900">{{ (int)$recipe->cook_time }} دقيقة</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-users info-icon"></i>
                            <div>
                                <span class="text-gray-500 text-sm">عدد الحصص</span>
                                <p class="font-semibold text-gray-900">{{ (int)$recipe->servings }} حصة</p>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-calendar info-icon"></i>
                            <div>
                                <span class="text-gray-500 text-sm">تاريخ الإنشاء</span>
                                <p class="font-semibold text-gray-900">{{ $recipe->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients -->
                <div class="admin-card p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">المكونات</h3>
                    
                    <div class="space-y-3">
                        @foreach($recipe->ingredients as $ingredient)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-900">{{ $ingredient->name }}</span>
                                <span class="text-orange-600 font-semibold">{{ $ingredient->quantity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="admin-card p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">الإجراءات</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('admin.recipes.edit', $recipe) }}" class="btn-primary w-full text-center">
                            <i class="fas fa-edit ml-2"></i>
                            تعديل الوصفة
                        </a>
                        
                        <form action="{{ route('admin.recipes.destroy', $recipe) }}" method="POST" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الوصفة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger w-full">
                                <i class="fas fa-trash ml-2"></i>
                                حذف الوصفة
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.recipes.index') }}" class="btn-secondary w-full text-center">
                            <i class="fas fa-arrow-right ml-2"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
