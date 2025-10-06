<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Tool;
use App\Services\ImageCompressionService;
use App\Services\SimpleImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    /**
     * تنظيف رابط الصورة من روابط Google و Google Drive
     */
    private function cleanImageUrl($url)
    {
        // إذا كان الرابط من Google Search، استخرج الرابط الأصلي
        if (strpos($url, 'google.com/url') !== false) {
            $parsed = parse_url($url);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $query);
                if (isset($query['url'])) {
                    return urldecode($query['url']);
                }
            }
        }
        
        // إذا كان الرابط من Google Drive، حوله إلى رابط مباشر للصورة
        if (strpos($url, 'drive.google.com/file/d/') !== false) {
            // استخراج معرف الملف من الرابط
            preg_match('/\/file\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
            if (isset($matches[1])) {
                $fileId = $matches[1];
                // استخدام رابط Google Photos الذي يعمل بشكل أفضل
                return "https://lh3.googleusercontent.com/d/" . $fileId;
            }
        }
        
        // إذا كان الرابط من Google Drive مع معاملات إضافية
        if (strpos($url, 'drive.google.com') !== false && strpos($url, 'id=') !== false) {
            $parsed = parse_url($url);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $query);
                if (isset($query['id'])) {
                    return "https://lh3.googleusercontent.com/d/" . $query['id'];
                }
            }
        }
        
        return $url;
    }

    /**
     * الحصول على رابط الصورة
     */
    private function getImageUrl($recipe)
    {
        if ($recipe->image) {
            return Storage::disk('public')->url($recipe->image);
        }
        
        if ($recipe->image_url) {
            return $recipe->image_url;
        }
        
        return null;
    }
    public function index()
    {
        $recipes = Recipe::with(['category', 'ingredients'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.recipes.index', compact('recipes'));
    }

    public function create()
    {
        $categories = Category::all();
        $tools = Tool::where('is_active', true)->orderBy('name')->get();
        return view('admin.recipes.create', compact('categories', 'tools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|exists:categories,category_id',
            'steps' => 'nullable|array',
            'steps.*' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.name' => 'nullable|string',
            'ingredients.*.amount' => 'nullable|string',
            'tools' => 'nullable|array',
            'tools.*' => 'nullable|exists:tools,id',
        ]);

        // الصورة اختيارية - يمكن تركها فارغة

        // معالجة رفع الصور مع الضغط
        $imagePaths = [];
        $imageFields = ['image', 'image_2', 'image_3', 'image_4', 'image_5'];
        
        foreach ($imageFields as $field) {
            $imagePath = null;
            if ($request->hasFile($field)) {
                // محاولة استخدام ضغط الصور المتقدم أولاً
                if (extension_loaded('gd')) {
                    $imagePath = ImageCompressionService::compressAndStore(
                        $request->file($field),
                        'recipes',
                        80, // جودة 80%
                        1200, // أقصى عرض
                        1200  // أقصى ارتفاع
                    );
                } else {
                    // استخدام الحفظ المباشر إذا لم يكن GD متوفراً
                    $imagePath = SimpleImageCompressionService::compressAndStore(
                        $request->file($field),
                        'recipes',
                        80
                    );
                }
            }
            $imagePaths[$field] = $imagePath;
        }

        $recipe = Recipe::create([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'prep_time' => $request->prep_time,
            'cook_time' => $request->cook_time,
            'servings' => $request->servings,
            'difficulty' => $request->difficulty,
            'image_url' => $request->image_url ? $this->cleanImageUrl($request->image_url) : null,
            'image' => $imagePaths['image'],
            'image_2' => $imagePaths['image_2'],
            'image_3' => $imagePaths['image_3'],
            'image_4' => $imagePaths['image_4'],
            'image_5' => $imagePaths['image_5'],
            'category_id' => $request->category_id,
            'steps' => $request->steps,
            'tools' => $request->tools ?? [],
        ]);

        // إضافة المكونات - تصفية المكونات الفارغة
        if ($request->ingredients) {
            foreach ($request->ingredients as $ingredient) {
                // التحقق من أن المكون له اسم صالح
                if (!empty($ingredient['name']) && trim($ingredient['name']) !== '') {
                    Ingredient::create([
                        'recipe_id' => $recipe->recipe_id,
                        'name' => trim($ingredient['name']),
                        'quantity' => $ingredient['amount'] ?? '',
                    ]);
                }
            }
        }

        return redirect()->route('admin.recipes.index')
            ->with('success', 'تم إضافة الوصفة بنجاح!');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['category', 'ingredients']);
        
        // تحويل IDs إلى أسماء للعرض
        if ($recipe->tools && is_array($recipe->tools)) {
            $toolIds = array_filter($recipe->tools, 'is_numeric');
            if (!empty($toolIds)) {
                $toolNames = Tool::whereIn('id', $toolIds)->pluck('name')->toArray();
                $recipe->tools = $toolNames;
            }
        }
        
        // إضافة رابط الصورة
        $recipe->image_url_display = $this->getImageUrl($recipe);
        
        return view('admin.recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        $categories = Category::all();
        $tools = Tool::where('is_active', true)->orderBy('name')->get();
        $recipe->load(['ingredients']);
        
        // تحويل IDs إلى أسماء للعرض
        if ($recipe->tools && is_array($recipe->tools)) {
            $toolIds = array_filter($recipe->tools, 'is_numeric');
            if (!empty($toolIds)) {
                $toolNames = Tool::whereIn('id', $toolIds)->pluck('name')->toArray();
                $recipe->tools = $toolNames;
            }
        }
        
        // إضافة رابط الصورة
        $recipe->image_url_display = $this->getImageUrl($recipe);
        
        return view('admin.recipes.edit', compact('recipe', 'categories', 'tools'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        try {
            // Log the request data for debugging
            \Log::info('Recipe update request', [
                'recipe_id' => $recipe->id,
                'request_data' => $request->all(),
                'method' => $request->method(),
                'headers' => $request->headers->all()
            ]);

            // All fields are now optional
            $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'prep_time' => 'nullable|integer|min:0',
            'cook_time' => 'nullable|integer|min:0',
            'servings' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|exists:categories,category_id',
            'steps' => 'nullable|array',
            'steps.*' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.name' => 'nullable|string',
            'ingredients.*.amount' => 'nullable|string',
            'tools' => 'nullable|array',
            'tools.*' => 'nullable|exists:tools,id',
        ]);

        // الصورة اختيارية - يمكن تركها فارغة

        // معالجة رفع الصورة الجديدة مع الضغط
        // معالجة رفع الصور مع الضغط
        $imagePaths = [];
        $imageFields = ['image', 'image_2', 'image_3', 'image_4', 'image_5'];
        
        foreach ($imageFields as $field) {
            $imagePath = $recipe->$field; // الاحتفاظ بالصورة الحالية
            
            if ($request->hasFile($field)) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($recipe->$field && Storage::disk('public')->exists($recipe->$field)) {
                    if (extension_loaded('gd')) {
                        ImageCompressionService::deleteCompressedImage($recipe->$field);
                    } else {
                        SimpleImageCompressionService::deleteImage($recipe->$field);
                    }
                }
                
                // محاولة استخدام ضغط الصور المتقدم أولاً
                if (extension_loaded('gd')) {
                    $imagePath = ImageCompressionService::compressAndStore(
                        $request->file($field),
                        'recipes',
                        80, // جودة 80%
                        1200, // أقصى عرض
                        1200  // أقصى ارتفاع
                    );
                } else {
                    // استخدام الحفظ المباشر إذا لم يكن GD متوفراً
                    $imagePath = SimpleImageCompressionService::compressAndStore(
                        $request->file($field),
                        'recipes',
                        80
                    );
                }
            }
            $imagePaths[$field] = $imagePath;
        }

        $recipe->update([
            'title' => $request->title,
            'description' => $request->description,
            'author' => $request->author,
            'prep_time' => $request->prep_time,
            'cook_time' => $request->cook_time,
            'servings' => $request->servings,
            'difficulty' => $request->difficulty,
            'image_url' => $request->image_url ? $this->cleanImageUrl($request->image_url) : null,
            'image' => $imagePaths['image'],
            'image_2' => $imagePaths['image_2'],
            'image_3' => $imagePaths['image_3'],
            'image_4' => $imagePaths['image_4'],
            'image_5' => $imagePaths['image_5'],
            'category_id' => $request->category_id,
            'steps' => $request->steps,
            'tools' => $request->tools ?? [],
        ]);

        // حذف المكونات القديمة وإضافة الجديدة - تصفية المكونات الفارغة
        $recipe->ingredients()->delete();
        if ($request->ingredients) {
            foreach ($request->ingredients as $ingredient) {
                // التحقق من أن المكون له اسم صالح
                if (!empty($ingredient['name']) && trim($ingredient['name']) !== '') {
                    Ingredient::create([
                        'recipe_id' => $recipe->recipe_id,
                        'name' => trim($ingredient['name']),
                        'quantity' => $ingredient['amount'] ?? '',
                    ]);
                }
            }
        }

            \Log::info('Recipe updated successfully', [
                'recipe_id' => $recipe->id,
                'title' => $recipe->title
            ]);

            return redirect()->route('admin.recipes.index')
                ->with('success', 'تم تحديث الوصفة بنجاح!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Recipe update validation failed', [
                'recipe_id' => $recipe->id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Recipe update failed', [
                'recipe_id' => $recipe->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث الوصفة: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->ingredients()->delete();
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->with('success', 'تم حذف الوصفة بنجاح!');
    }
}