@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    /** @var \App\Models\Workshop|null $workshop */
    $workshop = $workshop ?? null;
    $levels = [
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'advanced' => 'متقدم',
    ];
    $currencies = [
        'JOD' => 'دينار أردني',
    ];
    $isOnline = old('is_online', $workshop->is_online ?? true);
    $autoGenerateMeeting = old(
        'auto_generate_meeting',
        $workshop
            ? (($workshop->meeting_provider ?? null) === 'jitsi')
            : 1
    );
    $startDateValue = old('start_date', optional(optional($workshop)->start_date)->format('Y-m-d\TH:i'));
    $endDateValue = old('end_date', optional(optional($workshop)->end_date)->format('Y-m-d\TH:i'));
    $deadlineValue = old('registration_deadline', optional(optional($workshop)->registration_deadline)->format('Y-m-d\TH:i'));
    $coverImageUrl = null;
    if ($workshop && $workshop->image) {
        $coverImageUrl = Str::startsWith($workshop->image, ['http://', 'https://'])
            ? $workshop->image
            : Storage::disk('public')->url($workshop->image);
    }

    $currentUser = auth()->user();
    $canManageMeetingLinks = $currentUser && method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin();

    if (!$canManageMeetingLinks) {
        $autoGenerateMeeting = 1;
    }
@endphp

<div class="space-y-10">
    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">المعلومات الأساسية</p>
            <h2 class="mt-1 text-2xl font-bold text-slate-900">تفاصيل الورشة</h2>
            <p class="mt-2 text-sm text-slate-500">عرّف المتعلمين على مضمون الورشة وسبب تميزها.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
            <div class="space-y-3">
                <label for="title" class="text-sm font-semibold text-slate-700">عنوان الورشة *</label>
                <input type="text" id="title" name="title" required
                       value="{{ old('title', $workshop->title ?? '') }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('title') border-red-400 focus:ring-red-200 @enderror">
                @error('title')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-3">
                <label for="category" class="text-sm font-semibold text-slate-700">فئة الورشة *</label>
                <input type="text" id="category" name="category" required placeholder="مثل: مخبوزات، أطباق رئيسية، حلويات..."
                       value="{{ old('category', $workshop->category ?? '') }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('category') border-red-400 focus:ring-red-200 @enderror">
                @error('category')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-3">
                <label for="level" class="text-sm font-semibold text-slate-700">المستوى *</label>
                <select id="level" name="level" required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:ring-4 focus:ring-orange-100 @error('level') border-red-400 focus:ring-red-200 @enderror">
                    @foreach ($levels as $value => $label)
                        <option value="{{ $value }}" @selected(old('level', $workshop->level ?? 'beginner') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('level')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-3">
                <label for="duration" class="text-sm font-semibold text-slate-700">المدة (بالدقائق) *</label>
                <input type="number" id="duration" name="duration" required min="30" max="600" step="15"
                       value="{{ old('duration', $workshop->duration ?? 90) }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('duration') border-red-400 focus:ring-red-200 @enderror">
                @error('duration')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-5 space-y-3">
            <label for="description" class="text-sm font-semibold text-slate-700">وصف ملهم *</label>
            <textarea id="description" name="description" rows="4" required
                      class="w-full rounded-3xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('description') border-red-400 focus:ring-red-200 @enderror"
                      placeholder="حدثنا عن أهداف الورشة، أسلوبك في الشرح، والقيمة التي سيخرج بها المشاركون.">{{ old('description', $workshop->description ?? '') }}</textarea>
            @error('description')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <div class="space-y-3">
                <label for="content" class="text-sm font-semibold text-slate-700">تفاصيل المحتوى (اختياري)</label>
                <textarea id="content" name="content" rows="4"
                          class="w-full rounded-3xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">{{ old('content', $workshop->content ?? '') }}</textarea>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-3">
                    <label for="what_you_will_learn" class="text-sm font-semibold text-slate-700">ماذا سيتعلم المشاركون؟</label>
                    <textarea id="what_you_will_learn" name="what_you_will_learn" rows="3"
                              class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">{{ old('what_you_will_learn', $workshop->what_you_will_learn ?? '') }}</textarea>
                </div>
                <div class="space-y-3">
                    <label for="requirements" class="text-sm font-semibold text-slate-700">متطلبات مسبقة</label>
                    <textarea id="requirements" name="requirements" rows="3"
                              class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">{{ old('requirements', $workshop->requirements ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">التسعير والقدرة الاستيعابية</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">الإعدادات المالية</h2>
            </div>
            <div class="rounded-full bg-orange-50 px-4 py-2 text-sm font-medium text-orange-600">
                أخبر المشاركين بعدد المقاعد المتاحة لتشجيع التسجيل المبكر.
            </div>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            <div class="space-y-3">
                <label for="price" class="text-sm font-semibold text-slate-700">سعر الورشة *</label>
                <input type="number" id="price" name="price" min="0" step="0.5" required
                       value="{{ old('price', $workshop->price ?? 0) }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('price') border-red-400 focus:ring-red-200 @enderror">
                @error('price')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-3">
                <label for="currency" class="text-sm font-semibold text-slate-700">العملة *</label>
                <select id="currency" name="currency" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:ring-4 focus:ring-orange-100">
                    @foreach ($currencies as $value => $label)
                        <option value="{{ $value }}" @selected(old('currency', $workshop->currency ?? 'JOD') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-3">
                <label for="max_participants" class="text-sm font-semibold text-slate-700">الحد الأقصى للمشاركين *</label>
                <input type="number" id="max_participants" name="max_participants" min="1" max="500" required
                       value="{{ old('max_participants', $workshop->max_participants ?? 15) }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('max_participants') border-red-400 focus:ring-red-200 @enderror">
            </div>
            <div class="md:col-span-3">
                <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-sm text-amber-900">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-amber-500 shadow-inner">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="space-y-1">
                        <p class="font-semibold text-amber-900">هام:</p>
                        <p class="leading-relaxed">
                            يتم اقتطاع نسبة تتراوح بين <strong>25% – 30%</strong> لصالح منصّة وصفة عند إنشاء الورشة لتغطية بوابات الدفع، الدعم التقني والتسويق.
                            بعد خصم هذه النسبة يتم تحويل الصافي إليك خلال 7 أيام عمل من انتهاء الورشة.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">الجدولة</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">المواعيد</h2>
            </div>
            <p class="text-sm text-slate-500">تظهر هذه المواعيد للمتدربين فور نشر الورشة.</p>
        </div>
        <div class="grid gap-5 md:grid-cols-3">
            <div class="space-y-3">
                <label for="start_date" class="text-sm font-semibold text-slate-700">تاريخ البداية *</label>
                <input type="datetime-local" id="start_date" name="start_date" required
                       value="{{ $startDateValue }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('start_date') border-red-400 focus:ring-red-200 @enderror">
            </div>
            <div class="space-y-3">
                <label for="end_date" class="text-sm font-semibold text-slate-700">تاريخ النهاية *</label>
                <input type="datetime-local" id="end_date" name="end_date" required
                       value="{{ $endDateValue }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('end_date') border-red-400 focus:ring-red-200 @enderror">
            </div>
            <div class="space-y-3">
                <label for="registration_deadline" class="text-sm font-semibold text-slate-700">آخر موعد للتسجيل</label>
                <input type="datetime-local" id="registration_deadline" name="registration_deadline"
                       value="{{ $deadlineValue }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100 @error('registration_deadline') border-red-400 focus:ring-red-200 @enderror">
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">طريقة التقديم</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">أونلاين عبر Jitsi</h2>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-2 text-emerald-700">
                <i class="fas fa-video text-lg"></i>
                <span class="text-sm font-medium">نوفر لك توليد رابط اجتماع Jitsi تلقائياً</span>
            </div>
        </div>
        <div class="space-y-6">
            <div class="flex flex-wrap items-center gap-4">
                <input type="hidden" name="is_online" value="0">
                <label class="inline-flex items-center gap-3">
                    <input type="checkbox" name="is_online" id="is_online" value="1" class="toggle-input" @checked($isOnline)>
                    <span class="font-semibold text-slate-800">هذه الورشة أونلاين</span>
                </label>
                <span class="text-sm text-slate-500">يمكنك تبديلها لورشة حضورية في أي وقت.</span>
            </div>

            <div id="onlineFields" class="{{ $isOnline ? '' : 'hidden' }} space-y-5 rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                @if ($canManageMeetingLinks)
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="hidden" name="auto_generate_meeting" value="0">
                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                            <input type="checkbox" name="auto_generate_meeting" id="auto_generate_meeting" value="1" @checked($autoGenerateMeeting)>
                            توليد رابط Jitsi تلقائياً عند الحفظ
                        </label>
                        <button type="button" id="generateJitsiLinkBtn"
                                data-url="{{ route('chef.workshops.generate-link') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                            <i class="fas fa-bolt"></i>
                            توليد رابط الآن
                        </button>
                    </div>
                    <div class="space-y-2">
                        <label for="meeting_link" class="text-sm font-semibold text-slate-700">رابط الاجتماع</label>
                        <input type="url" id="meeting_link" name="meeting_link"
                               value="{{ $canManageMeetingLinks ? old('meeting_link', $workshop->meeting_link ?? '') : '' }}"
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-inner focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 @error('meeting_link') border-red-400 focus:ring-red-200 @enderror"
                               placeholder="https://meet.jit.si/wasfah-room" {{ $autoGenerateMeeting ? 'disabled' : '' }}>
                        <p id="meetingLinkHint" class="text-xs text-slate-500">
                            {{ $autoGenerateMeeting ? 'سيتم تعيين الرابط تلقائياً بعد الحفظ.' : 'يمكنك لصق رابط اجتماع جاهز إن رغبت.' }}
                        </p>
                        @error('meeting_link')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div id="generatedMeetingInfo" class="space-y-2 text-sm text-emerald-700">
                        @if (($workshop->meeting_provider ?? null) === 'jitsi' && $workshop->meeting_link)
                            <div class="rounded-2xl bg-white/70 p-3 text-emerald-700 shadow-inner">
                                <p class="font-semibold">تم تهيئة رابط Jitsi:</p>
                                <p class="truncate text-sm">{{ $workshop->meeting_link }}</p>
                                @if ($workshop->jitsi_passcode)
                                    <p class="mt-1 text-xs text-slate-500">رمز الدخول: {{ $workshop->jitsi_passcode }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <input type="hidden" name="auto_generate_meeting" id="auto_generate_meeting" value="1">
                    <div class="rounded-2xl border border-white/40 bg-white/80 px-4 py-3 text-sm text-slate-600 shadow-inner">
                        <p class="font-semibold text-slate-800">الرابط يُدار من فريق وصفة</p>
                        <p class="mt-1 text-xs text-slate-500">
                            سنقوم بتوليد رابط الاجتماع وتأمينه تلقائياً بعد حفظ الورشة، ولن يظهر الرابط الخام في لوحة الشيف حفاظاً على السرية.
                        </p>
                    </div>
                @endif
            </div>

            <div id="offlineFields" class="{{ $isOnline ? 'hidden' : '' }} space-y-4 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <div class="space-y-2">
                    <label for="location" class="text-sm font-semibold text-slate-700">الموقع *</label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location', $workshop->location ?? '') }}"
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-inner focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
                </div>
                <div class="space-y-2">
                    <label for="address" class="text-sm font-semibold text-slate-700">العنوان التفصيلي</label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-inner focus:border-slate-400 focus:ring-4 focus:ring-slate-100">{{ old('address', $workshop->address ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">معلوماتك</p>
            <h2 class="mt-1 text-2xl font-bold text-slate-900">تعريف بالمدرب</h2>
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div class="space-y-3">
                <label for="instructor" class="text-sm font-semibold text-slate-700">اسم المدرب</label>
                <input type="text" id="instructor" name="instructor"
                       value="{{ old('instructor', $workshop->instructor ?? auth()->user()->name) }}"
                       class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">
            </div>
            <div class="space-y-3">
                <label for="instructor_bio" class="text-sm font-semibold text-slate-700">نبذة قصيرة</label>
                <textarea id="instructor_bio" name="instructor_bio" rows="3"
                          class="w-full rounded-2xl border border-slate-200/80 bg-slate-50 px-4 py-3 text-slate-900 shadow-inner focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">{{ old('instructor_bio', $workshop->instructor_bio ?? auth()->user()->chef_specialty_description) }}</textarea>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">صورة الورشة</p>
            <h2 class="mt-1 text-2xl font-bold text-slate-900">إبهار بصري</h2>
            <p class="mt-1 text-sm text-slate-500">صور بدقة جيدة (حتى 5MB) لتحفيز الحجز.</p>
        </div>
        <div class="grid gap-5 lg:grid-cols-[2fr,1fr]">
            <div class="space-y-3">
                <label for="image" class="text-sm font-semibold text-slate-700">رفع صورة رئيسية</label>
                <input type="file"
                       id="image"
                       name="image"
                       accept="image/*"
                       class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500 focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                       data-max-size="5120"
                       data-max-size-message="لا يمكن رفع صورة أكبر من 5 ميجابايت."
                       data-error-target="#chef_workshop_image_error">
                <p id="chef_workshop_image_error" class="text-sm text-red-600 mt-2 hidden"></p>
                @error('image')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if ($workshop && $workshop->image)
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remove_image" value="1">
                        حذف الصورة الحالية
                    </label>
                @endif
            </div>
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3 text-center">
                @if ($coverImageUrl)
                    <img src="{{ $coverImageUrl }}" alt="Workshop cover" class="mx-auto h-40 w-full rounded-2xl object-cover">
                @else
                    <div class="flex h-40 flex-col items-center justify-center text-slate-400">
                        <i class="fas fa-image text-3xl"></i>
                        <p class="mt-2 text-sm">ستظهر المعاينة هنا بعد رفع الصورة.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">نشر الورشة</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">جاهزة للظهور؟</h2>
            </div>
            <label class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $workshop->is_active ?? false))>
                تفعيل الورشة فوراً بعد الحفظ
            </label>
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isOnlineInput = document.getElementById('is_online');
        const onlineFields = document.getElementById('onlineFields');
        const offlineFields = document.getElementById('offlineFields');
        const autoGenerateInput = document.getElementById('auto_generate_meeting');
        const meetingLinkInput = document.getElementById('meeting_link');
        const meetingHint = document.getElementById('meetingLinkHint');
        const generateBtn = document.getElementById('generateJitsiLinkBtn');
        const generatedInfo = document.getElementById('generatedMeetingInfo');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function toggleModeFields() {
            const isOnline = !!isOnlineInput?.checked;
            if (onlineFields) {
                onlineFields.classList.toggle('hidden', !isOnline);
            }
            if (offlineFields) {
                offlineFields.classList.toggle('hidden', isOnline);
            }
        }

        function toggleMeetingInputState() {
            if (!meetingLinkInput || !autoGenerateInput) {
                return;
            }
            const shouldDisable = autoGenerateInput.checked;
            meetingLinkInput.disabled = shouldDisable;
            if (meetingHint) {
                meetingHint.textContent = shouldDisable
                    ? 'سيتم تعيين الرابط تلقائياً بعد الحفظ.'
                    : 'يمكنك لصق رابط اجتماع جاهز إن رغبت.';
            }
        }

        isOnlineInput?.addEventListener('change', toggleModeFields);
        autoGenerateInput?.addEventListener('change', toggleMeetingInputState);
        toggleModeFields();
        toggleMeetingInputState();

        generateBtn?.addEventListener('click', async () => {
            if (!csrfToken) return;
            const title = document.getElementById('title')?.value;
            const startDate = document.getElementById('start_date')?.value;

            if (!title) {
                alert('يرجى إدخال عنوان الورشة أولاً.');
                return;
            }

            generateBtn.disabled = true;
            generateBtn.classList.add('opacity-70');
            try {
                const response = await fetch(generateBtn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title,
                        start_date: startDate,
                    }),
                });

                if (!response.ok) {
                    throw new Error('تعذر توليد الرابط حالياً.');
                }

                const data = await response.json();
                if (meetingLinkInput) {
                    meetingLinkInput.value = data.meeting_link;
                }

                if (generatedInfo) {
                    generatedInfo.innerHTML = `
                        <div class="rounded-2xl bg-white/70 p-3 text-emerald-700 shadow-inner">
                            <p class="font-semibold">تم إنشاء رابط Jitsi جاهز:</p>
                            <p class="mt-1 truncate text-sm">${data.meeting_link}</p>
                            ${data.passcode ? `<p class="mt-1 text-xs text-slate-500">رمز الدخول: ${data.passcode}</p>` : ''}
                        </div>
                    `;
                }

                if (autoGenerateInput) {
                    autoGenerateInput.checked = true;
                }
                toggleMeetingInputState();
            } catch (error) {
                alert(error.message || 'حدث خطأ غير متوقع.');
            } finally {
                generateBtn.disabled = false;
                generateBtn.classList.remove('opacity-70');
            }
        });
    });
</script>
@endpush
