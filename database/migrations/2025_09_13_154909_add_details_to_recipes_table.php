<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// In the new migration file (e.g., xxxx_xx_xx_xxxxxx_add_details_to_recipes_table.php)
public function up(): void
{
    Schema::table('recipes', function (Blueprint $table) {
        $table->string('prep_time')->nullable()->after('video_url');
        $table->string('cook_time')->nullable()->after('prep_time');
        $table->string('servings')->nullable()->after('cook_time');
        $table->string('difficulty')->nullable()->after('servings');
        $table->json('steps')->nullable()->after('difficulty'); // To store steps as a JSON array
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            //
        });
    }
};
