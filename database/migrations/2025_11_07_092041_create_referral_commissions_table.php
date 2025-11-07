<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_partner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('participant_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->foreignId('workshop_booking_id')->constrained('workshop_bookings')->cascadeOnDelete();

            $table->decimal('booking_amount', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(5.00);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('currency', 10)->nullable();

            $table->string('status', 20)->default('pending'); // pending, ready, paid, cancelled
            $table->timestamp('earned_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'referral_partner_id']);
            $table->unique('workshop_booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_commissions');
    }
};
