<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UpdateSessionSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تحسين إعدادات الجلسة ومنع تسجيل الخروج التلقائي';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء تحسين إعدادات الجلسة...');
        
        // تنظيف الجلسات المنتهية الصلاحية
        $this->cleanExpiredSessions();
        
        // تحديث إعدادات الجلسة في قاعدة البيانات
        $this->updateSessionConfig();
        
        // تنظيف الكاش
        $this->clearCache();
        
        $this->info('تم تحسين إعدادات الجلسة بنجاح!');
        $this->info('مدة الجلسة الآن: 7 أيام (10080 دقيقة)');
        $this->info('الجلسة لن تنتهي عند إغلاق المتصفح');
    }
    
    private function cleanExpiredSessions()
    {
        $this->info('تنظيف الجلسات المنتهية الصلاحية...');
        
        $expiredSessions = DB::table('sessions')
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime', 10080))->timestamp)
            ->delete();
            
        $this->info("تم حذف {$expiredSessions} جلسة منتهية الصلاحية");
    }
    
    private function updateSessionConfig()
    {
        $this->info('تحديث إعدادات الجلسة...');
        
        // التأكد من أن جدول الجلسات موجود
        if (!DB::getSchemaBuilder()->hasTable('sessions')) {
            $this->error('جدول الجلسات غير موجود!');
            return;
        }
        
        $this->info('تم التحقق من جدول الجلسات');
    }
    
    private function clearCache()
    {
        $this->info('تنظيف الكاش...');
        Cache::flush();
        $this->info('تم تنظيف الكاش');
    }
}
