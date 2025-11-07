<?php

namespace App\Helpers;

use App\Models\Category;
use Illuminate\Support\Facades\Route;

class Breadcrumbs
{
    /**
     * Build breadcrumb trail for the current route.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    public static function generate(): array
    {
        $route = Route::current();

        if (!$route) {
            return [];
        }

        $routeName = $route->getName();

        if (!$routeName || in_array($routeName, ['home'], true)) {
            return [];
        }

        $breadcrumbs = [
            [
                'label' => 'الرئيسية',
                'url' => route('home'),
            ],
        ];

        if (str_starts_with($routeName, 'admin.')) {
            $breadcrumbs = array_merge(
                $breadcrumbs,
                self::adminTrail($routeName)
            );
        } elseif (str_starts_with($routeName, 'chef.')) {
            $breadcrumbs = array_merge(
                $breadcrumbs,
                self::chefTrail($routeName)
            );
        } else {
            switch ($routeName) {
                case 'recipes':
                    $breadcrumbs = array_merge(
                        $breadcrumbs,
                        self::recipesTrail()
                    );
                    break;

                case 'recipe.show':
                    $breadcrumbs = array_merge(
                        $breadcrumbs,
                        self::recipeTrail()
                    );
                    break;

                case 'tools':
                    $breadcrumbs = array_merge(
                        $breadcrumbs,
                        self::toolsTrail()
                    );
                    break;

                case 'tools.show':
                    $breadcrumbs = array_merge(
                        $breadcrumbs,
                        self::toolDetailsTrail()
                    );
                    break;

                case 'workshops':
                case 'workshops.search':
                    $breadcrumbs = array_merge(
                        $breadcrumbs,
                        self::workshopsTrail($routeName === 'workshops.search')
                    );
                    break;

                case 'workshop.show':
                    $breadcrumbs = array_merge(
                        $breadcrumbs,
                        self::workshopTrail()
                    );
                    break;

                case 'search':
                    $breadcrumbs[] = [
                        'label' => 'نتائج البحث',
                        'url' => null,
                    ];
                    break;

                case 'saved.index':
                    $breadcrumbs[] = [
                        'label' => 'الأدوات المحفوظة',
                        'url' => null,
                    ];
                    break;

                case 'profile':
                    $breadcrumbs[] = [
                        'label' => 'الملف الشخصي',
                        'url' => null,
                    ];
                    break;

                case 'about':
                    $breadcrumbs[] = [
                        'label' => 'عن وصفة',
                        'url' => null,
                    ];
                    break;

                case 'contact':
                    $breadcrumbs[] = [
                        'label' => 'اتصل بنا',
                        'url' => null,
                    ];
                    break;

                case 'advertising':
                    $breadcrumbs[] = [
                        'label' => 'الإعلان',
                        'url' => null,
                    ];
                    break;

                case 'baking-tips':
                    $breadcrumbs[] = [
                        'label' => 'نصائح الخَبز',
                        'url' => null,
                    ];
                    break;

                default:
                    $breadcrumbs[] = [
                        'label' => self::titleCaseLastSegment($routeName),
                        'url' => null,
                    ];
                    break;
            }
        }

        $breadcrumbs = self::collapseConsecutiveDuplicates($breadcrumbs);

        if (count($breadcrumbs) <= 1) {
            return [];
        }

        // Ensure only the last breadcrumb is non-clickable.
        $lastIndex = count($breadcrumbs) - 1;
        foreach ($breadcrumbs as $index => &$breadcrumb) {
            if ($index === $lastIndex) {
                $breadcrumb['url'] = null;
            }
        }
        unset($breadcrumb);

        return $breadcrumbs;
    }

    /**
     * Breadcrumbs for the recipes listing.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function recipesTrail(): array
    {
        $trail = [
            [
                'label' => 'الوصفات',
                'url' => null,
            ],
        ];

        $categoryId = request()->get('category');

        if ($categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $trail[0]['url'] = route('recipes');

                $trail[] = [
                    'label' => $category->name,
                    'url' => null,
                ];
            }
        }

        return $trail;
    }

    /**
     * Breadcrumbs for a single recipe page.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function recipeTrail(): array
    {
        $trail = [
            [
                'label' => 'الوصفات',
                'url' => route('recipes'),
            ],
        ];

        $recipe = request()->route('recipe');

        if ($recipe && $recipe->category) {
            $trail[] = [
                'label' => $recipe->category->name,
                'url' => route('recipes', ['category' => $recipe->category->id]),
            ];
        }

        if ($recipe) {
            $trail[] = [
                'label' => $recipe->title,
                'url' => null,
            ];
        }

        return $trail;
    }

    /**
     * Breadcrumbs for the tools listing.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function toolsTrail(): array
    {
        return [
            [
                'label' => 'أدوات الطبخ',
                'url' => null,
            ],
        ];
    }

    /**
     * Breadcrumbs for a single tool page.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function toolDetailsTrail(): array
    {
        $trail = [
            [
                'label' => 'أدوات الطبخ',
                'url' => route('tools'),
            ],
        ];

        $tool = request()->route('tool');
        if ($tool && isset($tool->name)) {
            $trail[] = [
                'label' => $tool->name,
                'url' => null,
            ];
        }

        return $trail;
    }

    /**
     * Breadcrumbs for the workshops listing or search.
     *
     * @param bool $isSearch
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function workshopsTrail(bool $isSearch = false): array
    {
        $trail = [
            [
                'label' => 'ورشات العمل',
                'url' => $isSearch ? route('workshops') : null,
            ],
        ];

        if ($isSearch) {
            $trail[] = [
                'label' => 'نتائج البحث',
                'url' => null,
            ];
        }

        return $trail;
    }

    /**
     * Breadcrumbs for a single workshop page.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function workshopTrail(): array
    {
        $trail = [
            [
                'label' => 'ورشات العمل',
                'url' => route('workshops'),
            ],
        ];

        $workshop = request()->route('workshop');
        if ($workshop && isset($workshop->title)) {
            $trail[] = [
                'label' => $workshop->title,
                'url' => null,
            ];
        }

        return $trail;
    }

    /**
     * Build breadcrumbs for admin routes.
     */
    protected static function adminTrail(string $routeName): array
    {
        $trail = [
            [
                'label' => 'منطقة الإدمن',
                'url' => self::routeUrl('admin.dashboard'),
            ],
        ];

        $segments = explode('.', $routeName);
        array_shift($segments); // Remove "admin"

        if (empty($segments)) {
            return $trail;
        }

        $sectionKey = array_shift($segments);
        $sectionBreadcrumb = self::sectionBreadcrumb($sectionKey, self::adminSections());

        if ($sectionBreadcrumb) {
            $trail[] = $sectionBreadcrumb;
        }

        if (!empty($segments)) {
            $trail = array_merge($trail, self::adminActionTrail($sectionKey, $segments));
        }

        return $trail;
    }

    /**
     * Build breadcrumbs for chef routes.
     */
    protected static function chefTrail(string $routeName): array
    {
        $trail = [
            [
                'label' => 'منطقة الشيف',
                'url' => self::routeUrl('chef.dashboard'),
            ],
        ];

        $segments = explode('.', $routeName);
        array_shift($segments); // Remove "chef"

        if (empty($segments)) {
            return $trail;
        }

        $sectionKey = array_shift($segments);
        $sectionBreadcrumb = self::sectionBreadcrumb($sectionKey, self::chefSections());

        if ($sectionBreadcrumb) {
            $trail[] = $sectionBreadcrumb;
        }

        if (!empty($segments)) {
            $trail = array_merge($trail, self::chefActionTrail($sectionKey, $segments));
        }

        return $trail;
    }

    /**
     * Remove consecutive breadcrumbs that point to the same destination.
     *
     * @param array<int, array{label: string, url: string|null}> $trail
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function collapseConsecutiveDuplicates(array $trail): array
    {
        if (count($trail) < 2) {
            return $trail;
        }

        $collapsed = [];

        foreach ($trail as $crumb) {
            if (!isset($crumb['label'])) {
                continue;
            }

            $crumb = [
                'label' => $crumb['label'],
                'url' => $crumb['url'] ?? null,
            ];

            $lastIndex = count($collapsed) - 1;
            $last = $lastIndex >= 0 ? $collapsed[$lastIndex] : null;

            if ($last && self::crumbsPointToSameTarget($last, $crumb)) {
                $collapsed[$lastIndex] = [
                    'label' => $crumb['label'],
                    'url' => $crumb['url'] ?? $last['url'],
                ];
                continue;
            }

            $collapsed[] = $crumb;
        }

        return $collapsed;
    }

    /**
     * Determine if two breadcrumbs represent the same target.
     *
     * @param array{label: string, url: string|null} $first
     * @param array{label: string, url: string|null} $second
     */
    protected static function crumbsPointToSameTarget(array $first, array $second): bool
    {
        $firstUrl = $first['url'] ?? null;
        $secondUrl = $second['url'] ?? null;

        if ($firstUrl !== null || $secondUrl !== null) {
            return $firstUrl !== null
                && $secondUrl !== null
                && $firstUrl === $secondUrl;
        }

        return $first['label'] === $second['label'];
    }

    /**
     * Map admin sections to readable labels and default routes.
     *
     * @return array<string, array{label: string, route?: string}>
     */
    protected static function adminSections(): array
    {
        return [
            'dashboard' => [
                'label' => 'منطقة الإدارة (لوحة التحكم)',
                'route' => 'admin.dashboard',
            ],
            'admin-area' => [
                'label' => 'منطقة الإدارة (لوحة التحكم)',
                'route' => 'admin.admin-area',
            ],
            'tools' => [
                'label' => 'أدوات المطبخ',
                'route' => 'admin.tools.index',
            ],
            'workshops' => [
                'label' => 'ورشات العمل',
                'route' => 'admin.workshops.index',
            ],
            'bookings' => [
                'label' => 'حجوزات الورش',
                'route' => 'admin.bookings.index',
            ],
            'recipes' => [
                'label' => 'الوصفات',
                'route' => 'admin.recipes.index',
            ],
            'users' => [
                'label' => 'إدارة المستخدمين',
                'route' => 'admin.users.index',
            ],
            'referrals' => [
                'label' => 'برنامج الإحالات',
                'route' => 'admin.referrals.index',
            ],
            'visibility' => [
                'label' => 'إعدادات الظهور',
                'route' => 'admin.visibility.index',
            ],
            'hero-slides' => [
                'label' => 'سلايدر الهيرو',
                'route' => 'admin.hero-slides.index',
            ],
            'chefs' => [
                'label' => 'طلبات الشيفات',
                'route' => 'admin.chefs.requests',
            ],
        ];
    }

    /**
     * Map chef sections to readable labels and default routes.
     *
     * @return array<string, array{label: string, route?: string}>
     */
    protected static function chefSections(): array
    {
        return [
            'dashboard' => [
                'label' => 'لوحة الشيف',
                'route' => 'chef.dashboard',
            ],
            'links' => [
                'label' => 'روابط Wasfah',
                'route' => 'chef.links.edit',
            ],
            'recipes' => [
                'label' => 'وصفاتي',
                'route' => 'chef.recipes.index',
            ],
            'workshops' => [
                'label' => 'ورش الشيف',
                'route' => 'chef.workshops.index',
            ],
        ];
    }

    /**
     * Convert a section key into a breadcrumb entry.
     *
     * @param array<string, array{label: string, route?: string}> $map
     */
    protected static function sectionBreadcrumb(?string $sectionKey, array $map): ?array
    {
        if (!$sectionKey) {
            return null;
        }

        $section = $map[$sectionKey] ?? null;
        $label = $section['label'] ?? self::titleCaseLastSegment($sectionKey);
        $url = $section['route'] ?? null;

        return [
            'label' => $label,
            'url' => $url ? self::routeUrl($url) : null,
        ];
    }

    /**
     * Generate action-specific crumbs for admin sections.
     *
     * @param array<int, string> $segments
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function adminActionTrail(string $sectionKey, array $segments): array
    {
        $action = $segments[0] ?? null;

        if (!$action || in_array($action, ['index', 'store', 'update', 'destroy'], true)) {
            return [];
        }

        switch ($sectionKey) {
            case 'tools':
                return self::resourceActionTrail($action, 'tool', [
                    'create' => 'إضافة أداة جديدة',
                    'show' => fn ($tool) => 'تفاصيل: ' . self::modelLabel($tool, 'name', 'الأداة'),
                    'edit' => fn ($tool) => 'تعديل: ' . self::modelLabel($tool, 'name', 'الأداة'),
                ]);

            case 'workshops':
                return self::resourceActionTrail($action, 'workshop', [
                    'create' => 'إضافة ورشة جديدة',
                    'show' => fn ($workshop) => 'تفاصيل: ' . self::modelLabel($workshop, 'title', 'الورشة'),
                    'edit' => fn ($workshop) => 'تعديل: ' . self::modelLabel($workshop, 'title', 'الورشة'),
                    'meeting' => fn ($workshop) => 'رابط الاجتماع - ' . self::modelLabel($workshop, 'title', 'الورشة'),
                ]);

            case 'recipes':
                return self::resourceActionTrail($action, 'recipe', [
                    'create' => 'إضافة وصفة جديدة',
                    'show' => fn ($recipe) => 'تفاصيل: ' . self::modelLabel($recipe, 'title', 'الوصفة'),
                    'edit' => fn ($recipe) => 'تعديل: ' . self::modelLabel($recipe, 'title', 'الوصفة'),
                ]);

            case 'bookings':
                $trail = self::resourceActionTrail($action, 'booking', [
                    'show' => function ($booking) {
                        $id = $booking->id ?? null;
                        return $id ? "تفاصيل الحجز #{$id}" : 'تفاصيل الحجز';
                    },
                ]);

                if ($trail) {
                    return $trail;
                }

                if ($action === 'export') {
                    return [
                        [
                            'label' => 'تصدير الحجوزات',
                            'url' => null,
                        ],
                    ];
                }
                break;

            case 'referrals':
                return self::resourceActionTrail($action, 'user', [
                    'show' => fn ($user) => 'شريك الإحالة: ' . self::modelLabel($user, 'name', 'الحساب'),
                ]);

            case 'visibility':
                if ($action === 'config') {
                    return [
                        [
                            'label' => 'تهيئة الإعدادات',
                            'url' => null,
                        ],
                    ];
                }
                break;

            case 'hero-slides':
                return self::resourceActionTrail($action, 'heroSlide', [
                    'create' => 'إضافة شريحة جديدة',
                    'edit' => fn ($slide) => 'تعديل: ' . self::modelLabel($slide, 'title', 'الشريحة'),
                ]);

            case 'chefs':
                if ($action === 'requests') {
                    return [];
                }
                break;
        }

        return [
            [
                'label' => self::titleCaseLastSegment($action),
                'url' => null,
            ],
        ];
    }

    /**
     * Generate action-specific crumbs for chef sections.
     *
     * @param array<int, string> $segments
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function chefActionTrail(string $sectionKey, array $segments): array
    {
        $action = $segments[0] ?? null;

        if (!$action || in_array($action, ['index', 'store', 'update', 'destroy'], true)) {
            return [];
        }

        switch ($sectionKey) {
            case 'links':
                if ($action === 'edit') {
                    return [
                        [
                            'label' => 'تعديل صفحة الروابط',
                            'url' => null,
                        ],
                    ];
                }
                return [];

            case 'recipes':
                return self::resourceActionTrail($action, 'recipe', [
                    'create' => 'إضافة وصفة جديدة',
                    'edit' => fn ($recipe) => 'تعديل: ' . self::modelLabel($recipe, 'title', 'الوصفة'),
                ]);

            case 'workshops':
                return self::resourceActionTrail($action, 'workshop', [
                    'create' => 'إنشاء ورشة جديدة',
                    'edit' => fn ($workshop) => 'تعديل: ' . self::modelLabel($workshop, 'title', 'الورشة'),
                    'earnings' => 'أرباح الورش',
                    'join' => fn ($workshop) => 'انضمام إلى ' . self::modelLabel($workshop, 'title', 'الورشة'),
                ]);
        }

        return [
            [
                'label' => self::titleCaseLastSegment($action),
                'url' => null,
            ],
        ];
    }

    /**
     * Helper to convert CRUD-like actions into breadcrumb entries.
     *
     * @param array<string, string|callable> $labels
     * @return array<int, array{label: string, url: string|null}>
     */
    protected static function resourceActionTrail(?string $action, ?string $paramName, array $labels): array
    {
        if (!$action || !array_key_exists($action, $labels)) {
            return [];
        }

        $labelResolver = $labels[$action];
        $model = $paramName ? request()->route($paramName) : null;
        $label = is_callable($labelResolver)
            ? $labelResolver($model)
            : $labelResolver;

        if (!$label) {
            return [];
        }

        return [
            [
                'label' => $label,
                'url' => null,
            ],
        ];
    }

    /**
     * Safely read a human value from the given model.
     */
    protected static function modelLabel($model, string $attribute, string $fallback): string
    {
        if ($model && isset($model->{$attribute}) && $model->{$attribute} !== '') {
            return (string) $model->{$attribute};
        }

        return $fallback;
    }

    /**
     * Resolve a route name to URL if it exists.
     */
    protected static function routeUrl(?string $routeName): ?string
    {
        if (!$routeName || !Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName);
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Provide a readable fallback title for unknown routes.
     */
    protected static function titleCaseLastSegment(string $routeName): string
    {
        $segments = explode('.', $routeName);
        $last = end($segments) ?: $routeName;

        return ucwords(str_replace(['-', '_'], ' ', $last));
    }
}
