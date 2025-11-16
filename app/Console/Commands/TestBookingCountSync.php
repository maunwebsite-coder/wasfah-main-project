<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\User;

class TestBookingCountSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workshops:test-sync {workshop_id : ID of the workshop to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the automatic booking count synchronization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $workshopId = $this->argument('workshop_id');
        $workshop = Workshop::findOrFail($workshopId);
        
        $this->info("اختبار مزامنة عدد الحجوزات للورشة: {$workshop->title}");
        $this->info("العدد الحالي: {$workshop->bookings_count}");
        
        // عرض الحجوزات الحالية
        $confirmedBookings = WorkshopBooking::where('workshop_id', $workshopId)
            ->where('status', 'confirmed')
            ->count();
        $pendingBookings = WorkshopBooking::where('workshop_id', $workshopId)
            ->where('status', 'pending')
            ->count();
        $totalBookings = WorkshopBooking::where('workshop_id', $workshopId)->count();
        
        $this->info("الحجوزات المؤكدة: {$confirmedBookings}");
        $this->info("الحجوزات المعلقة: {$pendingBookings}");
        $this->info("إجمالي الحجوزات: {$totalBookings}");
        
        if ($workshop->bookings_count !== $confirmedBookings) {
            $this->error("❌ عدم تطابق! العدد المخزن: {$workshop->bookings_count}, المؤكد: {$confirmedBookings}");
            return Command::FAILURE;
        } else {
            $this->info("✅ العدد متطابق!");
        }
        
        // اختبار إنشاء حجز جديد
        $this->newLine();
        $this->info("اختبار إنشاء حجز جديد...");
        
        $user = User::first();
        if (!$user) {
            $this->error("لا يوجد مستخدمين في النظام");
            return Command::FAILURE;
        }
        
        $oldCount = $workshop->bookings_count;
        
        // إنشاء حجز معلق
        $booking = WorkshopBooking::create([
            'workshop_id' => $workshopId,
            'user_id' => $user->id,
            'status' => 'pending',
            'booking_date' => now(),
            'payment_status' => 'pending',
            'payment_amount' => $workshop->price,
            'payment_currency' => $workshop->currency,
            'notes' => 'اختبار مزامنة العدد',
        ]);
        
        $workshop->refresh();
        $this->info("بعد إنشاء حجز معلق:");
        $this->info("  - العدد المخزن: {$workshop->bookings_count} (لم يتغير)");
        $this->info("  - الحجوزات المؤكدة: " . WorkshopBooking::where('workshop_id', $workshopId)->where('status', 'confirmed')->count());
        
        // تأكيد الحجز
        $this->info("تأكيد الحجز...");
        $booking->update(['status' => 'confirmed']);
        
        $workshop->refresh();
        $this->info("بعد تأكيد الحجز:");
        $this->info("  - العدد المخزن: {$workshop->bookings_count} (زيادة +1)");
        $this->info("  - الحجوزات المؤكدة: " . WorkshopBooking::where('workshop_id', $workshopId)->where('status', 'confirmed')->count());
        
        if ($workshop->bookings_count === $oldCount + 1) {
            $this->info("✅ تم تحديث العدد تلقائياً!");
        } else {
            $this->error("❌ لم يتم تحديث العدد تلقائياً!");
        }
        
        // إلغاء الحجز
        $this->info("إلغاء الحجز...");
        $booking->update(['status' => 'cancelled']);
        
        $workshop->refresh();
        $this->info("بعد إلغاء الحجز:");
        $this->info("  - العدد المخزن: {$workshop->bookings_count} (عودة للعدد الأصلي)");
        $this->info("  - الحجوزات المؤكدة: " . WorkshopBooking::where('workshop_id', $workshopId)->where('status', 'confirmed')->count());
        
        if ($workshop->bookings_count === $oldCount) {
            $this->info("✅ تم تحديث العدد تلقائياً عند الإلغاء!");
        } else {
            $this->error("❌ لم يتم تحديث العدد تلقائياً عند الإلغاء!");
        }
        
        // حذف الحجز التجريبي
        $booking->delete();
        $this->info("تم حذف الحجز التجريبي");
        
        return Command::SUCCESS;
    }
}
