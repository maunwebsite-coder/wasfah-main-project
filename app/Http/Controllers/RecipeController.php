<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class RecipeController extends Controller
{
    /**
     * Display a listing of recipes.
     */
    public function index(Request $request): View
    {
        $query = Recipe::with(['category', 'interactions'])
            ->withCount(['interactions as saved_count' => function ($query) {
                $query->where('is_saved', true);
            }])
            ->withCount(['interactions as made_count' => function ($query) {
                $query->where('is_made', true);
            }])
            ->withAvg('interactions', 'rating');

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%");
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('id', $request->get('category'));
            });
        }

        // Apply difficulty filter
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->get('difficulty'));
        }

        // Apply prep time filter
        if ($request->filled('prep_time')) {
            $prepTime = $request->get('prep_time');
            switch ($prepTime) {
                case 'quick':
                    $query->where('prep_time', '<=', 30);
                    break;
                case 'medium':
                    $query->whereBetween('prep_time', [31, 60]);
                    break;
                case 'long':
                    $query->where('prep_time', '>', 60);
                    break;
            }
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', $sortDirection);
                break;
            case 'prep_time':
                $query->orderBy('prep_time', $sortDirection);
                break;
            case 'rating':
                $query->orderBy('interactions_avg_rating', $sortDirection);
                break;
            case 'saved':
                $query->orderBy('saved_count', $sortDirection);
                break;
            default:
                $query->orderBy('created_at', $sortDirection);
        }

        $recipes = $query->paginate(12)->withQueryString();
        
        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        return view('recipes', compact('recipes', 'categories'));
    }

    /**
     * API endpoint to get latest recipes for homepage
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $recipes = Recipe::with(['category', 'interactions'])
                ->withCount(['interactions as saved_count' => function ($query) {
                    $query->where('is_saved', true);
                }])
                ->withAvg('interactions', 'rating')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // Add is_saved status for authenticated users
            if (auth()->check()) {
                $user = auth()->user();
                $savedRecipes = $user->interactions()
                    ->where('is_saved', true)
                    ->pluck('recipe_id')
                    ->toArray();

                $recipes->each(function ($recipe) use ($savedRecipes) {
                    $recipe->is_saved = in_array($recipe->recipe_id, $savedRecipes);
                });
            } else {
                $recipes->each(function ($recipe) {
                    $recipe->is_saved = false;
                });
            }

            return response()->json($recipes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch recipes'], 500);
        }
    }

    /**
     * Display a specific recipe (Blade template)
     */
    public function show(Recipe $recipe): View
    {
        try {
            $recipe->load(['category', 'interactions', 'ingredients']);
            
            // Load counts and averages
            $recipe->loadCount(['interactions as saved_count' => function ($query) {
                $query->where('is_saved', true);
            }]);
            
            $recipe->loadCount(['interactions as made_count' => function ($query) {
                $query->where('is_made', true);
            }]);
            
            $recipe->loadAvg('interactions', 'rating');

            // Add is_saved status for authenticated users
            if (auth()->check()) {
                $user = auth()->user();
                $userInteraction = $user->interactions()
                    ->where('recipe_id', $recipe->recipe_id)
                    ->first();
                
                $recipe->is_saved = $userInteraction ? (bool)$userInteraction->is_saved : false;
                $recipe->is_made = $userInteraction ? (bool)$userInteraction->is_made : false;
                $recipe->user_rating = $userInteraction ? $userInteraction->rating : null;
            } else {
                $recipe->is_saved = false;
                $recipe->is_made = false;
                $recipe->user_rating = null;
            }

            // Ensure steps is properly formatted as array
            if (is_string($recipe->steps)) {
                $recipe->steps = json_decode($recipe->steps, true) ?? [];
            }
            if (!is_array($recipe->steps)) {
                $recipe->steps = [];
            }

            // Ensure tools is properly formatted as array
            if (is_string($recipe->tools)) {
                $recipe->tools = json_decode($recipe->tools, true) ?? [];
            }
            if (!is_array($recipe->tools)) {
                $recipe->tools = [];
            }
            
            // تحويل IDs إلى بيانات كاملة للمعدات
            if (!empty($recipe->tools)) {
                $toolIds = array_filter($recipe->tools, 'is_numeric');
                if (!empty($toolIds)) {
                    $tools = \App\Models\Tool::whereIn('id', $toolIds)
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();
                    $recipe->tools = $tools;
                } else {
                    $recipe->tools = collect([]);
                }
            } else {
                $recipe->tools = collect([]);
            }
            
            // Ensure tools is always a collection for consistency
            if (is_array($recipe->tools)) {
                $recipe->tools = collect($recipe->tools);
            }

            // Get random recipes from the same category (excluding current recipe)
            $relatedRecipes = Recipe::with(['category', 'interactions'])
                ->withCount(['interactions as saved_count' => function ($query) {
                    $query->where('is_saved', true);
                }])
                ->withAvg('interactions', 'rating')
                ->where('category_id', $recipe->category_id)
                ->where('recipe_id', '!=', $recipe->recipe_id)
                ->inRandomOrder()
                ->limit(6)
                ->get();

            // Add is_saved status for authenticated users for related recipes
            if (auth()->check()) {
                $user = auth()->user();
                $savedRecipes = $user->interactions()
                    ->where('is_saved', true)
                    ->pluck('recipe_id')
                    ->toArray();

                $relatedRecipes->each(function ($relatedRecipe) use ($savedRecipes) {
                    $relatedRecipe->is_saved = in_array($relatedRecipe->recipe_id, $savedRecipes);
                });
            } else {
                $relatedRecipes->each(function ($relatedRecipe) {
                    $relatedRecipe->is_saved = false;
                });
            }

            return view('recipe', compact('recipe', 'relatedRecipes'));
        } catch (\Exception $e) {
            \Log::error('Recipe show error: ' . $e->getMessage());
            abort(500, 'Failed to fetch recipe');
        }
    }

    /**
     * API endpoint to get a specific recipe
     */
    public function apiShow(Recipe $recipe): JsonResponse
    {
        try {
            $recipe->load(['category', 'interactions', 'ingredients']);
            
            // Load counts and averages
            $recipe->loadCount(['interactions as saved_count' => function ($query) {
                $query->where('is_saved', true);
            }]);
            
            $recipe->loadCount(['interactions as made_count' => function ($query) {
                $query->where('is_made', true);
            }]);
            
            $recipe->loadAvg('interactions', 'rating');

            // Add is_saved status for authenticated users
            if (auth()->check()) {
                $user = auth()->user();
                $userInteraction = $user->interactions()
                    ->where('recipe_id', $recipe->recipe_id)
                    ->first();
                
                $recipe->is_saved = $userInteraction ? (bool)$userInteraction->is_saved : false;
                $recipe->is_made = $userInteraction ? (bool)$userInteraction->is_made : false;
                $recipe->user_rating = $userInteraction ? $userInteraction->rating : null;
            } else {
                $recipe->is_saved = false;
                $recipe->is_made = false;
                $recipe->user_rating = null;
            }

            // Ensure steps is properly formatted as array
            if (is_string($recipe->steps)) {
                $recipe->steps = json_decode($recipe->steps, true) ?? [];
            }
            if (!is_array($recipe->steps)) {
                $recipe->steps = [];
            }

            // Ensure tools is properly formatted as array
            if (is_string($recipe->tools)) {
                $recipe->tools = json_decode($recipe->tools, true) ?? [];
            }
            if (!is_array($recipe->tools)) {
                $recipe->tools = [];
            }

            // تحويل IDs إلى بيانات كاملة للمعدات
            if (!empty($recipe->tools)) {
                $toolIds = array_filter($recipe->tools, 'is_numeric');
                if (!empty($toolIds)) {
                    $tools = \App\Models\Tool::whereIn('id', $toolIds)
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get()
                        ->toArray();
                    $recipe->tools = $tools;
                } else {
                    $recipe->tools = [];
                }
            } else {
                $recipe->tools = [];
            }

            // إضافة معلومات إضافية للتأكد من وصول البيانات
            $responseData = $recipe->toArray();
            $responseData['interactions_avg_rating'] = $recipe->interactions_avg_rating;
            $responseData['made_count'] = $recipe->made_count;
            $responseData['saved_count'] = $recipe->saved_count;
            
            return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('Recipe show API error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch recipe: ' . $e->getMessage()], 500);
        }
    }
}