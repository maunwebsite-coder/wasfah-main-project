@extends('layouts.app')

@section('title', 'تفاصيل الورشة - لوحة الإدارة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">تفاصيل الورشة</h1>
                <p class="text-gray-600">{{ $workshop->title }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.workshops.edit', $workshop->id) }}" 
                   class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
                <a href="{{ route('admin.workshops.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Workshop Image -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <img src="{{ $workshop->image ? asset('storage/' . $workshop->image) : 'https://placehold.co/800x400/f87171/FFFFFF?text=ورشة' }}" 
                         alt="{{ $workshop->title }}" 
                         class="w-full h-64 object-cover">
                </div>

                <!-- Workshop Details -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">تفاصيل الورشة</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">الوصف</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $workshop->description }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">المدرب</h3>
                                <p class="text-gray-600">{{ $workshop->instructor }}</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">السعر</h3>
                                <p class="text-gray-600 font-bold">{{ $workshop->price }} {{ $workshop->currency }}</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">تاريخ البداية</h3>
                                <p class="text-gray-600">{{ $workshop->start_date->format('d M, Y - H:i') }}</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">تاريخ النهاية</h3>
                                <p class="text-gray-600">{{ $workshop->end_date->format('d M, Y - H:i') }}</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">العدد الأقصى</h3>
                                <p class="text-gray-600">{{ $workshop->max_participants }} مشارك</p>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-700 mb-2">النوع</h3>
                                <p class="text-gray-600">{{ $workshop->is_online ? 'أونلاين' : 'حضوري' }}</p>
                            </div>
                        </div>

                        @if($workshop->location && !$workshop->is_online)
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">الموقع</h3>
                            <p class="text-gray-600">{{ $workshop->location }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4">حالة الورشة</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">الحالة:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $workshop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $workshop->is_active ? 'نشطة' : 'غير نشطة' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">النوع:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $workshop->is_online ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $workshop->is_online ? 'أونلاين' : 'حضوري' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">تاريخ الإنشاء:</span>
                            <span class="text-gray-800">{{ $workshop->created_at->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bookings Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4">الحجوزات</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">عدد الحجوزات:</span>
                            <span class="font-bold text-gray-800">{{ $workshop->bookings_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">المقاعد المتاحة:</span>
                            <span class="font-bold text-gray-800">{{ $workshop->max_participants - $workshop->bookings_count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" 
                                 style="width: {{ ($workshop->bookings_count / $workshop->max_participants) * 100 }}%"></div>
                        </div>
                        <p class="text-sm text-gray-500 text-center">
                            {{ round(($workshop->bookings_count / $workshop->max_participants) * 100) }}% مكتمل
                        </p>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4">الإجراءات</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.workshops.edit', $workshop->id) }}" 
                           class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-edit ml-2"></i>
                            تعديل الورشة
                        </a>
                        
                        <form action="{{ route('admin.workshops.toggle-status', $workshop->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full {{ $workshop->is_active ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas {{ $workshop->is_active ? 'fa-pause' : 'fa-play' }} ml-2"></i>
                                {{ $workshop->is_active ? 'إلغاء التفعيل' : 'تفعيل الورشة' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.workshops.destroy', $workshop->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الورشة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-trash ml-2"></i>
                                حذف الورشة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

