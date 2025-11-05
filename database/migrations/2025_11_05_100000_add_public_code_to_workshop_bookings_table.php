<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->string('public_code', 16)
                ->after('id')
                ->nullable()
                ->unique();
        });

        DB::table('workshop_bookings')
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    $code = $this->generateUniqueCode();

                    DB::table('workshop_bookings')
                        ->where('id', $booking->id)
                        ->update(['public_code' => $code]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_bookings', function (Blueprint $table) {
            $table->dropUnique('workshop_bookings_public_code_unique');
            $table->dropColumn('public_code');
        });
    }

    private function generateUniqueCode(int $length = 10): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $code = '';

            for ($i = 0; $i < $length; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }

            $exists = DB::table('workshop_bookings')
                ->where('public_code', $code)
                ->exists();
        } while ($exists);

        return $code;
    }
};
