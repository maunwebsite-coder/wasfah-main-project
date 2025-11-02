<?php

namespace App\Http\Controllers;

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
            ->orderBy('start_date', 'asc');

        // استبعاد الورشة المميزة من قائمة الورشات العادية
        if ($featuredWorkshop) {
            $workshopsQuery->where('id', '!=', $featuredWorkshop->id);
        }

        $workshops = $workshopsQuery->limit(4)->get();

        // جلب أحدث الوصفات للعرض في الشريط الجانبي
        $latestRecipes = Recipe::with(['category'])
            ->withCount(['interactions as saved_count' => function ($query) {
                $query->where('is_saved', true);
            }])
            ->withCount(['interactions as made_count' => function ($query) {
                $query->where('is_made', true);
            }])
            ->withCount(['interactions as interactions_count'])
            ->withAvg('interactions', 'rating')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // جلب الوصفات المميزة للعرض في القسم الرئيسي
        $featuredRecipes = Recipe::with(['category'])
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

        // إضافة حالة الحفظ للمستخدمين المسجلين
        if (auth()->check()) {
            $user = auth()->user();
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
        ];

        return view('home', compact('workshops', 'featuredWorkshop', 'latestRecipes', 'featuredRecipes', 'hasUpcomingWorkshops', 'heroMedia', 'homeTools'));
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
            $randomRecipe = Recipe::with(['category'])->inRandomOrder()->first();
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
}
