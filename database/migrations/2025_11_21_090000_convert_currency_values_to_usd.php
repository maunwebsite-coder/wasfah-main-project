<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('workshops')
            ->where(function ($query) {
                $query->whereNull('currency')
                    ->orWhere('currency', '!=', 'USD');
            })
            ->update(['currency' => 'USD']);

        if (Schema::hasColumn('users', 'referral_commission_currency')) {
            DB::table('users')
                ->where(function ($query) {
                    $query->whereNull('referral_commission_currency')
                        ->orWhere('referral_commission_currency', '!=', 'USD');
                })
                ->update(['referral_commission_currency' => 'USD']);
        }

        if (Schema::hasTable('referral_commissions')) {
            DB::table('referral_commissions')
                ->where(function ($query) {
                    $query->whereNull('currency')
                        ->orWhere('currency', '!=', 'USD');
                })
                ->update(['currency' => 'USD']);
        }

        if (Schema::hasTable('booking_revenue_shares')) {
            DB::table('booking_revenue_shares')
                ->where(function ($query) {
                    $query->whereNull('currency')
                        ->orWhere('currency', '!=', 'USD');
                })
                ->update(['currency' => 'USD']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Currency migration is irreversible by design.
    }
};
