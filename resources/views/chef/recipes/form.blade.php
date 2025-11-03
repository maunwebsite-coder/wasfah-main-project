@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use App\Models\Recipe;

    $isEdit = isset($recipe);
    $stepsInput = old('steps', $isEdit ? ($recipe->steps ?? []) : []);
    $stepsInput = is_array($stepsInput) ? array_values($stepsInput) : [];
    if (empty($stepsInput)) {
        $stepsInput = [''];
    }

    $ingredientsInput = old('ingredients', $isEdit ? ($recipe->ingredients->map(fn ($ingredient) => [
        'name' => $ingredient->name,
        'amount' => $ingredient->quantity,
    ])->toArray()) : []);
    $ingredientsInput = is_array($ingredientsInput) ? array_values($ingredientsInput) : [];
    if (empty($ingredientsInput)) {
        $ingredientsInput = [['name' => '', 'amount' => '']];
    }

    $ingredientsCount = count($ingredientsInput);

    $selectedTools = old('tools', $isEdit ? ($recipe->tools ?? []) : []);
    $selectedTools = is_array($selectedTools) ? array_map('intval', $selectedTools) : [];

    $visibilityValue = old(
        'visibility',
        $isEdit
            ? ($recipe->visibility ?? Recipe::VISIBILITY_PUBLIC)
            : Recipe::VISIBILITY_PUBLIC
    );
@endphp

<input type="hidden" name="submit_action" id="recipe-submit-action" value="draft">

<div class="space-y-8">
    <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">معلومات الوصفة الأساسية</h2>
                <p class="text-sm text-gray-500 mt-1">املأ تفاصيل الوصفة ليتمكن الفريق من مراجعتها بسرعة.</p>
            </div>
        </div>

        <div class="grid gap-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">عنوان الوصفة *</label>
                    <input type="text" id="title" name="title" required
                           value="{{ old('title', $isEdit ? $recipe->title : '') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('title')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">وصف مختصر للوصفة</label>
                    <textarea id="description" name="description" rows="5"
                              class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="شارك قصة الوصفة أو نصائح التقديم">{{ old('description', $isEdit ? $recipe->description : '') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">التصنيف</label>
                    <select id="category_id" name="category_id"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">اختر التصنيف</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->category_id }}"
                                {{ (string) old('category_id', $isEdit ? $recipe->category_id : '') === (string) $category->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">مستوى الصعوبة</label>
                    <select id="difficulty" name="difficulty"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">اختر المستوى</option>
                        <option value="easy" {{ old('difficulty', $isEdit ? $recipe->difficulty : '') === 'easy' ? 'selected' : '' }}>سهل</option>
                        <option value="medium" {{ old('difficulty', $isEdit ? $recipe->difficulty : '') === 'medium' ? 'selected' : '' }}>متوسط</option>
                        <option value="hard" {{ old('difficulty', $isEdit ? $recipe->difficulty : '') === 'hard' ? 'selected' : '' }}>صعب</option>
                    </select>
                    @error('difficulty')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">حالة الظهور</label>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-2 rounded-xl border {{ $visibilityValue === Recipe::VISIBILITY_PUBLIC ? 'border-orange-400 bg-orange-50 text-orange-700' : 'border-gray-200 bg-white text-gray-600' }} px-4 py-2 transition hover:border-orange-300 hover:bg-orange-50">
                            <input type="radio" name="visibility" value="{{ Recipe::VISIBILITY_PUBLIC }}" {{ $visibilityValue === Recipe::VISIBILITY_PUBLIC ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500">
                            <span class="text-sm font-semibold">عام</span>
                            <span class="text-xs text-gray-500">تظهر الوصفة لكل الزوار بعد اعتمادها</span>
                        </label>
                        <label class="flex items-center gap-2 rounded-xl border {{ $visibilityValue === Recipe::VISIBILITY_PRIVATE ? 'border-slate-400 bg-slate-50 text-slate-700' : 'border-gray-200 bg-white text-gray-600' }} px-4 py-2 transition hover:border-slate-300 hover:bg-slate-50">
                            <input type="radio" name="visibility" value="{{ Recipe::VISIBILITY_PRIVATE }}" {{ $visibilityValue === Recipe::VISIBILITY_PRIVATE ? 'checked' : '' }} class="text-slate-600 focus:ring-slate-500">
                            <span class="text-sm font-semibold">خاص</span>
                            <span class="text-xs text-gray-500">تظل الوصفة مخفية عن الزوار حتى وإن كانت معتمدة</span>
                        </label>
                    </div>
                    @error('visibility')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label for="prep_time" class="block text-sm font-medium text-gray-700 mb-2">مدة التحضير (بالدقائق)</label>
                    <input type="number" min="0" id="prep_time" name="prep_time"
                           value="{{ old('prep_time', $isEdit ? $recipe->prep_time : '') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('prep_time')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="cook_time" class="block text-sm font-medium text-gray-700 mb-2">مدة الطهي (بالدقائق)</label>
                    <input type="number" min="0" id="cook_time" name="cook_time"
                           value="{{ old('cook_time', $isEdit ? $recipe->cook_time : '') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('cook_time')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="servings" class="block text-sm font-medium text-gray-700 mb-2">عدد الحصص</label>
                    <input type="number" min="1" id="servings" name="servings"
                           value="{{ old('servings', $isEdit ? $recipe->servings : '') }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('servings')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">خطوات التحضير</h2>
                <p class="text-sm text-gray-500 mt-1">أضف خطوات واضحة ومتسلسلة حتى تكون الوصفة سهلة المتابعة.</p>
            </div>
            <button type="button" id="add-step-btn" class="inline-flex items-center gap-2 rounded-full border border-orange-200 px-4 py-2 text-sm font-medium text-orange-600 hover:bg-orange-50 transition">
                <i class="fas fa-plus"></i>
                إضافة خطوة
            </button>
        </div>

        <div id="steps-wrapper" class="space-y-4">
            @foreach ($stepsInput as $index => $step)
                <div class="step-item flex items-start gap-3 bg-orange-50 rounded-xl p-4">
                    <div class="flex-shrink-0">
                        <span class="step-number inline-flex h-8 w-8 items-center justify-center rounded-full bg-orange-500 text-white font-semibold">{{ $loop->iteration }}</span>
                    </div>
                    <div class="flex-1">
                        <textarea name="steps[]" rows="2" class="w-full rounded-xl border border-orange-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="وصف الخطوة">{{ $step }}</textarea>
                    </div>
                    <button type="button" class="remove-step text-sm text-red-500 hover:text-red-600">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            @endforeach
        </div>
        @error('steps')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </section>

    <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">المكونات</h2>
                <p class="text-sm text-gray-500 mt-1">أدرج المكونات والكميات بشكل واضح.</p>
            </div>
            <button type="button" id="add-ingredient-btn" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 px-4 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50 transition">
                <i class="fas fa-plus"></i>
                إضافة مكون
            </button>
        </div>

        <div id="ingredients-wrapper" class="space-y-4" data-next-index="{{ $ingredientsCount }}">
            @foreach ($ingredientsInput as $index => $ingredient)
                <div class="ingredient-item grid gap-4 md:grid-cols-12 bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-emerald-700 mb-2">اسم المكون</label>
                        <input type="text" name="ingredients[{{ $index }}][name]" value="{{ $ingredient['name'] ?? '' }}" class="w-full rounded-xl border border-emerald-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="مثال: دقيق متعدد الاستخدامات">
                    </div>
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-emerald-700 mb-2">الكمية</label>
                        <input type="text" name="ingredients[{{ $index }}][amount]" value="{{ $ingredient['amount'] ?? '' }}" class="w-full rounded-xl border border-emerald-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="مثال: كوبان">
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="button" class="remove-ingredient w-full rounded-xl border border-red-200 px-4 py-3 text-red-500 hover:bg-red-50 transition">
                            إزالة
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        @error('ingredients.*.name')
            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </section>

    <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">الأدوات المقترحة (اختياري)</h2>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($tools as $tool)
                <label class="flex items-center gap-4 rounded-xl border border-gray-200 px-4 py-3 hover:border-orange-300 transition">
                    <input type="checkbox" name="tools[]" value="{{ $tool->id }}"
                           {{ in_array((int) $tool->id, $selectedTools, true) ? 'checked' : '' }}
                           class="h-5 w-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-100">
                            <img src="{{ $tool->image_url }}" alt="{{ $tool->name }}" class="h-full w-full object-cover">
                        </div>
                        <span class="text-gray-700 text-sm font-medium truncate" title="{{ $tool->name }}">
                            {{ Str::limit($tool->name, 24) }}
                        </span>
                    </div>
                </label>
            @endforeach
        </div>
    </section>

    <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">صور الوصفة</h2>
                <p class="text-sm text-gray-500 mb-4">يمكنك رفع حتى 5 صور بجودة عالية لإبراز جمال الوصفة.</p>
                <div class="space-y-4">
                    @for ($i = 1; $i <= 5; $i++)
                        @php
                            $field = $i === 1 ? 'image' : 'image_' . $i;
                            $label = $i === 1 ? 'الصورة الرئيسية' : 'صورة إضافية ' . $i;
                        @endphp
                        <div class="border border-dashed border-gray-300 rounded-xl p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
                            <input type="file" name="{{ $field }}" accept="image/*"
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-full file:border-0 file:bg-orange-50 file:px-4 file:py-2 file:text-orange-600 hover:file:bg-orange-100">
                            @error($field)
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror

                            @if ($isEdit && $recipe->{$field})
                                <div class="mt-3 flex items-center gap-4">
                                    <img src="{{ Storage::disk('public')->url($recipe->{$field}) }}" alt="صورة الوصفة" class="h-20 w-20 rounded-lg object-cover border border-gray-200">
                                    <label class="inline-flex items-center gap-2 text-sm text-red-500">
                                        <input type="checkbox" name="remove_images[]" value="{{ $field }}" class="h-4 w-4 rounded border-gray-300 text-red-500 focus:ring-red-400">
                                        إزالة هذه الصورة
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100 h-fit">
                <h3 class="text-lg font-semibold text-orange-700 mb-4">رابط صورة خارجي (اختياري)</h3>
                <p class="text-sm text-orange-600 mb-4">إذا كان لديك رابط مباشر للصورة (مثل Google Drive أو Unsplash) يمكنك إضافته هنا.</p>
                <input type="url" name="image_url" value="{{ old('image_url', $isEdit ? $recipe->image_url : '') }}"
                       class="w-full rounded-xl border border-orange-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                       placeholder="https://example.com/your-image.jpg">
                @error('image_url')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const stepsWrapper = document.getElementById('steps-wrapper');
                const addStepBtn = document.getElementById('add-step-btn');
                const ingredientsWrapper = document.getElementById('ingredients-wrapper');
                const addIngredientBtn = document.getElementById('add-ingredient-btn');
                const submitActionInput = document.getElementById('recipe-submit-action');
                const draftButtons = document.querySelectorAll('[data-submit-action="draft"]');
                const submitButtons = document.querySelectorAll('[data-submit-action="submit"]');

                function refreshStepNumbers() {
                    stepsWrapper.querySelectorAll('.step-number').forEach((badge, index) => {
                        badge.textContent = index + 1;
                    });
                }

                if (addStepBtn) {
                    addStepBtn.addEventListener('click', function () {
                        const template = document.createElement('div');
                        template.className = 'step-item flex items-start gap-3 bg-orange-50 rounded-xl p-4';
                        template.innerHTML = `
                            <div class="flex-shrink-0">
                                <span class="step-number inline-flex h-8 w-8 items-center justify-center rounded-full bg-orange-500 text-white font-semibold">1</span>
                            </div>
                            <div class="flex-1">
                                <textarea name="steps[]" rows="2" class="w-full rounded-xl border border-orange-200 px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="وصف الخطوة"></textarea>
                            </div>
                            <button type="button" class="remove-step text-sm text-red-500 hover:text-red-600">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        `;
                        stepsWrapper.appendChild(template);
                        refreshStepNumbers();
                    });
                }

                stepsWrapper?.addEventListener('click', function (event) {
                    if (event.target.closest('.remove-step')) {
                        const items = stepsWrapper.querySelectorAll('.step-item');
                        if (items.length <= 1) {
                            items[0].querySelector('textarea').value = '';
                            return;
                        }
                        event.target.closest('.step-item').remove();
                        refreshStepNumbers();
                    }
                });

                if (addIngredientBtn) {
                    addIngredientBtn.addEventListener('click', function () {
                        const template = document.createElement('div');
                        template.className = 'ingredient-item grid gap-4 md:grid-cols-12 bg-emerald-50 p-4 rounded-xl border border-emerald-100';
                        const nextIndex = parseInt(ingredientsWrapper.dataset.nextIndex ?? '0', 10);
                        template.innerHTML = `
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-emerald-700 mb-2">اسم المكون</label>
                                <input type="text" name="ingredients[${nextIndex}][name]" class="w-full rounded-xl border border-emerald-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="مثال: دقيق متعدد الاستخدامات">
                            </div>
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-emerald-700 mb-2">الكمية</label>
                                <input type="text" name="ingredients[${nextIndex}][amount]" class="w-full rounded-xl border border-emerald-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="مثال: كوبان">
                            </div>
                            <div class="md:col-span-2 flex items-end">
                                <button type="button" class="remove-ingredient w-full rounded-xl border border-red-200 px-4 py-3 text-red-500 hover:bg-red-50 transition">
                                    إزالة
                                </button>
                            </div>
                        `;
                        ingredientsWrapper.appendChild(template);
                        ingredientsWrapper.dataset.nextIndex = nextIndex + 1;
                    });
                }

                ingredientsWrapper?.addEventListener('click', function (event) {
                    if (event.target.closest('.remove-ingredient')) {
                        const items = ingredientsWrapper.querySelectorAll('.ingredient-item');
                        if (items.length <= 1) {
                            items[0].querySelectorAll('input').forEach((input) => input.value = '');
                            return;
                        }
                        event.target.closest('.ingredient-item').remove();
                    }
                });

                function setSubmitAction(action) {
                    if (submitActionInput) {
                        submitActionInput.value = action;
                    }
                }

                draftButtons.forEach((btn) => {
                    btn.addEventListener('click', function () {
                        setSubmitAction('draft');
                    });
                });

                submitButtons.forEach((btn) => {
                    btn.addEventListener('click', function () {
                        setSubmitAction('submit');
                    });
                });

                // Set default action to draft on load
                setSubmitAction('draft');
            });
        </script>
    @endpush
@endonce
