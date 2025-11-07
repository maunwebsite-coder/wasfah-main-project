<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
                $recipes = $this->recipeSearchBuilder($request, $query)
                    ->take(20)
                    ->get();
            }
            
            if ($type === 'all' || $type === 'workshops') {
                $workshops = $this->workshopSearchBuilder($request, $query)
                    ->take(20)
                    ->get();
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
                    $results['recipes'] = $this->searchRecipes($query, $request);
                }
                
                if ($type === 'all' || $type === 'workshops') {
                    $results['workshops'] = $this->searchWorkshops($query, $request);
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
     * إنشاء استعلام Scout الخاص بالوصفات مع جميع علاقاتها ومرشحاتها.
     */
    private function recipeSearchBuilder(Request $request, string $query)
    {
        $callback = $this->recipeQueryCallback($request);

        if (Recipe::hasDescriptionFullTextIndex()) {
            return Recipe::search($query)
                ->query($callback);
        }

        $builder = Recipe::query();
        $callback($builder);
        $this->applyRecipeLikeSearch($builder, $query);

        return $builder;
    }
    
    /**
     * البحث في الوصفات مع تحسينات الأداء
     */
    private function searchRecipes(string $query, Request $request)
    {
        return $this->recipeSearchBuilder($request, $query)
            ->take(5)
            ->get();
    }
    
    /**
     * إنشاء استعلام Scout الخاص بالورشات مع المرشحات والترتيب.
     */
    private function workshopSearchBuilder(Request $request, string $query)
    {
        $callback = $this->workshopQueryCallback($request);

        if (Workshop::hasDescriptionFullTextIndex()) {
            return Workshop::search($query)
                ->query($callback);
        }

        $builder = Workshop::query();
        $callback($builder);
        $this->applyWorkshopLikeSearch($builder, $query);

        return $builder;
    }
    
    /**
     * البحث في الورشات مع تحسينات الأداء
     */
    private function searchWorkshops(string $query, Request $request)
    {
        return $this->workshopSearchBuilder($request, $query)
            ->take(5)
            ->get();
    }

    private function recipeQueryCallback(Request $request): \Closure
    {
        return function (EloquentBuilder $builder) use ($request) {
            $builder->select([
                    'recipes.recipe_id',
                    'recipes.title',
                    'recipes.slug',
                    'recipes.description',
                    'recipes.author',
                    'recipes.image',
                    'recipes.image_url',
                    'recipes.created_at',
                    'recipes.category_id',
                    'recipes.prep_time',
                ])
                ->distinct()
                ->with(['category:category_id,name', 'ingredients:id,name,recipe_id'])
                ->withCount(['interactions as saved_count' => function ($interactionQuery) {
                    $interactionQuery->where('is_saved', true);
                }])
                ->withCount(['interactions as made_count' => function ($interactionQuery) {
                    $interactionQuery->where('is_made', true);
                }])
                ->withCount(['interactions as interactions_count'])
                ->withAvg('interactions', 'rating');

            if (Recipe::supportsModerationStatus()) {
                $builder->where('recipes.status', Recipe::STATUS_APPROVED);
            }

            if (Recipe::supportsVisibilityFlag()) {
                $builder->where('recipes.visibility', Recipe::VISIBILITY_PUBLIC);
            }

            if ($request->filled('recipe_category')) {
                $categoryName = $request->recipe_category;
                $builder->whereHas('category', function ($categoryQuery) use ($categoryName) {
                    $categoryQuery->where('name', $categoryName);
                });
            }

            if ($request->filled('difficulty')) {
                $builder->where('difficulty', $request->difficulty);
            }

            if ($request->filled('prep_time')) {
                switch ($request->prep_time) {
                    case '0-30':
                        $builder->where('prep_time', '<=', 30);
                        break;
                    case '30-60':
                        $builder->whereBetween('prep_time', [30, 60]);
                        break;
                    case '60-120':
                        $builder->whereBetween('prep_time', [60, 120]);
                        break;
                    case '120+':
                        $builder->where('prep_time', '>', 120);
                        break;
                }
            }

            $sort = $request->get('recipe_sort');

            switch ($sort) {
                case 'newest':
                    $builder->orderBy('recipes.created_at', 'desc');
                    break;
                case 'rating':
                    $builder->orderBy('interactions_avg_rating', 'desc');
                    break;
                case 'popular':
                    $builder->orderBy('saved_count', 'desc');
                    break;
                default:
                    $builder->orderBy('recipes.created_at', 'desc');
                    break;
            }
        };
    }

    private function workshopQueryCallback(Request $request): \Closure
    {
        return function (EloquentBuilder $builder) use ($request) {
            $builder->select([
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
                    'reviews_count',
                ])
                ->where('is_active', true);

            if ($request->filled('workshop_type')) {
                if ($request->workshop_type === 'online') {
                    $builder->online();
                } elseif ($request->workshop_type === 'offline') {
                    $builder->offline();
                }
            }

            if ($request->filled('workshop_level')) {
                $builder->byLevel($request->workshop_level);
            }

            if ($request->filled('workshop_category')) {
                $builder->byCategory($request->workshop_category);
            }

            if ($request->filled('price_range')) {
                switch ($request->price_range) {
                    case '0-100':
                        $builder->where('price', '<=', 100);
                        break;
                    case '100-200':
                        $builder->whereBetween('price', [100, 200]);
                        break;
                    case '200-300':
                        $builder->whereBetween('price', [200, 300]);
                        break;
                    case '300+':
                        $builder->where('price', '>', 300);
                        break;
                }
            }

            $sortBy = $request->get('sort_by');

            switch ($sortBy) {
                case 'price_low':
                    $builder->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $builder->orderBy('price', 'desc');
                    break;
                case 'date_soon':
                    $builder->orderBy('start_date', 'asc');
                    break;
                case 'rating':
                    $builder->orderBy('rating', 'desc');
                    break;
                default:
                    $builder->orderBy('start_date', 'asc');
                    break;
            }
        };
    }

    private function applyRecipeLikeSearch(EloquentBuilder $builder, string $query): void
    {
        $this->applyLikeSearch(
            $builder,
            $query,
            [
                'recipes.title',
                'recipes.author',
                'recipes.description',
            ],
            ['recipes.slug']
        );
    }

    private function applyWorkshopLikeSearch(EloquentBuilder $builder, string $query): void
    {
        $this->applyLikeSearch(
            $builder,
            $query,
            [
                'workshops.title',
                'workshops.instructor',
                'workshops.category',
                'workshops.location',
                'workshops.description',
            ],
            ['workshops.slug']
        );
    }

    private function applyLikeSearch(EloquentBuilder $builder, string $query, array $columns, array $prefixColumns = []): void
    {
        if (empty($columns)) {
            return;
        }

        $escaped = $this->escapeLikeValue($query);

        $firstColumn = array_shift($columns);

        $builder->where(function (EloquentBuilder $likeQuery) use ($firstColumn, $columns, $prefixColumns, $escaped) {
            $likeQuery->where($firstColumn, 'like', "%{$escaped}%");

            foreach ($columns as $column) {
                $likeQuery->orWhere($column, 'like', "%{$escaped}%");
            }

            foreach ($prefixColumns as $column) {
                $likeQuery->orWhere($column, 'like', "{$escaped}%");
            }
        });
    }

    private function escapeLikeValue(string $value): string
    {
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $value
        );
    }
}
