<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use App\Models\Workshop;
use App\Models\Recipe;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
        // جلب الورشة المميزة مع ضمان عرض آخر ورشة مميزة حتى إن لم تكن مستقبلية
        $featuredWorkshopQuery = Workshop::active()
            ->featured()
            ->withCount(['bookings' => function ($query) {
                $query->where('status', 'confirmed');
            }]);

        $featuredWorkshop = (clone $featuredWorkshopQuery)
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->first();

        if (!$featuredWorkshop) {
            $featuredWorkshop = $featuredWorkshopQuery
                ->orderBy('start_date', 'desc')
                ->first();
        }

        // التحقق من وجود ورشات قادمة على الإطلاق
        $hasUpcomingWorkshops = Workshop::active()
            ->upcoming()
            ->exists();

        // جلب أحدث الورشات النشطة (بما في ذلك المكتملة) - استبعاد الورشة المميزة إذا كانت موجودة
        $workshopsQuery = Workshop::active()
            ->withCount(['bookings' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->orderByDesc('start_date')
            ->orderByDesc('created_at');

        // استبعاد الورشة المميزة من قائمة الورشات العادية
        if ($featuredWorkshop) {
            $workshopsQuery->where('id', '!=', $featuredWorkshop->id);
        }

        $workshops = $workshopsQuery->limit(4)->get();

        // جلب أحدث الوصفات للعرض في الشريط الجانبي
        $latestRecipes = Recipe::approved()
            ->public()
            ->with(['category'])
            ->withCount(['interactions as saved_count' => function ($query) {
                $query->where('is_saved', true);
            }])
            ->withCount(['interactions as made_count' => function ($query) {
                $query->where('is_made', true);
            }])
            ->withCount(['interactions as interactions_count'])
            ->withAvg('interactions', 'rating')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // جلب الوصفات المميزة للعرض في القسم الرئيسي
        $featuredRecipes = Recipe::approved()
            ->public()
            ->with(['category'])
            ->withCount(['interactions as saved_count' => function ($query) {
                $query->where('is_saved', true);
            }])
            ->withCount(['interactions as made_count' => function ($query) {
                $query->where('is_made', true);
            }])
            ->withCount(['interactions as interactions_count'])
            ->withAvg('interactions', 'rating')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $authenticatedUser = auth()->user();

        // إضافة حالة الحفظ للمستخدمين المسجلين
        if ($authenticatedUser) {
            $user = $authenticatedUser;
            $savedRecipes = $user->interactions()
                ->where('is_saved', true)
                ->pluck('recipe_id')
                ->toArray();

            $latestRecipes->each(function ($recipe) use ($savedRecipes) {
                $recipe->is_saved = in_array($recipe->recipe_id, $savedRecipes);
            });

            $featuredRecipes->each(function ($recipe) use ($savedRecipes) {
                $recipe->is_saved = in_array($recipe->recipe_id, $savedRecipes);
            });
        } else {
            $latestRecipes->each(function ($recipe) {
                $recipe->is_saved = false;
            });

            $featuredRecipes->each(function ($recipe) {
                $recipe->is_saved = false;
            });
        }

        $homeTools = Tool::active()->ordered()->limit(12)->get();

        $heroMedia = [
            'workshop' => $this->resolveWorkshopHeroMedia($featuredWorkshop, $workshops),
            'recipe' => $this->resolveRecipeHeroMedia($featuredRecipes, $latestRecipes),
            'tool' => $this->resolveToolHeroMedia(),
            'links' => $this->resolveLinksHeroMedia(),
        ];

        $isAuthenticated = (bool) $authenticatedUser;
        $isChefUser = $authenticatedUser?->isChef() ?? false;

        $createWorkshopAction = [
            'label' => $isChefUser ? 'أنشئ ورشتك الآن' : ($isAuthenticated ? 'أكمل ملفك كشيف وأنشئ ورشتك' : 'انضم كشيف وأنشئ ورشتك'),
            'url' => $isChefUser ? route('chef.workshops.create') : ($isAuthenticated ? route('onboarding.show') : route('login')),
            'icon' => $isChefUser ? 'fas fa-plus-circle' : 'fas fa-user-plus',
            'type' => 'accent',
            'open_in_new_tab' => false,
        ];

        $createWasfahLinkAction = [
            'label' => $isChefUser ? 'أنشئ Wasfah Link الآن' : ($isAuthenticated ? 'أكمل ملفك لتفعيل Wasfah Links' : 'سجل وابدأ Wasfah Links'),
            'url' => $isChefUser ? route('chef.links.edit') : ($isAuthenticated ? route('onboarding.show') : route('register')),
            'icon' => 'fas fa-link',
            'type' => 'primary',
            'open_in_new_tab' => false,
        ];

        $managedHeroSlides = HeroSlide::active()->ordered()->get();
        $heroSlides = $this->transformHeroSlides($managedHeroSlides, $heroMedia, $createWorkshopAction, $createWasfahLinkAction);

        $bookedWorkshopIds = [];

        if ($authenticatedUser) {
            $bookedWorkshopIds = $authenticatedUser
                ->workshopBookings()
                ->pluck('workshop_id')
                ->unique()
                ->values()
                ->all();
        }

        return view('home', compact(
            'workshops',
            'featuredWorkshop',
            'latestRecipes',
            'featuredRecipes',
            'hasUpcomingWorkshops',
            'heroMedia',
            'heroSlides',
            'homeTools',
            'bookedWorkshopIds'
        ));
    }

    /**
     * حدد صور شريحة الورش
     */
    protected function resolveWorkshopHeroMedia(?Workshop $featuredWorkshop, Collection $workshops): array
    {
        $candidates = collect([$featuredWorkshop])->merge($workshops)->filter();

        $withImages = $candidates->filter(function ($workshop) {
            return $workshop && !empty($workshop->image);
        });

        if ($withImages->isEmpty()) {
            $fallback = Workshop::active()->whereNotNull('image')->inRandomOrder()->first();
            if ($fallback) {
                $withImages = collect([$fallback]);
            }
        }

        $selected = $withImages->isNotEmpty() ? $withImages->random() : null;

        $desktop = $this->makeImageUrl(optional($selected)->image, asset('image/wterm.png'));
        $mobile = $desktop;

        if ($selected) {
            $gallery = collect($selected->images ?? [])->filter();
            if ($gallery->isNotEmpty()) {
                $mobile = $this->makeImageUrl($gallery->random(), $desktop);
            }
        }

        return [
            'desktop' => $desktop,
            'mobile' => $mobile,
        ];
    }

    /**
     * حدد صور شريحة الوصفات
     */
    protected function resolveRecipeHeroMedia(Collection $featuredRecipes, Collection $latestRecipes): array
    {
        $candidates = collect();

        if ($featuredRecipes->isNotEmpty()) {
            $candidates = $candidates->merge($featuredRecipes);
        }

        if ($latestRecipes->isNotEmpty()) {
            $candidates = $candidates->merge($latestRecipes);
        }

        if ($candidates->isEmpty()) {
            $randomRecipe = Recipe::approved()->public()->with(['category'])->inRandomOrder()->first();
            if ($randomRecipe) {
                $candidates = collect([$randomRecipe]);
            }
        }

        $selected = $candidates->isNotEmpty() ? $candidates->random() : null;

        $images = $selected ? collect($selected->getAllImages())->filter() : collect();

        $desktop = $images->isNotEmpty()
            ? $images->random()
            : $this->makeImageUrl(optional($selected)->image_url, asset('image/Brownies.png'));

        $mobile = $images->isNotEmpty()
            ? $images->random()
            : $desktop;

        return [
            'desktop' => $desktop,
            'mobile' => $mobile,
        ];
    }

    /**
     * حدد صور شريحة أدوات الشيف
     */
    protected function resolveToolHeroMedia(): array
    {
        $tool = Tool::active()->inRandomOrder()->first();

        $desktop = $tool ? $tool->image_url : asset('image/tnl.png');

        $gallery = $tool ? collect($tool->gallery_image_urls ?? [])->filter() : collect();

        $mobile = $gallery->isNotEmpty()
            ? $gallery->random()
            : $desktop;

        return [
            'desktop' => $desktop,
            'mobile' => $mobile,
        ];
    }

    /**
     * حدد صور شريحة Wasfah Links
     */
    protected function resolveLinksHeroMedia(): array
    {
        $fallback = asset('image/wasfah-links.webm');

        return [
            'desktop' => $fallback,
            'mobile' => $fallback,
        ];
    }

    /**
     * تهيئة رابط الصورة
     */
    protected function makeImageUrl(?string $path, string $fallback): string
    {
        if (!$path) {
            return $fallback;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    protected function transformHeroSlides(Collection $managedSlides, array $heroMedia, array $createWorkshopAction, array $createWasfahLinkAction): array
    {
        if ($managedSlides->isEmpty()) {
            return $this->defaultHeroSlides($heroMedia, $createWorkshopAction, $createWasfahLinkAction);
        }

        $fallbackDesktop = data_get($heroMedia, 'workshop.desktop', asset('image/wterm.png'));
        $fallbackMobile = data_get($heroMedia, 'workshop.mobile', $fallbackDesktop);

        return $managedSlides->map(function (HeroSlide $slide) use ($fallbackDesktop, $fallbackMobile, $createWorkshopAction, $createWasfahLinkAction) {
            $desktop = $slide->desktop_image_url ?? $fallbackDesktop;
            $mobile = $slide->mobile_image_url ?? $slide->desktop_image_url ?? $fallbackMobile;

            $features = collect($slide->features ?? [])->filter()->values()->all();

            $actions = collect($slide->actions ?? [])
                ->map(fn ($action) => $this->resolveHeroSlideAction($action, $createWorkshopAction, $createWasfahLinkAction))
                ->filter()
                ->values()
                ->all();

            return [
                'badge' => $slide->badge,
                'title' => $slide->title,
                'description' => $slide->description,
                'features' => $features,
                'image' => $desktop,
                'mobile_image' => $mobile,
                'image_alt' => $slide->image_alt ?: $slide->title,
                'actions' => $actions,
            ];
        })->all();
    }

    protected function defaultHeroSlides(array $heroMedia, array $createWorkshopAction, array $createWasfahLinkAction): array
    {
        return [
            [
                'badge' => 'ورشات العمل',
                'title' => 'ورشات حلويات احترافية',
                'description' => 'ورشات مباشرة بخطوات واضحة من شيفات مختصين.',
                'features' => [
                    'جلسات تفاعلية محدودة العدد',
                    'ملفات تطبيقية وشهادة حضور',
                ],
                'image' => data_get($heroMedia, 'workshop.desktop', asset('image/wterm.png')),
                'mobile_image' => data_get($heroMedia, 'workshop.mobile', data_get($heroMedia, 'workshop.desktop', asset('image/wterm.png'))),
                'image_alt' => 'ورشة عمل للحلويات الاحترافية',
                'actions' => [
                    [
                        'label' => 'استكشف الورشات',
                        'url' => route('workshops'),
                        'icon' => 'fas fa-chalkboard-teacher',
                        'type' => 'primary',
                        'open_in_new_tab' => false,
                    ],
                    [
                        'label' => 'جدول الورشات',
                        'url' => route('workshops'),
                        'icon' => 'fas fa-calendar-alt',
                        'type' => 'secondary',
                        'open_in_new_tab' => false,
                    ],
                ],
            ],
            [
                'badge' => 'للشيفات',
                'title' => 'أنشئ ورشتك على وصفة',
                'description' => 'أطلق ورشتك الاحترافية مع نظام حجوزات مدمج وأدوات تسويق مصممة للشيفات.',
                'features' => [
                    'لوحة تحكم لإدارة الجلسات والمدفوعات',
                    'رابط تسجيل مباشر للمتدربين',
                    'دعم فني وخبراء يساعدونك في كل خطوة',
                ],
                'image' => data_get($heroMedia, 'chef.desktop', data_get($heroMedia, 'workshop.desktop', asset('image/wterm.png'))),
                'mobile_image' => data_get($heroMedia, 'chef.mobile', data_get($heroMedia, 'chef.desktop', data_get($heroMedia, 'workshop.desktop', asset('image/wterm.png')))),
                'image_alt' => 'شيف يطلق ورشته الخاصة',
                'actions' => [
                    $createWorkshopAction,
                ],
            ],
            [
                'badge' => 'Wasfah Links',
                'title' => 'Wasfah Links للشيفات',
                'description' => 'اجمع ورشاتك وروابطك المهمة في صفحة واحدة قابلة للمشاركة مع متابعيك.',
                'features' => [
                    'صفحة مخصصة باسمك مع رابط قصير',
                    'تحكم كامل من لوحة الشيف لتحديث المحتوى فوراً',
                    'مثالية لمشاركتها على إنستغرام وواتساب',
                ],
                'image' => data_get($heroMedia, 'links.desktop', asset('image/wasfah-links.webm')),
                'mobile_image' => data_get($heroMedia, 'links.mobile', data_get($heroMedia, 'links.desktop', asset('image/wasfah-links.webm'))),
                'image_alt' => 'صفحة Wasfah Links للشيف',
                'actions' => [
                    $createWasfahLinkAction,
                    [
                        'label' => 'استعرض Wasfah Links',
                        'url' => route('links'),
                        'icon' => 'fas fa-eye',
                        'type' => 'secondary',
                        'open_in_new_tab' => false,
                    ],
                ],
            ],
            [
                'badge' => 'أدوات الشيف',
                'title' => 'دليل أدوات الشيف',
                'description' => 'اختيارات دقيقة لأدوات تساعدك على الإتقان.',
                'features' => [
                    'قوائم محدثة وروابط موثوقة',
                    'نصائح استخدام وصيانة مختصرة',
                ],
                'image' => data_get($heroMedia, 'tool.desktop', asset('image/tnl.png')),
                'mobile_image' => data_get($heroMedia, 'tool.mobile', data_get($heroMedia, 'tool.desktop', asset('image/tnl.png'))),
                'image_alt' => 'مجموعة أدوات لتحضير الحلويات',
                'actions' => [
                    [
                        'label' => 'استعرض أدوات الشيف',
                        'url' => route('tools'),
                        'icon' => 'fas fa-toolbox',
                        'type' => 'primary',
                        'open_in_new_tab' => false,
                    ],
                    [
                        'label' => 'الأدوات المحفوظة',
                        'url' => route('saved.index'),
                        'icon' => 'fas fa-heart',
                        'type' => 'secondary',
                        'open_in_new_tab' => false,
                    ],
                ],
            ],
            [
                'badge' => 'الوصفات',
                'title' => 'مكتبة وصفات عالمية',
                'description' => 'وصفات فاخرة مجرَّبة مع شرح مصوَّر ونصائح مختصرة.',
                'features' => [
                    'تصنيفات حسب المستوى والمناسبة',
                    'حفظ ومزامنة وصفاتك المفضلة',
                ],
                'image' => data_get($heroMedia, 'recipe.desktop', asset('image/Brownies.png')),
                'mobile_image' => data_get($heroMedia, 'recipe.mobile', data_get($heroMedia, 'recipe.desktop', asset('image/Brownies.png'))),
                'image_alt' => 'حلى براونيز فاخرة',
                'actions' => [
                    [
                        'label' => 'ابدأ اكتشاف الوصفات',
                        'url' => route('recipes'),
                        'icon' => 'fas fa-utensils',
                        'type' => 'primary',
                        'open_in_new_tab' => false,
                    ],
                    [
                        'label' => 'الوصفات المحفوظة',
                        'url' => route('saved.index'),
                        'icon' => 'fas fa-bookmark',
                        'type' => 'secondary',
                        'open_in_new_tab' => false,
                    ],
                ],
            ],
        ];
    }

    protected function resolveHeroSlideAction(array $action, array $createWorkshopAction, array $createWasfahLinkAction): ?array
    {
        $behavior = $action['behavior'] ?? 'static';

        if (in_array($behavior, ['create_workshop', 'create_wasfah_link'], true)) {
            $base = $behavior === 'create_workshop' ? $createWorkshopAction : $createWasfahLinkAction;

            if (!empty($action['label'])) {
                $base['label'] = $action['label'];
            }

            if (!empty($action['icon'])) {
                $base['icon'] = $action['icon'];
            }

            if (!empty($action['type'])) {
                $base['type'] = $action['type'];
            }

            if (!empty($action['url'])) {
                $base['url'] = $action['url'];
            }

            if (array_key_exists('open_in_new_tab', $action)) {
                $base['open_in_new_tab'] = (bool) $action['open_in_new_tab'];
            }

            return $base;
        }

        $label = $action['label'] ?? null;
        $url = $action['url'] ?? null;

        if (!$label || !$url) {
            return null;
        }

        return [
            'label' => $label,
            'url' => $url,
            'icon' => $action['icon'] ?? null,
            'type' => $action['type'] ?? 'primary',
            'open_in_new_tab' => (bool) ($action['open_in_new_tab'] ?? false),
        ];
    }
}
