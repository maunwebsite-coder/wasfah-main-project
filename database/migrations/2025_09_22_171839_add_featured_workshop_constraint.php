<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إنشاء trigger للتحقق من أن ورشة واحدة فقط يمكن أن تكون مميزة
        DB::statement('
            CREATE TRIGGER check_featured_workshop_before_insert
            BEFORE INSERT ON workshops
            FOR EACH ROW
            BEGIN
                IF NEW.is_featured = 1 THEN
                    IF (SELECT COUNT(*) FROM workshops WHERE is_featured = 1) > 0 THEN
                        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "يمكن أن تكون ورشة واحدة فقط مميزة في نفس الوقت";
                    END IF;
                END IF;
            END
        ');

        DB::statement('
            CREATE TRIGGER check_featured_workshop_before_update
            BEFORE UPDATE ON workshops
            FOR EACH ROW
            BEGIN
                IF NEW.is_featured = 1 AND OLD.is_featured = 0 THEN
                    IF (SELECT COUNT(*) FROM workshops WHERE is_featured = 1 AND id != NEW.id) > 0 THEN
                        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "يمكن أن تكون ورشة واحدة فقط مميزة في نفس الوقت";
                    END IF;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS check_featured_workshop_before_insert');
        DB::statement('DROP TRIGGER IF EXISTS check_featured_workshop_before_update');
    }
};