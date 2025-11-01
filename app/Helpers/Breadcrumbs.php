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
                // Do not render breadcrumbs for admin or unsupported routes.
                if (!str_starts_with($routeName, 'admin.')) {
                    $breadcrumbs[] = [
                        'label' => self::titleCaseLastSegment($routeName),
                        'url' => null,
                    ];
                }
                break;
        }

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
     * Provide a readable fallback title for unknown routes.
     */
    protected static function titleCaseLastSegment(string $routeName): string
    {
        $segments = explode('.', $routeName);
        $last = end($segments) ?: $routeName;

        return ucwords(str_replace(['-', '_'], ' ', $last));
    }
}
