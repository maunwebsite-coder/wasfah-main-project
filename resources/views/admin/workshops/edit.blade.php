@extends('layouts.app')

@section('title', 'تعديل الورشة - لوحة الإدارة')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">تعديل الورشة</h1>
                <p class="text-gray-600">تعديل بيانات ورشة: {{ $workshop->title }}</p>
            </div>
            <a href="{{ route('admin.workshops.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form action="{{ route('admin.workshops.update', $workshop->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">عنوان الورشة *</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $workshop->title) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('title') border-red-500 @enderror"
                               placeholder="مثال: تعلم صنع الكيك الفرنسي"
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructor -->
                    <div>
                        <label for="instructor" class="block text-sm font-semibold text-gray-700 mb-2">اسم المدرب *</label>
                        <input type="text" 
                               id="instructor" 
                               name="instructor" 
                               value="{{ old('instructor', $workshop->instructor) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('instructor') border-red-500 @enderror"
                               placeholder="مثال: الشيف أحمد محمد"
                               required>
                        @error('instructor')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">السعر *</label>
                        <div class="flex">
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', $workshop->price) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('price') border-red-500 @enderror"
                                   placeholder="0"
                                   min="0"
                                   step="0.01"
                                   required>
                            <select name="currency" 
                                    class="px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('currency') border-red-500 @enderror">
                                <option value="JOD" {{ old('currency', $workshop->currency) == 'JOD' ? 'selected' : '' }}>دينار أردني</option>
                                <option value="AED" {{ old('currency', $workshop->currency) == 'AED' ? 'selected' : '' }}>درهم إماراتي</option>
                            </select>
                        </div>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('currency')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">تاريخ البداية *</label>
                        <input type="datetime-local" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date', $workshop->start_date->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('start_date') border-red-500 @enderror"
                               required>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">تاريخ النهاية *</label>
                        <input type="datetime-local" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ old('end_date', $workshop->end_date->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('end_date') border-red-500 @enderror"
                               required>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Participants -->
                    <div>
                        <label for="max_participants" class="block text-sm font-semibold text-gray-700 mb-2">العدد الأقصى للمشاركين *</label>
                        <input type="number" 
                               id="max_participants" 
                               name="max_participants" 
                               value="{{ old('max_participants', $workshop->max_participants) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('max_participants') border-red-500 @enderror"
                               placeholder="20"
                               min="1"
                               required>
                        @error('max_participants')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">الموقع *</label>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               value="{{ old('location', $workshop->location) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('location') border-red-500 @enderror"
                               placeholder="مثال: عمان - شارع الملكة رانيا"
                               required>
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image Upload Section -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            {{ $workshop->image ? 'تغيير صورة الورشة' : 'صورة الورشة' }}
                        </label>
                        
                        <!-- Current Image Display -->
                        @if($workshop->image)
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">الصورة الحالية</label>
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $workshop->image) }}?v={{ time() }}" 
                                     alt="{{ $workshop->title }}" 
                                     class="w-32 h-32 object-cover rounded-lg border border-gray-200"
                                     id="current-image-preview"
                                     onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-2">لحذف الصورة الحالية، اختر صورة جديدة</p>
                                    <button type="button" 
                                            id="remove-current-image"
                                            class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash ml-1"></i>
                                        حذف الصورة الحالية
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Image Upload Area -->
                        <div class="image-upload-container">
                            <div class="image-upload-area" 
                                 id="image-upload-area"
                                 onclick="document.getElementById('image').click()">
                                <div class="text-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                                    <p class="text-lg font-medium text-gray-700 mb-2">اسحب وأفلت الصورة هنا</p>
                                    <p class="text-sm text-gray-500 mb-4">أو انقر للاختيار من جهازك</p>
                                    <div class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                        <i class="fas fa-folder-open ml-2"></i>
                                        اختر صورة
                                    </div>
                                </div>
                            </div>
                            
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="handleImageUpload(this)">
                            
                            <!-- Image Preview -->
                            <div id="image-preview" class="hidden mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">معاينة الصورة الجديدة</label>
                                <div class="relative inline-block">
                                    <img id="preview-img" 
                                         class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                    <button type="button" 
                                            id="remove-preview"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors"
                                            onclick="removeImagePreview()">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600" id="image-info"></p>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-500 mt-3">
                                <i class="fas fa-info-circle ml-1"></i>
                                الصيغ المدعومة: JPEG, PNG, JPG, GIF, WebP (حجم أقصى: 5MB)
                            </p>
                            
                            @error('image')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">فئة الورشة *</label>
                        <select id="category" 
                                name="category" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('category') border-red-500 @enderror"
                                required>
                            <option value="">اختر الفئة</option>
                            <option value="cooking" {{ old('category', $workshop->category) == 'cooking' ? 'selected' : '' }}>طبخ</option>
                            <option value="baking" {{ old('category', $workshop->category) == 'baking' ? 'selected' : '' }}>خبز</option>
                            <option value="desserts" {{ old('category', $workshop->category) == 'desserts' ? 'selected' : '' }}>حلويات</option>
                            <option value="beverages" {{ old('category', $workshop->category) == 'beverages' ? 'selected' : '' }}>مشروبات</option>
                            <option value="other" {{ old('category', $workshop->category) == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Level -->
                    <div>
                        <label for="level" class="block text-sm font-semibold text-gray-700 mb-2">مستوى الورشة *</label>
                        <select id="level" 
                                name="level" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('level') border-red-500 @enderror"
                                required>
                            <option value="">اختر المستوى</option>
                            <option value="beginner" {{ old('level', $workshop->level) == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                            <option value="intermediate" {{ old('level', $workshop->level) == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                            <option value="advanced" {{ old('level', $workshop->level) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                        </select>
                        @error('level')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-semibold text-gray-700 mb-2">مدة الورشة (بالدقائق) *</label>
                        <input type="number" 
                               id="duration" 
                               name="duration" 
                               value="{{ old('duration', $workshop->duration) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('duration') border-red-500 @enderror"
                               placeholder="120"
                               min="1"
                               required>
                        @error('duration')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Registration Deadline -->
                    <div>
                        <label for="registration_deadline" class="block text-sm font-semibold text-gray-700 mb-2">آخر موعد للتسجيل</label>
                        <input type="datetime-local" 
                               id="registration_deadline" 
                               name="registration_deadline" 
                               value="{{ old('registration_deadline', $workshop->registration_deadline ? $workshop->registration_deadline->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('registration_deadline') border-red-500 @enderror">
                        @error('registration_deadline')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meeting Link (for online workshops) -->
                    <div>
                        <label for="meeting_link" class="block text-sm font-semibold text-gray-700 mb-2">رابط الاجتماع (للورش الأونلاين)</label>
                        <input type="url" 
                               id="meeting_link" 
                               name="meeting_link" 
                               value="{{ old('meeting_link', $workshop->meeting_link) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('meeting_link') border-red-500 @enderror"
                               placeholder="https://meet.google.com/...">
                        @error('meeting_link')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">العنوان التفصيلي</label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address', $workshop->address) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('address') border-red-500 @enderror"
                               placeholder="مثال: شارع الملكة رانيا، عمان، الأردن">
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">وصف الورشة *</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="6"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror"
                                  placeholder="اكتب وصفاً مفصلاً عن الورشة وما سيتعلمه المشاركون..."
                                  required>{{ old('description', $workshop->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="md:col-span-2">
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">محتوى الورشة التفصيلي</label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="8"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('content') border-red-500 @enderror"
                                  placeholder="اكتب محتوى مفصلاً عن الورشة...">{{ old('content', $workshop->content) }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- What You Will Learn -->
                    <div class="md:col-span-2">
                        <label for="what_you_will_learn" class="block text-sm font-semibold text-gray-700 mb-2">ما سيتعلمه المشاركون</label>
                        <textarea id="what_you_will_learn" 
                                  name="what_you_will_learn" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('what_you_will_learn') border-red-500 @enderror"
                                  placeholder="اكتب ما سيتعلمه المشاركون في الورشة...">{{ old('what_you_will_learn', $workshop->what_you_will_learn) }}</textarea>
                        @error('what_you_will_learn')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requirements -->
                    <div class="md:col-span-2">
                        <label for="requirements" class="block text-sm font-semibold text-gray-700 mb-2">متطلبات الورشة</label>
                        <textarea id="requirements" 
                                  name="requirements" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('requirements') border-red-500 @enderror"
                                  placeholder="اكتب متطلبات الورشة...">{{ old('requirements', $workshop->requirements) }}</textarea>
                        @error('requirements')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Materials Needed -->
                    <div class="md:col-span-2">
                        <label for="materials_needed" class="block text-sm font-semibold text-gray-700 mb-2">المواد المطلوبة</label>
                        <textarea id="materials_needed" 
                                  name="materials_needed" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('materials_needed') border-red-500 @enderror"
                                  placeholder="اكتب المواد المطلوبة للورشة...">{{ old('materials_needed', $workshop->materials_needed) }}</textarea>
                        @error('materials_needed')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructor Bio -->
                    <div class="md:col-span-2">
                        <label for="instructor_bio" class="block text-sm font-semibold text-gray-700 mb-2">نبذة عن المدرب</label>
                        <textarea id="instructor_bio" 
                                  name="instructor_bio" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('instructor_bio') border-red-500 @enderror"
                                  placeholder="اكتب نبذة عن المدرب...">{{ old('instructor_bio', $workshop->instructor_bio) }}</textarea>
                        @error('instructor_bio')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Description -->
                    <div class="md:col-span-2">
                        <label for="featured_description" class="block text-sm font-semibold text-gray-700 mb-2">وصف الورشة المميزة</label>
                        <textarea id="featured_description" 
                                  name="featured_description" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('featured_description') border-red-500 @enderror"
                                  placeholder="وصف خاص للورشة المميزة (اختياري)">{{ old('featured_description', $workshop->featured_description) }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">هذا الوصف سيظهر في الكارت الكبير للورشة المميزة</p>
                        @error('featured_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recipe Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">اختيار وصفات الورشة</label>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-4">اختر الوصفات التي ستكون جزءاً من هذه الورشة (يمكنك اختيار أكثر من وصفة)</p>
                                <div class="flex items-center gap-4 mb-4">
                                    <input type="text" 
                                           id="recipe-search" 
                                           placeholder="البحث في الوصفات..."
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                    <button type="button" 
                                            id="select-all-recipes"
                                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                        اختيار الكل
                                    </button>
                                    <button type="button" 
                                            id="clear-selection"
                                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                        إلغاء الكل
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto" id="recipes-container">
                                @foreach($recipes as $recipe)
                                    <div class="recipe-item bg-white rounded-lg border border-gray-200 p-4 hover:border-orange-300 transition-colors" 
                                         data-recipe-id="{{ $recipe->recipe_id }}"
                                         data-recipe-title="{{ strtolower($recipe->title) }}">
                                        <label class="flex items-start cursor-pointer">
                                            <input type="checkbox" 
                                                   name="recipe_ids[]" 
                                                   value="{{ $recipe->recipe_id }}"
                                                   {{ in_array($recipe->recipe_id, old('recipe_ids', $workshop->recipes->pluck('recipe_id')->toArray())) ? 'checked' : '' }}
                                                   class="recipe-checkbox w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 mt-1">
                                            <div class="mr-3 flex-1">
                                                <div class="flex items-center mb-2">
                                                    <img src="{{ $recipe->image_url ?: 'https://placehold.co/60x60/f87171/FFFFFF?text=وصفة' }}" 
                                                         alt="{{ $recipe->title }}" 
                                                         class="w-12 h-12 rounded-lg object-cover ml-3"
                                                         onerror="this.src='{{ asset('image/logo.png') }}';">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-gray-900 text-sm line-clamp-2">{{ $recipe->title }}</h4>
                                                        <p class="text-xs text-gray-500">{{ $recipe->author }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between text-xs text-gray-500">
                                                    <span><i class="fas fa-clock ml-1"></i> {{ ($recipe->prep_time ?? 0) + ($recipe->cook_time ?? 0) }} دقيقة</span>
                                                    <span><i class="fas fa-users ml-1"></i> {{ $recipe->servings ?? 0 }} حصة</span>
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-600 rounded-full">
                                                        {{ $recipe->difficulty === 'easy' ? 'سهل' : ($recipe->difficulty === 'medium' ? 'متوسط' : 'صعب') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($recipes->count() == 0)
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-utensils text-4xl mb-4"></i>
                                    <p>لا توجد وصفات متاحة حالياً</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="md:col-span-2">
                        <div class="flex flex-wrap gap-6">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_online" 
                                       value="1"
                                       {{ old('is_online', $workshop->is_online) ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                                <span class="mr-2 text-sm font-medium text-gray-700">ورشة أونلاين</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $workshop->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                                <span class="mr-2 text-sm font-medium text-gray-700">تفعيل الورشة</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_featured" 
                                       value="1"
                                       {{ old('is_featured', $workshop->is_featured) ? 'checked' : '' }}
                                       class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                                <span class="mr-2 text-sm font-medium text-gray-700">ورشة مميزة (الورشة القادمة)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.workshops.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg transition-colors">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.image-upload-container {
    position: relative;
}

.image-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f9fafb;
}

.image-upload-area:hover {
    border-color: #f97316;
    background-color: #fff7ed;
}

.image-upload-area.dragover {
    border-color: #f97316;
    background-color: #fff7ed;
    transform: scale(1.02);
}

.image-upload-area.has-image {
    border-color: #10b981;
    background-color: #ecfdf5;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background-color: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 1rem;
}

.progress-fill {
    height: 100%;
    background-color: #f97316;
    transition: width 0.3s ease;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    color: white;
    font-weight: 500;
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    background-color: #10b981;
}

.notification.error {
    background-color: #ef4444;
}

.notification.warning {
    background-color: #f59e0b;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const featuredCheckbox = document.querySelector('input[name="is_featured"]');
    const form = document.querySelector('form');
    const workshopId = {{ $workshop->id }};
    
    // Image upload functionality
    const imageInput = document.getElementById('image');
    const imageUploadArea = document.getElementById('image-upload-area');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const imageInfo = document.getElementById('image-info');
    const removeCurrentImageBtn = document.getElementById('remove-current-image');
    const currentImagePreview = document.getElementById('current-image-preview');
    
    // Drag and drop functionality
    if (imageUploadArea) {
        imageUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        imageUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        imageUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                handleImageUpload(imageInput);
            }
        });
    }
    
    // Remove current image functionality
    if (removeCurrentImageBtn) {
        removeCurrentImageBtn.addEventListener('click', function() {
            if (confirm('هل أنت متأكد من حذف الصورة الحالية؟')) {
                // Hide current image
                this.closest('.mb-6').style.display = 'none';
                showNotification('تم حذف الصورة الحالية', 'success');
                
                // Add hidden input to indicate image should be removed
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'remove_image';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);
            }
        });
    }
    
    // Recipe selection functionality
    const recipeSearch = document.getElementById('recipe-search');
    const selectAllBtn = document.getElementById('select-all-recipes');
    const clearSelectionBtn = document.getElementById('clear-selection');
    const recipeCheckboxes = document.querySelectorAll('.recipe-checkbox');
    const recipeItems = document.querySelectorAll('.recipe-item');
    
    // Search functionality
    if (recipeSearch) {
        recipeSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            recipeItems.forEach(item => {
                const title = item.getAttribute('data-recipe-title');
                if (title.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Select all recipes
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            recipeCheckboxes.forEach(checkbox => {
                if (checkbox.closest('.recipe-item').style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
        });
    }
    
    // Clear selection
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            recipeCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    }
    
    // Update recipe item appearance when selected
    recipeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const recipeItem = this.closest('.recipe-item');
            if (this.checked) {
                recipeItem.classList.add('border-orange-500', 'bg-orange-50');
            } else {
                recipeItem.classList.remove('border-orange-500', 'bg-orange-50');
            }
        });
        
        // Initialize appearance based on current state
        if (checkbox.checked) {
            const recipeItem = checkbox.closest('.recipe-item');
            recipeItem.classList.add('border-orange-500', 'bg-orange-50');
        }
    });
    
    if (featuredCheckbox) {
        featuredCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // التحقق من وجود ورشة مميزة أخرى
                fetch(`/admin/workshops/check-featured?exclude=${workshopId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.hasFeatured) {
                            if (!confirm('يوجد ورشة مميزة حالياً. هل تريد جعل هذه الورشة هي الورشة المميزة الجديدة؟ سيتم إلغاء تمييز الورشة السابقة.')) {
                                this.checked = false;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    }
    
    // التحقق قبل إرسال النموذج
    form.addEventListener('submit', function(e) {
        if (featuredCheckbox && featuredCheckbox.checked) {
            // التحقق مرة أخرى قبل الإرسال
            fetch(`/admin/workshops/check-featured?exclude=${workshopId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.hasFeatured) {
                        if (!confirm('يوجد ورشة مميزة حالياً. هل تريد جعل هذه الورشة هي الورشة المميزة الجديدة؟ سيتم إلغاء تمييز الورشة السابقة.')) {
                            e.preventDefault();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
        
        // إظهار رسالة تحميل
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الحفظ...';
        }
    });
});

// Image upload handling
function handleImageUpload(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('نوع الملف غير مدعوم. يرجى اختيار صورة JPG, PNG, GIF أو WebP', 'error');
        input.value = '';
        return;
    }
    
    // Validate file size (5MB limit)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
        showNotification('حجم الصورة يجب أن يكون أقل من 5 ميجابايت', 'error');
        input.value = '';
        return;
    }
    
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('preview-img');
            const imagePreview = document.getElementById('image-preview');
            const imageInfo = document.getElementById('image-info');
            const imageUploadArea = document.getElementById('image-upload-area');
            const currentImagePreview = document.getElementById('current-image-preview');
            
            previewImg.src = e.target.result;
            imagePreview.classList.remove('hidden');
            imageUploadArea.classList.add('has-image');
            
            // Hide current image section if it exists
            if (currentImagePreview && currentImagePreview.closest('.mb-6')) {
                currentImagePreview.closest('.mb-6').style.display = 'none';
            }
            
            // Show file info
            const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
            imageInfo.textContent = `الملف: ${file.name} (${fileSizeMB} MB)`;
            
            showNotification('تم تحميل الصورة بنجاح', 'success');
        };
    
    reader.readAsDataURL(file);
}

// Remove image preview
function removeImagePreview() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const imageUploadArea = document.getElementById('image-upload-area');
    const currentImagePreview = document.getElementById('current-image-preview');
    
    imageInput.value = '';
    imagePreview.classList.add('hidden');
    imageUploadArea.classList.remove('has-image');
    
    // Show current image section again if it exists
    if (currentImagePreview && currentImagePreview.closest('.mb-6')) {
        currentImagePreview.closest('.mb-6').style.display = 'block';
    }
    
    showNotification('تم حذف الصورة المحددة', 'success');
}

// Show notification
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>
@endsection
