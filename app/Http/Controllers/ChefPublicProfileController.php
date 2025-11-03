<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class ChefPublicProfileController extends Controller
{
    /**
     * Display the public profile for a chef with their published recipes.
     */
    public function show(User $chef): View
    {
        if (!$chef->isChef()) {
            abort(404);
        }

        $viewer = Auth::user();
        $isOwner = $viewer && $viewer->id === $chef->id;
        $canViewExclusive = $isOwner;
        $visibilityColumnExists = Schema::hasColumn('recipes', 'visibility');

        $recipes = $chef->recipes()
            ->approved()
            ->with(['category'])
            ->withCount([
                'interactions as saved_count' => function ($query) {
                    $query->where('is_saved', true);
                },
                'interactions as made_count' => function ($query) {
                    $query->where('is_made', true);
                },
                'interactions as rating_count' => function ($query) {
                    $query->whereNotNull('rating');
                },
            ])
            ->withAvg('interactions', 'rating')
            ->orderByDesc('created_at')
            ->get();

        if (!$visibilityColumnExists) {
            $recipes->each(function (Recipe $recipe) {
                if (empty($recipe->visibility)) {
                    $recipe->visibility = Recipe::VISIBILITY_PUBLIC;
                }
            });
        }

        $publicRecipes = $visibilityColumnExists
            ? $recipes->where('visibility', Recipe::VISIBILITY_PUBLIC)->values()
            : $recipes->values();

        $exclusiveRecipes = ($visibilityColumnExists && $canViewExclusive)
            ? $recipes->where('visibility', Recipe::VISIBILITY_PRIVATE)->values()
            : collect();

        $popularRecipes = $publicRecipes
            ->merge($exclusiveRecipes)
            ->sortByDesc(function ($recipe) {
                $ratingScore = (float) ($recipe->interactions_avg_rating ?? 0);

                return ($recipe->saved_count * 100000)
                    + ($ratingScore * 1000)
                    + $recipe->created_at?->getTimestamp();
            })
            ->take(12)
            ->values();

        $stats = $this->buildChefStats($recipes);

        $viewName = collect([
            'chef.public-profile',
            'chef.profile-fallback',
        ])->first(function (string $candidate): bool {
            return ViewFacade::exists($candidate);
        });

        if (!$viewName) {
            abort(500, 'Chef public profile view is missing.');
        }

        return view($viewName, [
            'chef' => $chef,
            'avatarUrl' => $this->resolveAvatarUrl($chef->avatar),
            'publicRecipes' => $publicRecipes,
            'exclusiveRecipes' => $exclusiveRecipes,
            'popularRecipes' => $popularRecipes,
            'stats' => $stats,
            'socialLinks' => $this->buildSocialLinks($chef),
            'isOwner' => $isOwner,
            'canViewExclusive' => $canViewExclusive,
        ]);
    }

    /**
     * Prepare aggregate stats for the chef's recipes.
     */
    protected function buildChefStats(Collection $recipes): array
    {
        $averageRating = $recipes->pluck('interactions_avg_rating')
            ->filter()
            ->average();

        return [
            'recipes_count' => $recipes->count(),
            'total_saves' => (int) $recipes->sum('saved_count'),
            'total_made' => (int) $recipes->sum('made_count'),
            'rating_count' => (int) $recipes->sum('rating_count'),
            'average_rating' => $averageRating
                ? round((float) $averageRating, 1)
                : null,
        ];
    }

    /**
     * Convert the stored avatar path to a public URL.
     */
    protected function resolveAvatarUrl(?string $avatar): string
    {
        if (!$avatar) {
            return asset('image/logo.png');
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        if (Storage::disk('public')->exists($avatar)) {
            return Storage::disk('public')->url($avatar);
        }

        return asset(trim($avatar, '/'));
    }

    /**
     * Build social links list for the profile header.
     */
    protected function buildSocialLinks(User $chef): Collection
    {
        return collect([
            $chef->instagram_url ? [
                'label' => 'إنستغرام',
                'url' => $chef->instagram_url,
                'icon' => 'fab fa-instagram',
                'followers' => $chef->instagram_followers,
            ] : null,
            $chef->youtube_url ? [
                'label' => 'يوتيوب',
                'url' => $chef->youtube_url,
                'icon' => 'fab fa-youtube',
                'followers' => $chef->youtube_followers,
            ] : null,
        ])->filter()->values();
    }
}
