<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\Recipe;
use App\Services\ImageCompressionService;
use App\Services\SimpleImageCompressionService;
use App\Services\EnhancedImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * عرض قائمة الورشات
     */
    public function index()
    {
        $workshops = Workshop::withCount(['bookings' => function ($query) {
            $query->where('status', 'confirmed');
        }])
        ->withCount('bookings as total_bookings')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $featuredWorkshop = Workshop::where('is_featured', true)->first();

        $aggregates = Workshop::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_online = 1 THEN 1 ELSE 0 END) as online,
                COALESCE(SUM(bookings_count), 0) as confirmed_bookings
            ')
            ->first();

        $stats = [
            'total' => (int) ($aggregates?->total ?? 0),
            'active' => (int) ($aggregates?->active ?? 0),
            'online' => (int) ($aggregates?->online ?? 0),
            'confirmed_bookings' => (int) ($aggregates?->confirmed_bookings ?? 0),
        ];

        return view('admin.workshops.index', compact('workshops', 'featuredWorkshop', 'stats'));
    }

    /**
     * عرض نموذج إنشاء ورشة جديدة
     */
    public function create()
    {
        $recipes = Recipe::orderBy('title')->get();
        return view('admin.workshops.create', compact('recipes'));
    }

    /**
     * حفظ ورشة جديدة
     */
    public function store(Request $request)
    {
        $request->validate(Workshop::validationRules());

        $workshop = new Workshop();
        $workshop->title = $request->title;
        $workshop->description = $request->description;
        $workshop->instructor = $request->instructor;
        $workshop->start_date = $request->start_date;
        $workshop->end_date = $request->end_date;
        $workshop->price = $request->price;
        $workshop->currency = $request->currency;
        $workshop->max_participants = $request->max_participants;
        $workshop->is_online = $request->boolean('is_online');
        $workshop->is_active = $request->boolean('is_active');
        $workshop->featured_description = $request->featured_description;
        $locationValue = trim((string) ($request->location ?? ''));
        if ($workshop->is_online) {
            $meetingLink = trim((string) ($request->meeting_link ?? ''));
            $workshop->meeting_link = $meetingLink !== '' ? $meetingLink : null;
            $workshop->location = $locationValue !== '' ? $locationValue : 'أونلاين';
        } else {
            $workshop->meeting_link = null;
            $workshop->location = $locationValue;
        }
        
        // الحقول الجديدة
        $workshop->category = $request->category;
        $workshop->level = $request->level;
        $workshop->duration = $request->duration;
        $workshop->registration_deadline = $request->registration_deadline;
        $workshop->address = $request->address ?? '';
        $workshop->content = $request->content ?? '';
        $workshop->what_you_will_learn = $request->what_you_will_learn ?? '';
        $workshop->requirements = $request->requirements ?? '';
        $workshop->materials_needed = $request->materials_needed ?? '';
        $workshop->instructor_bio = $request->instructor_bio ?? '';

        // التعامل مع الورشة المميزة بعد ضبط الحقول الإلزامية
        if ($request->has('is_featured') && $request->is_featured) {
            $workshop->makeFeatured();
        } else {
            $workshop->is_featured = false;
        }
        
        // رفع الصورة مع الضغط
        if ($request->hasFile('image')) {
            $uploadResult = EnhancedImageUploadService::uploadImage(
                $request->file('image'),
                'workshops',
                85, // جودة 85%
                1200, // أقصى عرض
                1200  // أقصى ارتفاع
            );
            
            if ($uploadResult['success']) {
                $workshop->image = $uploadResult['path'];
                \Log::info('Image uploaded successfully for new workshop', [
                    'image_path' => $uploadResult['path'],
                    'compressed' => $uploadResult['compressed'] ?? false,
                    'original_size' => $uploadResult['original_size'] ?? null,
                    'compressed_size' => $uploadResult['compressed_size'] ?? null
                ]);
            } else {
                \Log::error('Failed to upload image for new workshop', [
                    'error' => $uploadResult['error'],
                    'file_name' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize()
                ]);
                return redirect()->back()
                    ->with('error', $uploadResult['error']);
            }
        }

        $workshop->save();

        // ربط الوصفات المختارة بالورشة
        if ($request->has('recipe_ids') && is_array($request->recipe_ids)) {
            $recipeData = [];
            foreach ($request->recipe_ids as $index => $recipeId) {
                $recipeData[$recipeId] = ['order' => $index + 1];
            }
            $workshop->recipes()->sync($recipeData);
        }

        return redirect()->route('admin.workshops.index')
            ->with('success', 'تم إنشاء الورشة بنجاح!');
    }

    /**
     * عرض تفاصيل ورشة
     */
    public function show($id)
    {
        $workshop = Workshop::withCount(['bookings' => function ($query) {
            $query->where('status', 'confirmed');
        }])
        ->withCount('bookings as total_bookings')
        ->findOrFail($id);
        return view('admin.workshops.show', compact('workshop'));
    }

    /**
     * عرض نموذج تعديل ورشة
     */
    public function edit($id)
    {
        $workshop = Workshop::with('recipes')->findOrFail($id);
        $recipes = Recipe::orderBy('title')->get();
        return view('admin.workshops.edit', compact('workshop', 'recipes'));
    }

    /**
     * تحديث ورشة
     */
    public function update(Request $request, $id)
    {
        $workshop = Workshop::findOrFail($id);

        // Log the request data for debugging
        \Log::info('Workshop update request', [
            'workshop_id' => $id,
            'has_image' => $request->hasFile('image'),
            'image_size' => $request->hasFile('image') ? $request->file('image')->getSize() : null,
            'image_mime' => $request->hasFile('image') ? $request->file('image')->getMimeType() : null,
            'all_files' => $request->allFiles(),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
        ]);

        try {
            $request->validate(Workshop::validationRules($id));
            \Log::info('Validation passed for workshop update', ['workshop_id' => $id]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for workshop update', [
                'workshop_id' => $id,
                'errors' => $e->errors()
            ]);
            throw $e;
        }

        $workshop->title = $request->title;
        $workshop->description = $request->description;
        $workshop->instructor = $request->instructor;
        $workshop->start_date = $request->start_date;
        $workshop->end_date = $request->end_date;
        $workshop->price = $request->price;
        $workshop->currency = $request->currency;
        $workshop->max_participants = $request->max_participants;
        $workshop->is_online = $request->boolean('is_online');
        $workshop->is_active = $request->boolean('is_active');
        $workshop->featured_description = $request->featured_description;
        $locationValue = trim((string) ($request->location ?? ''));
        if ($workshop->is_online) {
            $meetingLink = trim((string) ($request->meeting_link ?? ''));
            $workshop->meeting_link = $meetingLink !== '' ? $meetingLink : null;
            $workshop->location = $locationValue !== '' ? $locationValue : 'أونلاين';
        } else {
            $workshop->meeting_link = null;
            $workshop->location = $locationValue;
        }
        
        // الحقول الجديدة
        $workshop->category = $request->category;
        $workshop->level = $request->level;
        $workshop->duration = $request->duration;
        $workshop->registration_deadline = $request->registration_deadline;
        $workshop->address = $request->address ?? '';
        $workshop->content = $request->content ?? '';
        $workshop->what_you_will_learn = $request->what_you_will_learn ?? '';
        $workshop->requirements = $request->requirements ?? '';
        $workshop->materials_needed = $request->materials_needed ?? '';
        $workshop->instructor_bio = $request->instructor_bio ?? '';

        // التعامل مع الورشة المميزة بعد ضبط الحقول الإلزامية
        if ($request->has('is_featured') && $request->is_featured) {
            $workshop->makeFeatured();
        } else {
            $workshop->is_featured = false;
        }
        
        // تحديث الصورة
        if ($request->hasFile('image')) {
            \Log::info('Processing image upload for workshop', [
                'workshop_id' => $id,
                'old_image' => $workshop->image,
                'new_image_size' => $request->file('image')->getSize(),
                'new_image_mime' => $request->file('image')->getMimeType(),
                'file_name' => $request->file('image')->getClientOriginalName(),
            ]);

            // حذف الصورة القديمة
            if ($workshop->image) {
                EnhancedImageUploadService::deleteImage($workshop->image);
            }
            
            $uploadResult = EnhancedImageUploadService::uploadImage(
                $request->file('image'),
                'workshops',
                85, // جودة 85%
                1200, // أقصى عرض
                1200  // أقصى ارتفاع
            );
            
            if ($uploadResult['success']) {
                $workshop->image = $uploadResult['path'];
                \Log::info('Image uploaded successfully for workshop', [
                    'workshop_id' => $id,
                    'image_path' => $uploadResult['path'],
                    'compressed' => $uploadResult['compressed'] ?? false,
                    'original_size' => $uploadResult['original_size'] ?? null,
                    'compressed_size' => $uploadResult['compressed_size'] ?? null
                ]);
            } else {
                \Log::error('Failed to upload image for workshop', [
                    'workshop_id' => $id,
                    'error' => $uploadResult['error'],
                    'file_name' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize()
                ]);
                return redirect()->back()
                    ->with('error', $uploadResult['error']);
            }
        } elseif ($request->has('remove_image') && $request->remove_image) {
            // حذف الصورة الحالية
            if ($workshop->image) {
                EnhancedImageUploadService::deleteImage($workshop->image);
                $workshop->image = null;
                \Log::info('Image removed for workshop', [
                    'workshop_id' => $id
                ]);
            }
        }

        $workshop->save();
        
        \Log::info('Workshop updated successfully', [
            'workshop_id' => $id,
            'new_image' => $workshop->image,
            'title' => $workshop->title
        ]);

        // تحديث الوصفات المختارة للورشة
        if ($request->has('recipe_ids') && is_array($request->recipe_ids)) {
            $recipeData = [];
            foreach ($request->recipe_ids as $index => $recipeId) {
                $recipeData[$recipeId] = ['order' => $index + 1];
            }
            $workshop->recipes()->sync($recipeData);
        } else {
            // إذا لم يتم اختيار أي وصفات، احذف جميع الروابط
            $workshop->recipes()->detach();
        }

        return redirect()->route('admin.workshops.index')
            ->with('success', 'تم تحديث الورشة بنجاح!');
    }

    /**
     * حذف ورشة
     */
    public function destroy($id)
    {
        $workshop = Workshop::findOrFail($id);
        
        // حذف الصورة
        if ($workshop->image) {
            EnhancedImageUploadService::deleteImage($workshop->image);
        }
        
        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('success', 'تم حذف الورشة بنجاح!');
    }

    /**
     * تفعيل/إلغاء تفعيل ورشة
     */
    public function toggleStatus($id)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->is_active = !$workshop->is_active;
        $workshop->save();

        $status = $workshop->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';
        return redirect()->back()
            ->with('success', $status . ' الورشة بنجاح!');
    }

    /**
     * جعل ورشة هي الورشة القادمة (المميزة)
     */
    public function toggleFeatured($id)
    {
        $workshop = Workshop::findOrFail($id);
        
        // استخدام الـ method الجديد من الـ model
        $workshop->makeFeatured();

        return redirect()->back()
            ->with('success', 'تم جعل "' . $workshop->title . '" هي الورشة القادمة بنجاح!');
    }

    /**
     * التحقق من وجود ورشة مميزة
     */
    public function checkFeatured(Request $request)
    {
        $excludeId = $request->get('exclude');
        
        $query = Workshop::where('is_featured', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $hasFeatured = $query->exists();
        
        return response()->json([
            'hasFeatured' => $hasFeatured
        ]);
    }
}
