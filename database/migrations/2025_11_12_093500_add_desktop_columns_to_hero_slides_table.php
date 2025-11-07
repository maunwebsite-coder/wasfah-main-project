<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hero_slides')) {
            if (!Schema::hasColumn('hero_slides', 'desktop_image_path')) {
                Schema::table('hero_slides', function (Blueprint $table) {
                    $table->string('desktop_image_path')->nullable()->after('image_alt');
                });
            }

            if (!Schema::hasColumn('hero_slides', 'mobile_image_path')) {
                Schema::table('hero_slides', function (Blueprint $table) {
                    $table->string('mobile_image_path')->nullable()->after('desktop_image_path');
                });
            }

            if (Schema::hasColumn('hero_slides', 'image_path')) {
                DB::table('hero_slides')
                    ->whereNull('desktop_image_path')
                    ->update([
                        'desktop_image_path' => DB::raw('image_path'),
                    ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hero_slides')) {
            Schema::table('hero_slides', function (Blueprint $table) {
                if (Schema::hasColumn('hero_slides', 'mobile_image_path')) {
                    $table->dropColumn('mobile_image_path');
                }

                if (Schema::hasColumn('hero_slides', 'desktop_image_path')) {
                    $table->dropColumn('desktop_image_path');
                }
            });
        }
    }
};
