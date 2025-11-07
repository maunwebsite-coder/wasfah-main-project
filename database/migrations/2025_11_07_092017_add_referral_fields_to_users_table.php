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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_referral_partner')
                ->default(false)
                ->after('is_admin');

            $table->string('referral_code', 40)
                ->nullable()
                ->unique()
                ->after('is_referral_partner');

            $table->foreignId('referrer_id')
                ->nullable()
                ->after('referral_code')
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('referral_commission_rate', 5, 2)
                ->default(5.00)
                ->after('referrer_id');

            $table->timestamp('referral_partner_since_at')
                ->nullable()
                ->after('referral_commission_rate');

            $table->text('referral_admin_notes')
                ->nullable()
                ->after('referral_partner_since_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_referral_partner',
                'referral_code',
                'referral_commission_rate',
                'referral_partner_since_at',
                'referral_admin_notes',
            ]);

            $table->dropConstrainedForeignId('referrer_id');
        });
    }
};
