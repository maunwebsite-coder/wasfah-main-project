<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * عرض صفحة نتائج البحث
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all'); // all, recipes, workshops
        
        $recipes = collect();
        $workshops = collect();
        
        if ($query) {
            if ($type === 'all' || $type === 'recipes') {
                $recipesQuery = Recipe::select([
                        'recipes.recipe_id',
                        'recipes.title',
                        'recipes.slug',
                        'recipes.description',
                        'recipes.author',
                        'recipes.image',
                        'recipes.image_url',
                        'recipes.created_at',
                        'recipes.category_id',
                        'recipes.prep_time'
                    ])
                    ->distinct()
                    ->with(['category:category_id,name', 'ingredients:id,name'])
                    ->withCount(['interactions as saved_count' => function ($query) {
                        $query->where('is_saved', true);
                    }])
                    ->withCount(['interactions as made_count' => function ($query) {
                        $query->where('is_made', true);
                    }])
                    ->withCount(['interactions as interactions_count'])
                    ->withAvg('interactions', 'rating')
                    ->where(function ($q) use ($query) {
                        $q->where('recipes.title', 'like', "%{$query}%")
                          ->orWhere('recipes.description', 'like', "%{$query}%")
                          ->orWhere('recipes.author', 'like', "%{$query}%")
                          ->orWhereHas('category', function ($subQ) use ($query) {
                              $subQ->where('name', 'like', "%{$query}%");
                          })
                          ->orWhereHas('ingredients', function ($subQ) use ($query) {
                              $subQ->where('name', 'like', "%{$query}%");
                          });
                    });

                if (Recipe::supportsModerationStatus()) {
                    $recipesQuery->where('recipes.status', Recipe::STATUS_APPROVED);
                }

                if (Recipe::supportsVisibilityFlag()) {
                    $recipesQuery->where('recipes.visibility', Recipe::VISIBILITY_PUBLIC);
                }

                // Apply recipe filters
                if ($request->has('recipe_category') && $request->recipe_category) {
                    $recipesQuery->whereHas('category', function ($q) use ($request) {
                        $q->where('name', $request->recipe_category);
                    });
                }

                if ($request->has('difficulty') && $request->difficulty) {
                    $recipesQuery->where('difficulty', $request->difficulty);
                }

                if ($request->has('prep_time') && $request->prep_time) {
                    switch ($request->prep_time) {
                        case '0-30':
                            $recipesQuery->where('prep_time', '<=', 30);
                            break;
                        case '30-60':
                            $recipesQuery->whereBetween('prep_time', [30, 60]);
                            break;
                        case '60-120':
                            $recipesQuery->whereBetween('prep_time', [60, 120]);
                            break;
                        case '120+':
                            $recipesQuery->where('prep_time', '>', 120);
                            break;
                    }
                }

                // Apply sorting
                if ($request->has('recipe_sort') && $request->recipe_sort) {
                    switch ($request->recipe_sort) {
                        case 'newest':
                            $recipesQuery->orderBy('created_at', 'desc');
                            break;
                        case 'rating':
                            $recipesQuery->orderBy('interactions_avg_rating', 'desc');
                            break;
                        case 'popular':
                            $recipesQuery->orderBy('saved_count', 'desc');
                            break;
                        case 'relevance':
                        default:
                            $recipesQuery->orderBy('created_at', 'desc');
                            break;
                    }
                } else {
                    $recipesQuery->orderBy('created_at', 'desc');
                }

                $recipes = $recipesQuery->limit(20)->get();
            }
            
            if ($type === 'all' || $type === 'workshops') {
                $workshopsQuery = Workshop::select([
                        'id',
                        'slug',
                        'title',
                        'description',
                        'instructor',
                        'category',
                        'price',
                        'currency',
                        'start_date',
                        'location',
                        'image',
                        'is_online',
                        'rating',
                        'reviews_count'
                    ])
                    ->where('is_active', true)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%")
                          ->orWhere('instructor', 'like', "%{$query}%")
                          ->orWhere('category', 'like', "%{$query}%");
                    });

                // Apply workshop filters
                if ($request->has('workshop_type') && $request->workshop_type) {
                    if ($request->workshop_type === 'online') {
                        $workshopsQuery->online();
                    } elseif ($request->workshop_type === 'offline') {
                        $workshopsQuery->offline();
                    }
                }

                if ($request->has('workshop_level') && $request->workshop_level) {
                    $workshopsQuery->byLevel($request->workshop_level);
                }

                if ($request->has('workshop_category') && $request->workshop_category) {
                    $workshopsQuery->byCategory($request->workshop_category);
                }

                if ($request->has('price_range') && $request->price_range) {
                    switch ($request->price_range) {
                        case '0-100':
                            $workshopsQuery->where('price', '<=', 100);
                            break;
                        case '100-200':
                            $workshopsQuery->whereBetween('price', [100, 200]);
                            break;
                        case '200-300':
                            $workshopsQuery->whereBetween('price', [200, 300]);
                            break;
                        case '300+':
                            $workshopsQuery->where('price', '>', 300);
                            break;
                    }
                }

                // Apply sorting
                if ($request->has('sort_by') && $request->sort_by) {
                    switch ($request->sort_by) {
                        case 'price_low':
                            $workshopsQuery->orderBy('price', 'asc');
                            break;
                        case 'price_high':
                            $workshopsQuery->orderBy('price', 'desc');
                            break;
                        case 'date_soon':
                            $workshopsQuery->orderBy('start_date', 'asc');
                            break;
                        case 'rating':
                            $workshopsQuery->orderBy('rating', 'desc');
                            break;
                        case 'relevance':
                        default:
                            $workshopsQuery->orderBy('start_date', 'asc');
                            break;
                    }
                } else {
                    $workshopsQuery->orderBy('start_date', 'asc');
                }

                $workshops = $workshopsQuery->limit(20)->get();
            }
        }
        
        return view('search', compact('query', 'type', 'recipes', 'workshops'));
    }
    
    /**
     * API endpoint للبحث
     */
    public function api(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $type = $request->get('type', 'all');
            
            // التحقق من صحة البيانات
            if (empty($query) || strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب إدخال كلمة بحث مكونة من حرفين على الأقل',
                    'query' => $query,
                    'results' => [
                        'recipes' => collect(),
                        'workshops' => collect()
                    ]
                ], 400);
            }
            
            // إنشاء cache key
            $cacheKey = "search_api_{$type}_{$query}";
            
            // التحقق من cache أولاً
            if (Cache::has($cacheKey)) {
                return response()->json(Cache::get($cacheKey));
            }
            
            $results = [
                'recipes' => collect(),
                'workshops' => collect()
            ];
            
            if ($query) {
                if ($type === 'all' || $type === 'recipes') {
                    $results['recipes'] = $this->searchRecipes($query);
                }
                
                if ($type === 'all' || $type === 'workshops') {
                    $results['workshops'] = $this->searchWorkshops($query);
                }
            }
            
            $response = [
                'success' => true,
                'query' => $query,
                'results' => $results
            ];
            
            // حفظ النتائج في cache لمدة 5 دقائق
            Cache::put($cacheKey, $response, 300);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error('Search API Error: ' . $e->getMessage(), [
                'query' => $request->get('q', ''),
                'type' => $request->get('type', 'all'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث. يرجى المحاولة لاحقاً',
                'query' => $request->get('q', ''),
                'results' => [
                    'recipes' => collect(),
                    'workshops' => collect()
                ]
            ], 500);
        }
    }
    
    /**
     * البحث في الوصفات مع تحسينات الأداء
     */
    private function searchRecipes($query)
    {
        $recipesQuery = Recipe::select([
                'recipes.recipe_id',
                'recipes.title',
                'recipes.slug',
                'recipes.description',
                'recipes.author',
                'recipes.image',
                'recipes.image_url',
                'recipes.created_at',
                'recipes.prep_time'
            ])
            ->distinct()
            ->with(['category:category_id,name', 'ingredients:id,name'])
            ->withCount(['interactions as saved_count' => function ($query) {
                $query->where('is_saved', true);
            }])
            ->withCount(['interactions as made_count' => function ($query) {
                $query->where('is_made', true);
            }])
            ->withCount(['interactions as interactions_count'])
            ->withAvg('interactions', 'rating')
            ->where(function ($q) use ($query) {
                $q->where('recipes.title', 'like', "%{$query}%")
                  ->orWhere('recipes.description', 'like', "%{$query}%")
                  ->orWhere('recipes.author', 'like', "%{$query}%")
                  ->orWhereHas('category', function ($subQ) use ($query) {
                      $subQ->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('ingredients', function ($subQ) use ($query) {
                      $subQ->where('name', 'like', "%{$query}%");
                  });
            });

        if (Recipe::supportsModerationStatus()) {
            $recipesQuery->where('recipes.status', Recipe::STATUS_APPROVED);
        }

        if (Recipe::supportsVisibilityFlag()) {
            $recipesQuery->where('recipes.visibility', Recipe::VISIBILITY_PUBLIC);
        }

        return $recipesQuery
            ->orderBy('recipes.created_at', 'desc')
            ->limit(5)
            ->get();
    }
    
    /**
     * البحث في الورشات مع تحسينات الأداء
     */
    private function searchWorkshops($query)
    {
        return Workshop::select([
                'id',
                'slug',
                'title',
                'description',
                'instructor',
                'category',
                'price',
                'currency',
                'start_date',
                'location',
                'image',
                'is_online'
            ])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('instructor', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            })
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();
    }
}
