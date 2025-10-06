<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Models\Workshop;
use App\Models\Tool;
use App\Services\ImageCompressionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CompressExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:compress {--type=all : Type of images to compress (recipes, workshops, tools, all)} {--force : Force compression even if compressed version exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ضغط الصور الموجودة في قاعدة البيانات';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $force = $this->option('force');

        $this->info('بدء عملية ضغط الصور...');

        if ($type === 'all' || $type === 'recipes') {
            $this->compressRecipes($force);
        }

        if ($type === 'all' || $type === 'workshops') {
            $this->compressWorkshops($force);
        }

        if ($type === 'all' || $type === 'tools') {
            $this->compressTools($force);
        }

        $this->info('تم الانتهاء من ضغط الصور بنجاح!');
    }

    /**
     * ضغط صور الوصفات
     */
    private function compressRecipes($force = false)
    {
        $this->info('ضغط صور الوصفات...');
        
        $recipes = Recipe::whereNotNull('image')->get();
        $bar = $this->output->createProgressBar($recipes->count());
        $bar->start();

        $compressed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($recipes as $recipe) {
            try {
                if (!$force && $this->isAlreadyCompressed($recipe->image)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                if (Storage::disk('public')->exists($recipe->image)) {
                    // قراءة الصورة الأصلية
                    $imagePath = Storage::disk('public')->path($recipe->image);
                    $imageData = file_get_contents($imagePath);
                    
                    // إنشاء ملف مؤقت
                    $tempFile = tempnam(sys_get_temp_dir(), 'recipe_');
                    file_put_contents($tempFile, $imageData);
                    
                    // إنشاء UploadedFile وهمي
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempFile,
                        basename($recipe->image),
                        mime_content_type($tempFile),
                        null,
                        true
                    );

                    // ضغط الصورة
                    $compressedPath = ImageCompressionService::compressAndStore(
                        $uploadedFile,
                        'recipes',
                        80,
                        1200,
                        1200
                    );

                    if ($compressedPath) {
                        // حذف الصورة الأصلية
                        Storage::disk('public')->delete($recipe->image);
                        
                        // تحديث مسار الصورة في قاعدة البيانات
                        $recipe->update(['image' => $compressedPath]);
                        $compressed++;
                    }

                    // حذف الملف المؤقت
                    unlink($tempFile);
                }

            } catch (\Exception $e) {
                $this->error("خطأ في ضغط صورة الوصفة {$recipe->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("تم ضغط {$compressed} صورة وصفة، تم تخطي {$skipped}، أخطاء {$errors}");
    }

    /**
     * ضغط صور الورشات
     */
    private function compressWorkshops($force = false)
    {
        $this->info('ضغط صور الورشات...');
        
        $workshops = Workshop::whereNotNull('image')->get();
        $bar = $this->output->createProgressBar($workshops->count());
        $bar->start();

        $compressed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($workshops as $workshop) {
            try {
                if (!$force && $this->isAlreadyCompressed($workshop->image)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                if (Storage::disk('public')->exists($workshop->image)) {
                    // قراءة الصورة الأصلية
                    $imagePath = Storage::disk('public')->path($workshop->image);
                    $imageData = file_get_contents($imagePath);
                    
                    // إنشاء ملف مؤقت
                    $tempFile = tempnam(sys_get_temp_dir(), 'workshop_');
                    file_put_contents($tempFile, $imageData);
                    
                    // إنشاء UploadedFile وهمي
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempFile,
                        basename($workshop->image),
                        mime_content_type($tempFile),
                        null,
                        true
                    );

                    // ضغط الصورة
                    $compressedPath = ImageCompressionService::compressAndStore(
                        $uploadedFile,
                        'workshops',
                        80,
                        1200,
                        1200
                    );

                    if ($compressedPath) {
                        // حذف الصورة الأصلية
                        Storage::disk('public')->delete($workshop->image);
                        
                        // تحديث مسار الصورة في قاعدة البيانات
                        $workshop->update(['image' => $compressedPath]);
                        $compressed++;
                    }

                    // حذف الملف المؤقت
                    unlink($tempFile);
                }

            } catch (\Exception $e) {
                $this->error("خطأ في ضغط صورة الورشة {$workshop->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("تم ضغط {$compressed} صورة ورشة، تم تخطي {$skipped}، أخطاء {$errors}");
    }

    /**
     * ضغط صور الأدوات
     */
    private function compressTools($force = false)
    {
        $this->info('ضغط صور الأدوات...');
        
        $tools = Tool::whereNotNull('image')->get();
        $bar = $this->output->createProgressBar($tools->count());
        $bar->start();

        $compressed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($tools as $tool) {
            try {
                if (!$force && $this->isAlreadyCompressed($tool->image)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                if (Storage::disk('public')->exists($tool->image)) {
                    // قراءة الصورة الأصلية
                    $imagePath = Storage::disk('public')->path($tool->image);
                    $imageData = file_get_contents($imagePath);
                    
                    // إنشاء ملف مؤقت
                    $tempFile = tempnam(sys_get_temp_dir(), 'tool_');
                    file_put_contents($tempFile, $imageData);
                    
                    // إنشاء UploadedFile وهمي
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempFile,
                        basename($tool->image),
                        mime_content_type($tempFile),
                        null,
                        true
                    );

                    // ضغط الصورة
                    $compressedPath = ImageCompressionService::compressAndStore(
                        $uploadedFile,
                        'tools',
                        80,
                        800,
                        800
                    );

                    if ($compressedPath) {
                        // حذف الصورة الأصلية
                        Storage::disk('public')->delete($tool->image);
                        
                        // تحديث مسار الصورة في قاعدة البيانات
                        $tool->update(['image' => $compressedPath]);
                        $compressed++;
                    }

                    // حذف الملف المؤقت
                    unlink($tempFile);
                }

            } catch (\Exception $e) {
                $this->error("خطأ في ضغط صورة الأداة {$tool->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("تم ضغط {$compressed} صورة أداة، تم تخطي {$skipped}، أخطاء {$errors}");
    }

    /**
     * التحقق من أن الصورة مضغوطة بالفعل
     */
    private function isAlreadyCompressed($imagePath)
    {
        // يمكن إضافة منطق للتحقق من أن الصورة مضغوطة بالفعل
        // مثلاً التحقق من حجم الملف أو وجود علامة معينة
        return false;
    }
}