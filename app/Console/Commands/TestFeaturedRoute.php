<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Workshop;
use App\Http\Controllers\Admin\WorkshopController;
use Illuminate\Http\Request;

class TestFeaturedRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workshops:test-route {workshop_id : ID of the workshop to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the featured workshop route functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $workshopId = $this->argument('workshop_id');
        $workshop = Workshop::findOrFail($workshopId);
        
        $this->info("اختبار route الورشة المميزة");
        $this->info("الورشة: {$workshop->title}");
        $this->info("الحالة الحالية: " . ($workshop->is_featured ? 'مميزة' : 'غير مميزة'));
        
        // إنشاء controller instance
        $controller = new WorkshopController();
        
        try {
            // استدعاء toggleFeatured method
            $this->info("استدعاء toggleFeatured method...");
            $response = $controller->toggleFeatured($workshopId);
            
            $this->info("✅ تم استدعاء toggleFeatured بنجاح");
            
            // التحقق من النتيجة
            $workshop->refresh();
            $this->info("الحالة الجديدة: " . ($workshop->is_featured ? 'مميزة' : 'غير مميزة'));
            
            // التحقق من أن الورشات الأخرى لم تعد مميزة
            $otherFeatured = Workshop::where('is_featured', true)
                ->where('id', '!=', $workshopId)
                ->count();
            
            if ($otherFeatured === 0) {
                $this->info("✅ تم إلغاء تمييز الورشات الأخرى");
            } else {
                $this->error("❌ لا تزال هناك ورشات أخرى مميزة: {$otherFeatured}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ خطأ في استدعاء toggleFeatured: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}