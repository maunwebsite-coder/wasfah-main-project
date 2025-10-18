<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\Request;

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
            ->orderBy('created_at', 'desc')
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

        return view('home', compact('workshops', 'featuredWorkshop', 'latestRecipes', 'featuredRecipes', 'hasUpcomingWorkshops'));
    }
}
