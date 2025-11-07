<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroSlideRequest;
use App\Models\HeroSlide;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeroSlideController extends Controller
{
    public function index()
    {
        if (!HeroSlide::exists()) {
            $this->seedDefaultSlides();
        }

        $slides = HeroSlide::orderBy('sort_order')->orderBy('id')->get();
        $activeCount = $slides->where('is_active', true)->count();

        return view('admin.hero-slides.index', compact('slides', 'activeCount'));
    }

    public function create()
    {
        $heroSlide = new HeroSlide([
            'is_active' => true,
            'features' => [''],
            'actions' => [['behavior' => 'static']],
        ]);

        return view('admin.hero-slides.create', compact('heroSlide'));
    }

    public function store(HeroSlideRequest $request)
    {
        $payload = $this->preparePayload($request);

        HeroSlide::create($payload);

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'تم إنشاء شريحة الهيرو بنجاح.');
    }

    public function initializeDefaults()
    {
        if (HeroSlide::exists()) {
            return redirect()
                ->route('admin.hero-slides.index')
                ->with('error', 'يوجد شرائح مسجلة بالفعل. يمكنك حذفها ثم إعادة الاستيراد إذا لزم الأمر.');
        }

        $this->seedDefaultSlides();

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'تم استيراد الشرائح الافتراضية ويمكنك تعديلها الآن.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.edit', compact('heroSlide'));
    }

    public function update(HeroSlideRequest $request, HeroSlide $heroSlide)
    {
        $payload = $this->preparePayload($request, $heroSlide);

        $heroSlide->update($payload);

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'تم تحديث شريحة الهيرو بنجاح.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        $this->deleteImage($heroSlide->desktop_image_path);
        $this->deleteImage($heroSlide->mobile_image_path);

        $heroSlide->delete();

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'تم حذف الشريحة بنجاح.');
    }

    public function toggleStatus(HeroSlide $heroSlide)
    {
        $heroSlide->is_active = ! $heroSlide->is_active;
        $heroSlide->save();

        return redirect()
            ->back()
            ->with('success', 'تم تحديث حالة الشريحة.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'min:0', 'max:1000'],
        ]);

        foreach ($validated['order'] as $slideId => $position) {
            HeroSlide::where('id', $slideId)->update(['sort_order' => $position]);
        }

        return redirect()
            ->back()
            ->with('success', 'تم تحديث ترتيب الشرائح.');
    }

    protected function preparePayload(HeroSlideRequest $request, ?HeroSlide $heroSlide = null): array
    {
        $payload = [
            'badge' => $request->filled('badge') ? $request->input('badge') : null,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image_alt' => $request->input('image_alt'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('sort_order')) {
            $payload['sort_order'] = (int) $request->input('sort_order');
        } else {
            $payload['sort_order'] = $heroSlide?->sort_order ?? ((HeroSlide::max('sort_order') ?? 0) + 1);
        }

        $payload['features'] = $this->sanitizeFeatures($request->input('features', []));
        $payload['actions'] = $this->sanitizeActions($request->input('actions', []));

        $payload['desktop_image_path'] = $this->resolveImagePath(
            $request,
            'desktop_image',
            $heroSlide?->desktop_image_path
        );

        $payload['mobile_image_path'] = $this->resolveImagePath(
            $request,
            'mobile_image',
            $heroSlide?->mobile_image_path
        );

        return $payload;
    }

    protected function sanitizeFeatures(array $features): array
    {
        return collect($features)
            ->map(fn ($feature) => is_string($feature) ? trim($feature) : null)
            ->filter(fn ($feature) => filled($feature))
            ->values()
            ->all();
    }

    protected function sanitizeActions(array $actions): array
    {
        return collect($actions)
            ->map(function ($action) {
                return [
                    'label' => $action['label'] ?? null,
                    'url' => $action['url'] ?? null,
                    'icon' => $action['icon'] ?? null,
                    'type' => $action['type'] ?? 'primary',
                    'behavior' => $action['behavior'] ?? 'static',
                    'open_in_new_tab' => !empty($action['open_in_new_tab']),
                ];
            })
            ->filter(function ($action) {
                if (in_array($action['behavior'], ['create_workshop', 'create_wasfah_link'], true)) {
                    return true;
                }

                return filled($action['label']) && filled($action['url']);
            })
            ->values()
            ->all();
    }

    protected function resolveImagePath(HeroSlideRequest $request, string $key, ?string $original): ?string
    {
        $removeKey = 'remove_' . $key;
        $urlKey = $key . '_url';

        if ($request->boolean($removeKey)) {
            $this->deleteImage($original);
            $original = null;
        }

        if ($request->hasFile($key)) {
            $this->deleteImage($original);
            return $request->file($key)->store('hero-slides', 'public');
        }

        if ($request->filled($urlKey)) {
            if ($original && ! $this->isRemotePath($original)) {
                $this->deleteImage($original);
            }
            return $request->input($urlKey);
        }

        return $original;
    }

    protected function deleteImage(?string $path): void
    {
        if (! $path || $this->isRemotePath($path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    protected function isRemotePath(string $path): bool
    {
        return Str::startsWith($path, ['http://', 'https://', '//']);
    }

    /**
     * أنشئ الشرائح الافتراضية المستخرجة من الهيرو الحالي.
     */
    protected function seedDefaultSlides(): void
    {
        DB::transaction(function () {
            foreach ($this->defaultBlueprints() as $index => $slide) {
                HeroSlide::create(array_merge($slide, [
                    'sort_order' => ($index + 1) * 10,
                    'is_active' => true,
                ]));
            }
        });
    }

    /**
     * الشرائح الافتراضية المستخرجة من الهيرو الحالي.
     */
    protected function defaultBlueprints(): array
    {
        return [
            [
                'badge' => 'ورشات العمل',
                'title' => 'ورشات حلويات احترافية',
                'description' => 'ورشات مباشرة بخطوات واضحة من شيفات مختصين.',
                'image_alt' => 'ورشة عمل للحلويات الاحترافية',
                'desktop_image_path' => asset('image/wterm.png'),
                'mobile_image_path' => asset('image/wterm.png'),
                'features' => [
                    'جلسات تفاعلية محدودة العدد',
                    'ملفات تطبيقية وشهادة حضور',
                ],
                'actions' => [
                    [
                        'behavior' => 'static',
                        'label' => 'استكشف الورشات',
                        'url' => route('workshops'),
                        'icon' => 'fas fa-chalkboard-teacher',
                        'type' => 'primary',
                    ],
                    [
                        'behavior' => 'static',
                        'label' => 'جدول الورشات',
                        'url' => route('workshops'),
                        'icon' => 'fas fa-calendar-alt',
                        'type' => 'secondary',
                    ],
                ],
            ],
            [
                'badge' => 'للشيفات',
                'title' => 'أنشئ ورشتك على وصفة',
                'description' => 'أطلق ورشتك الاحترافية مع نظام حجوزات مدمج وأدوات تسويق للشيفات.',
                'image_alt' => 'شيف يطلق ورشته الخاصة',
                'desktop_image_path' => asset('image/wterm.png'),
                'mobile_image_path' => asset('image/wterm.png'),
                'features' => [
                    'لوحة تحكم لإدارة الجلسات والمدفوعات',
                    'رابط تسجيل مباشر للمتدربين',
                    'دعم فني وخبراء يساعدونك في كل خطوة',
                ],
                'actions' => [
                    [
                        'behavior' => 'create_workshop',
                        'type' => 'accent',
                    ],
                ],
            ],
            [
                'badge' => 'Wasfah Links',
                'title' => 'Wasfah Links للشيفات',
                'description' => 'اجمع ورشاتك وروابطك المهمة في صفحة واحدة قابلة للمشاركة مع متابعيك.',
                'image_alt' => 'صفحة Wasfah Links التفاعلية',
                'desktop_image_path' => asset('image/wasfah-links.webm'),
                'mobile_image_path' => asset('image/wasfah-links.webm'),
                'features' => [
                    'صفحة مخصصة باسمك مع رابط قصير',
                    'تحكم كامل من لوحة الشيف لتحديث المحتوى فوراً',
                    'مثالية لمشاركتها على إنستغرام وواتساب',
                ],
                'actions' => [
                    [
                        'behavior' => 'create_wasfah_link',
                        'type' => 'primary',
                    ],
                    [
                        'behavior' => 'static',
                        'label' => 'استعرض Wasfah Links',
                        'url' => route('links'),
                        'icon' => 'fas fa-eye',
                        'type' => 'secondary',
                    ],
                ],
            ],
            [
                'badge' => 'أدوات الشيف',
                'title' => 'دليل أدوات الشيف',
                'description' => 'اختيارات دقيقة لأدوات تساعدك على الإتقان.',
                'image_alt' => 'مجموعة أدوات لتحضير الحلويات',
                'desktop_image_path' => asset('image/tnl.png'),
                'mobile_image_path' => asset('image/tnl.png'),
                'features' => [
                    'قوائم محدثة وروابط موثوقة',
                    'نصائح استخدام وصيانة مختصرة',
                ],
                'actions' => [
                    [
                        'behavior' => 'static',
                        'label' => 'استعرض أدوات الشيف',
                        'url' => route('tools'),
                        'icon' => 'fas fa-toolbox',
                        'type' => 'primary',
                    ],
                    [
                        'behavior' => 'static',
                        'label' => 'الأدوات المحفوظة',
                        'url' => route('saved.index'),
                        'icon' => 'fas fa-heart',
                        'type' => 'secondary',
                    ],
                ],
            ],
            [
                'badge' => 'الوصفات',
                'title' => 'مكتبة وصفات عالمية',
                'description' => 'وصفات فاخرة مجرَّبة مع شرح مصوَّر ونصائح مختصرة.',
                'image_alt' => 'حلى براونيز فاخرة',
                'desktop_image_path' => asset('image/Brownies.png'),
                'mobile_image_path' => asset('image/Brownies.png'),
                'features' => [
                    'تصنيفات حسب المستوى والمناسبة',
                    'حفظ ومزامنة وصفاتك المفضلة',
                ],
                'actions' => [
                    [
                        'behavior' => 'static',
                        'label' => 'ابدأ اكتشاف الوصفات',
                        'url' => route('recipes'),
                        'icon' => 'fas fa-utensils',
                        'type' => 'primary',
                    ],
                    [
                        'behavior' => 'static',
                        'label' => 'الوصفات المحفوظة',
                        'url' => route('saved.index'),
                        'icon' => 'fas fa-bookmark',
                        'type' => 'secondary',
                    ],
                ],
            ],
        ];
    }
}
