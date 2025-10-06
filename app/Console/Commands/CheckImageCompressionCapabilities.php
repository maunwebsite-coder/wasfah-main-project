<?php

namespace App\Console\Commands;

use App\Services\ImageCompressionService;
use Illuminate\Console\Command;

class CheckImageCompressionCapabilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:check-capabilities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'التحقق من إمكانيات ضغط الصور في النظام';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== فحص إمكانيات ضغط الصور ===');
        $this->newLine();

        $capabilities = ImageCompressionService::checkSystemCapabilities();

        // فحص PHP GD Extension
        $this->info('1. PHP GD Extension:');
        if ($capabilities['gd_loaded']) {
            $this->line('   ✅ PHP GD Extension مفعل');
            $this->line("   📋 الإصدار: {$capabilities['gd_version']}");
        } else {
            $this->error('   ❌ PHP GD Extension غير مفعل');
            $this->warn('   💡 للحصول على ضغط الصور، يجب تفعيل PHP GD Extension');
        }
        $this->newLine();

        // فحص أنواع الصور المدعومة
        $this->info('2. أنواع الصور المدعومة:');
        foreach ($capabilities['supported_formats'] as $format => $supported) {
            $status = $supported ? '✅' : '❌';
            $this->line("   $status $format");
        }
        $this->newLine();

        // فحص إعدادات النظام
        $this->info('3. إعدادات النظام:');
        $this->line("   📊 حد الذاكرة: {$capabilities['memory_limit']}");
        $this->line("   ⏱️  حد وقت التنفيذ: {$capabilities['max_execution_time']} ثانية");
        $this->newLine();

        // التوصيات
        $this->info('4. التوصيات:');
        
        if (!$capabilities['gd_loaded']) {
            $this->warn('   ⚠️  يجب تفعيل PHP GD Extension للحصول على ضغط الصور');
            $this->line('   💡 يمكنك تفعيله عبر إضافة extension=gd إلى ملف php.ini');
        } else {
            $this->line('   ✅ النظام جاهز لضغط الصور');
        }

        if (intval($capabilities['memory_limit']) < 256) {
            $this->warn('   ⚠️  حد الذاكرة منخفض، يُنصح بزيادته إلى 256M على الأقل');
        }

        if (intval($capabilities['max_execution_time']) < 30) {
            $this->warn('   ⚠️  حد وقت التنفيذ منخفض، يُنصح بزيادته إلى 30 ثانية على الأقل');
        }

        $this->newLine();
        $this->info('=== انتهاء الفحص ===');

        // إرجاع كود الخروج
        return $capabilities['gd_loaded'] ? 0 : 1;
    }
}