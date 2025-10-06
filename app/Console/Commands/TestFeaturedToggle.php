<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Workshop;

class TestFeaturedToggle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workshops:test-featured {workshop_id : ID of the workshop to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the featured workshop toggle functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $workshopId = $this->argument('workshop_id');
        $workshop = Workshop::findOrFail($workshopId);
        
        $this->info("اختبار تبديل الورشة المميزة");
        $this->info("الورشة: {$workshop->title}");
        $this->info("الحالة الحالية: " . ($workshop->is_featured ? 'مميزة' : 'غير مميزة'));
        
        // عرض جميع الورشات المميزة حالياً
        $featuredWorkshops = Workshop::where('is_featured', true)->get();
        $this->info("الورشات المميزة حالياً: " . $featuredWorkshops->count());
        
        foreach ($featuredWorkshops as $featured) {
            $this->line("  - {$featured->title} (ID: {$featured->id})");
        }
        
        // اختبار makeFeatured
        $this->newLine();
        $this->info("تطبيق makeFeatured()...");
        
        try {
            $workshop->makeFeatured();
            $this->info("✅ تم تطبيق makeFeatured() بنجاح");
            
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
            $this->error("❌ خطأ في تطبيق makeFeatured(): " . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}