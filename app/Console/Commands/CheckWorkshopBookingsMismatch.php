<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Workshop;
use App\Models\WorkshopBooking;

class CheckWorkshopBookingsMismatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workshops:check-mismatch {--detailed : Show detailed information for each workshop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for mismatches between stored bookings_count and actual booking records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("فحص عدم التطابق في عدد الحجوزات...");
        
        $workshops = Workshop::all();
        $mismatches = [];
        $totalMismatches = 0;
        
        foreach ($workshops as $workshop) {
            $storedCount = $workshop->bookings_count;
            $actualConfirmed = WorkshopBooking::where('workshop_id', $workshop->id)
                ->where('status', 'confirmed')
                ->count();
            $actualPending = WorkshopBooking::where('workshop_id', $workshop->id)
                ->where('status', 'pending')
                ->count();
            $actualCancelled = WorkshopBooking::where('workshop_id', $workshop->id)
                ->where('status', 'cancelled')
                ->count();
            $actualTotal = WorkshopBooking::where('workshop_id', $workshop->id)->count();
            
            $hasMismatch = false;
            $mismatchDetails = [];
            
            // فحص التطابق مع الحجوزات المؤكدة (المستخدمة في HomeController)
            if ($storedCount !== $actualConfirmed) {
                $hasMismatch = true;
                $mismatchDetails[] = "مؤكدة: مخزن={$storedCount}, فعلي={$actualConfirmed}";
            }
            
            if ($hasMismatch) {
                $mismatches[] = [
                    'id' => $workshop->id,
                    'title' => $workshop->title,
                    'stored_count' => $storedCount,
                    'actual_confirmed' => $actualConfirmed,
                    'actual_pending' => $actualPending,
                    'actual_cancelled' => $actualCancelled,
                    'actual_total' => $actualTotal,
                    'details' => $mismatchDetails
                ];
                $totalMismatches++;
            }
        }
        
        // عرض النتائج
        $this->newLine();
        $this->info("نتائج الفحص:");
        $this->info("عدد الورشات: " . $workshops->count());
        $this->info("عدد الورشات التي بها عدم تطابق: " . $totalMismatches);
        
        if ($totalMismatches > 0) {
            $this->newLine();
            $this->warn("ورشات بها عدم تطابق:");
            
            if ($this->option('detailed')) {
                $this->table(
                    ['ID', 'العنوان', 'مخزن', 'مؤكد', 'معلق', 'ملغي', 'المجموع'],
                    collect($mismatches)->map(function ($mismatch) {
                        return [
                            $mismatch['id'],
                            substr($mismatch['title'], 0, 30) . '...',
                            $mismatch['stored_count'],
                            $mismatch['actual_confirmed'],
                            $mismatch['actual_pending'],
                            $mismatch['actual_cancelled'],
                            $mismatch['actual_total']
                        ];
                    })
                );
                
                $this->newLine();
                $this->info("تفاصيل عدم التطابق:");
                foreach ($mismatches as $mismatch) {
                    $this->line("الورشة #{$mismatch['id']} ({$mismatch['title']}):");
                    foreach ($mismatch['details'] as $detail) {
                        $this->line("  - {$detail}");
                    }
                    $this->newLine();
                }
            } else {
                $this->table(
                    ['ID', 'العنوان', 'مخزن', 'مؤكد', 'الفرق'],
                    collect($mismatches)->map(function ($mismatch) {
                        $diff = $mismatch['actual_confirmed'] - $mismatch['stored_count'];
                        return [
                            $mismatch['id'],
                            substr($mismatch['title'], 0, 40) . '...',
                            $mismatch['stored_count'],
                            $mismatch['actual_confirmed'],
                            $diff > 0 ? "+{$diff}" : $diff
                        ];
                    })
                );
            }
            
            $this->newLine();
            $this->info("لإصلاح عدم التطابق، استخدم الأمر:");
            $this->line("php artisan workshops:sync-bookings-count");
        } else {
            $this->info("✅ لا توجد عدم تطابق في عدد الحجوزات!");
        }
        
        // إحصائيات عامة
        $this->newLine();
        $this->info("إحصائيات عامة للحجوزات:");
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
}