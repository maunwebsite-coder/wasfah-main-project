<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The default categories that should always be available.
     */
    private array $defaultCategories = [
        'حلويات',
        'سلطات',
        'سناكات صحية',
        'أطباق رئيسية',
        'شوربات',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $existingNames = DB::table('categories')
            ->whereIn('name', $this->defaultCategories)
            ->pluck('name')
            ->all();

        $now = Carbon::now();
        $inserts = [];

        foreach ($this->defaultCategories as $name) {
            if (! in_array($name, $existingNames, true)) {
                $inserts[] = [
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($inserts)) {
            DB::table('categories')->insert($inserts);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')
            ->whereIn('name', $this->defaultCategories)
            ->delete();
    }
};
