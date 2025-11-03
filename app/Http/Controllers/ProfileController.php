<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Workshop;
use App\Models\UserInteraction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ContentModerationService;

class ProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي للمستخدم
     */
    public function index()
    {
        $user = Auth::user();
        
        // الحصول على الوصفات المحفوظة
        $savedInteractions = UserInteraction::where('user_id', $user->id)
            ->where('is_saved', true)
            ->with(['recipe.category'])
            ->latest('updated_at')
            ->get();

        $savedRecipes = $savedInteractions
            ->map(function ($interaction) {
                if (!$interaction->recipe) {
                    return null;
                }

                $interaction->recipe->setRelation('userInteraction', $interaction);

                return $interaction->recipe;
            })
            ->filter()
            ->values(); // إزالة القيم الفارغة
        
        // الحصول على الوصفات المصنوعة
        $madeInteractions = UserInteraction::where('user_id', $user->id)
            ->where('is_made', true)
            ->with(['recipe.category'])
            ->latest('updated_at')
            ->get();

        $madeRecipes = $madeInteractions
            ->map(function ($interaction) {
                if (!$interaction->recipe) {
                    return null;
                }

                $interaction->recipe->setRelation('userInteraction', $interaction);

                return $interaction->recipe;
            })
            ->filter()
            ->values();
        
        // الحصول على جميع الورشات المحجوزة (مؤكدة ومعلقة وملغية)
        $bookedWorkshops = $user->workshopBookings()
            ->with('workshop')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // الحصول على تقييمات الورشات
        $workshopReviews = $user->workshopReviews()
            ->with('workshop')
            ->get();

        $upcomingWorkshops = $bookedWorkshops
            ->filter(function ($booking) {
                $startDate = optional($booking->workshop)->start_date;

                return $startDate && $startDate->isFuture();
            })
            ->sortBy(function ($booking) {
                return optional($booking->workshop->start_date);
            })
            ->values();

        $nextWorkshop = $upcomingWorkshops->first();

        $activityFeed = collect()
            ->merge(
                $savedInteractions->take(4)->map(function ($interaction) {
                    if (!$interaction->recipe) {
                        return null;
                    }

                    return [
                        'type' => 'saved_recipe',
                        'title' => $interaction->recipe->title,
                        'timestamp' => $interaction->updated_at ?? $interaction->created_at,
                        'meta' => [
                            'recipe_id' => $interaction->recipe->recipe_id,
                            'slug' => $interaction->recipe->slug,
                            'image' => $interaction->recipe->image ?? $interaction->recipe->image_url,
                            'category' => optional($interaction->recipe->category)->name,
                        ],
                    ];
                })
            )
            ->merge(
                $madeInteractions->take(4)->map(function ($interaction) {
                    if (!$interaction->recipe) {
                        return null;
                    }

                    return [
                        'type' => 'made_recipe',
                        'title' => $interaction->recipe->title,
                        'timestamp' => $interaction->updated_at ?? $interaction->created_at,
                        'meta' => [
                            'recipe_id' => $interaction->recipe->recipe_id,
                            'slug' => $interaction->recipe->slug,
                            'image' => $interaction->recipe->image ?? $interaction->recipe->image_url,
                            'category' => optional($interaction->recipe->category)->name,
                        ],
                    ];
                })
            )
            ->merge(
                $bookedWorkshops->take(6)->map(function ($booking) {
                    if (!$booking->workshop) {
                        return null;
                    }

                    return [
                        'type' => 'workshop_booking',
                        'title' => $booking->workshop->title,
                        'timestamp' => $booking->updated_at ?? $booking->created_at,
                        'meta' => [
                            'status' => $booking->status,
                            'start_date' => $booking->workshop->start_date,
                            'end_date' => $booking->workshop->end_date,
                            'workshop_id' => $booking->workshop->id,
                        ],
                    ];
                })
            )
            ->filter()
            ->sortByDesc('timestamp')
            ->values()
            ->take(8);

        $latestActivity = $activityFeed->first();
        
        // إحصائيات المستخدم
        $stats = [
            'saved_recipes_count' => $savedRecipes->count(),
            'made_recipes_count' => $madeRecipes->count(),
            'booked_workshops_count' => $bookedWorkshops->count(),
            'confirmed_workshops_count' => $bookedWorkshops->where('status', 'confirmed')->count(),
            'pending_workshops_count' => $bookedWorkshops->where('status', 'pending')->count(),
            'cancelled_workshops_count' => $bookedWorkshops->where('status', 'cancelled')->count(),
            'reviews_count' => $workshopReviews->count(),
            'upcoming_workshops_count' => $upcomingWorkshops->count(),
            'next_workshop_at' => optional(optional($nextWorkshop)->workshop)->start_date,
            'last_activity_at' => $latestActivity['timestamp'] ?? null,
        ];

        $stats['engagement_score'] = ($stats['saved_recipes_count'] * 2)
            + ($stats['made_recipes_count'] * 3)
            + ($stats['booked_workshops_count'] * 4);

        $computeAchievement = function (int $count, array $milestones) {
            $currentLevel = 'starter';
            $nextGoal = null;
            $progress = 100;

            foreach ($milestones as $threshold => $label) {
                if ($count >= $threshold) {
                    $currentLevel = $label;
                    continue;
                }

                $nextGoal = $threshold;
                $progress = $threshold > 0 ? min(100, (int) round(($count / $threshold) * 100)) : 0;
                break;
            }

            if ($nextGoal === null && $count < array_key_last($milestones)) {
                $nextGoal = array_key_last($milestones);
                $progress = $nextGoal > 0 ? min(100, (int) round(($count / $nextGoal) * 100)) : 0;
            }

            if ($count >= array_key_last($milestones)) {
                $nextGoal = null;
                $progress = 100;
            }

            return [
                'current_level' => $currentLevel,
                'next_goal' => $nextGoal,
                'progress' => $progress,
            ];
        };

        $achievements = [
            array_merge([
                'key' => 'collector',
                'title' => 'هاوي الوصفات',
                'description' => 'حفظت ' . $stats['saved_recipes_count'] . ' وصفات في مكتبتك.',
                'count' => $stats['saved_recipes_count'],
                'icon' => 'bookmark',
            ], $computeAchievement($stats['saved_recipes_count'], [
                3 => 'bronze',
                10 => 'silver',
                25 => 'gold',
                50 => 'platinum',
            ])),
            array_merge([
                'key' => 'chef',
                'title' => 'الشيف المنزلي',
                'description' => 'جربت ' . $stats['made_recipes_count'] . ' وصفات بنفسك.',
                'count' => $stats['made_recipes_count'],
                'icon' => 'utensils',
            ], $computeAchievement($stats['made_recipes_count'], [
                1 => 'bronze',
                5 => 'silver',
                15 => 'gold',
                30 => 'platinum',
            ])),
            array_merge([
                'key' => 'learner',
                'title' => 'عاشق التعلّم',
                'description' => 'حجزت ' . $stats['booked_workshops_count'] . ' ورشات حتى الآن.',
                'count' => $stats['booked_workshops_count'],
                'icon' => 'graduation-cap',
            ], $computeAchievement($stats['booked_workshops_count'], [
                1 => 'bronze',
                3 => 'silver',
                6 => 'gold',
                10 => 'platinum',
            ])),
        ];
        
        return view('profile', compact(
            'user',
            'savedRecipes',
            'madeRecipes', 
            'bookedWorkshops',
            'workshopReviews',
            'stats',
            'activityFeed',
            'achievements',
            'nextWorkshop',
            'upcomingWorkshops'
        ));
    }
    
    /**
     * تحديث معلومات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s\(\)]+$/',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if (ContentModerationService::containsProhibitedLanguage($request->name)) {
            return back()
                ->withErrors(['name' => 'الاسم يحتوي على كلمات غير لائقة.'])
                ->withInput();
        }
        
        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($user->isChef() && $request->hasFile('avatar')) {
            $originalName = $request->file('avatar')->getClientOriginalName();

            if (ContentModerationService::containsProhibitedLanguage($originalName)) {
                return back()
                    ->withErrors(['avatar' => 'اسم الملف يحتوي على كلمات غير لائقة.'])
                    ->withInput();
            }

            if (ContentModerationService::imageAppearsExplicit($request->file('avatar'))) {
                return back()
                    ->withErrors(['avatar' => 'الرجاء اختيار صورة احترافية مناسبة.'])
                    ->withInput();
            }

            $newAvatarPath = $request->file('avatar')->store('avatars', 'public');

            $oldAvatar = $user->avatar;
            if ($oldAvatar && !Str::startsWith($oldAvatar, ['http://', 'https://']) && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $updateData['avatar'] = $newAvatarPath;
        }

        $user->update($updateData);
        
        return redirect()->route('profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
}
