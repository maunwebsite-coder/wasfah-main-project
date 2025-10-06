<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Workshop;
use App\Models\WorkshopBooking;

class SyncWorkshopBookingsCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workshops:sync-bookings-count {--status=confirmed : Filter by booking status (confirmed, pending, cancelled, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize workshops bookings_count with actual booking records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->option('status');
        
        $this->info("بدء مزامنة عدد الحجوزات للورشات...");
        $this->info("حالة الحجوزات المطلوبة: " . ($status === 'all' ? 'جميع الحجوزات' : $status));
        
        $workshops = Workshop::all();
        $updatedCount = 0;
        $errors = [];
        
        $progressBar = $this->output->createProgressBar($workshops->count());
        $progressBar->start();
        
        foreach ($workshops as $workshop) {
            try {
                // حساب عدد الحجوزات الفعلي
                $actualCount = $this->getActualBookingsCount($workshop->id, $status);
                
                // تحديث bookings_count إذا كان مختلفاً
                if ($workshop->bookings_count !== $actualCount) {
                    $oldCount = $workshop->bookings_count;
                    $workshop->update(['bookings_count' => $actualCount]);
                    
                    $this->line("\nتم تحديث الورشة #{$workshop->id} ({$workshop->title}):");
                    $this->line("  - العدد القديم: {$oldCount}");
                    $this->line("  - العدد الجديد: {$actualCount}");
                    
                    $updatedCount++;
                }
                
            } catch (\Exception $e) {
                $errors[] = "خطأ في الورشة #{$workshop->id}: " . $e->getMessage();
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // عرض النتائج
        $this->info("تم الانتهاء من المزامنة!");
        $this->info("عدد الورشات المحدثة: {$updatedCount}");
        
        if (!empty($errors)) {
            $this->error("الأخطاء:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }
        
        // عرض إحصائيات إضافية
        $this->newLine();
        $this->info("إحصائيات الحجوزات:");
        $this->table(
            ['الحالة', 'العدد'],
            [
                ['مؤكدة', WorkshopBooking::where('status', 'confirmed')->count()],
                ['معلقة', WorkshopBooking::where('status', 'pending')->count()],
                ['ملغية', WorkshopBooking::where('status', 'cancelled')->count()],
                ['مكتملة', WorkshopBooking::where('status', 'completed')->count()],
                ['المجموع', WorkshopBooking::count()],
            ]
        );
        
        return Command::SUCCESS;
    }
    
    /**
     * حساب عدد الحجوزات الفعلي للورشة
     */
    private function getActualBookingsCount($workshopId, $status = 'confirmed')
    {
        $query = WorkshopBooking::where('workshop_id', $workshopId);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        return $query->count();
    }
}