<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkshopBooking;
use App\Models\Workshop;
use App\Models\User;
use Carbon\Carbon;

class WorkshopBookingSeeder extends Seeder
{
    public function run()
    {
        // الحصول على الورشات والمستخدمين
        $workshops = Workshop::all();
        $users = User::all();

        if ($workshops->isEmpty() || $users->isEmpty()) {
            $this->command->info('لا توجد ورشات أو مستخدمين لإضافة حجوزات');
            return;
        }

        // إنشاء حجوزات متنوعة
        $statuses = ['pending', 'confirmed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'refunded'];
        $paymentMethods = ['cash', 'bank_transfer', 'credit_card', 'paypal'];

        for ($i = 0; $i < 25; $i++) {
            $workshop = $workshops->random();
            $user = $users->random();
            
            // تجنب الحجز المكرر لنفس الورشة والمستخدم
            $existingBooking = WorkshopBooking::where('workshop_id', $workshop->id)
                                             ->where('user_id', $user->id)
                                             ->first();
            
            if ($existingBooking) {
                continue;
            }

            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $bookingDate = Carbon::now()->subDays(rand(1, 30));

            $booking = WorkshopBooking::create([
                'workshop_id' => $workshop->id,
                'user_id' => $user->id,
                'status' => $status,
                'booking_date' => $bookingDate,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentStatus === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null,
                'payment_amount' => $workshop->price,
                'notes' => $this->getRandomNotes(),
                'confirmed_at' => $status === 'confirmed' ? $bookingDate->addHours(rand(1, 24)) : null,
                'cancelled_at' => $status === 'cancelled' ? $bookingDate->addDays(rand(1, 7)) : null,
                'cancellation_reason' => $status === 'cancelled' ? $this->getRandomCancellationReason() : null,
            ]);

            // تحديث عدد الحجوزات في الورشة
            $workshop->increment('bookings_count');
        }

        $this->command->info('تم إنشاء ' . WorkshopBooking::count() . ' حجز بنجاح');
    }

    private function getRandomNotes()
    {
        $notes = [
            'أرغب في تعلم المزيد عن هذا الموضوع',
            'هذا أول حجز لي في الموقع',
            'سمعت عن الورشة من صديق',
            'أتمنى أن تكون الورشة مفيدة',
            'لدي بعض الأسئلة حول المحتوى',
            'أرغب في تطوير مهاراتي',
            'شكراً لكم على هذه الفرصة',
            null, // بعض الحجوزات بدون ملاحظات
            null,
            null,
        ];

        return $notes[array_rand($notes)];
    }

    private function getRandomCancellationReason()
    {
        $reasons = [
            'تغيير في المواعيد الشخصية',
            'وجدت ورشة أخرى مناسبة أكثر',
            'مشكلة في الدفع',
            'تغيير في الأولويات',
            'ظروف طارئة',
            'لم تعد الورشة مناسبة لي',
        ];

        return $reasons[array_rand($reasons)];
    }
}
